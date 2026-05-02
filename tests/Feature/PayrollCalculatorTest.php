<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CnssRate;
use App\Models\Employee;
use App\Models\IrBracket;
use App\Models\User;
use App\Services\PayrollCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private PayrollCalculator $calculator;
    private Company $company;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new PayrollCalculator();

        $this->company = Company::create([
            'name' => 'Test Company',
        ]);

        // Authentifier un user de la company pour le CompanyScope
        $user = User::create([
            'name'       => 'Test User',
            'email'      => 'test@test.ma',
            'password'   => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);
        $this->actingAs($user);

        $this->employee = Employee::create([
            'company_id'         => $this->company->id,
            'first_name'         => 'Ahmed',
            'last_name'          => 'Benali',
            'contract_type'      => 'CDI',
            'marital_status'     => 'celibataire',
            'number_of_children' => 0,
            'status'             => 'actif',
        ]);

        $this->seedIrBrackets();
    }

    private function seedIrBrackets(): void
    {
        $brackets = [
            ['min_salary' => 0,      'max_salary' => 30000,   'rate_percentage' => 0,  'deduction_amount' => 0],
            ['min_salary' => 30001,  'max_salary' => 50000,   'rate_percentage' => 10, 'deduction_amount' => 3000],
            ['min_salary' => 50001,  'max_salary' => 60000,   'rate_percentage' => 20, 'deduction_amount' => 8000],
            ['min_salary' => 60001,  'max_salary' => 80000,   'rate_percentage' => 30, 'deduction_amount' => 14000],
            ['min_salary' => 80001,  'max_salary' => 180000,  'rate_percentage' => 34, 'deduction_amount' => 17200],
            ['min_salary' => 180001, 'max_salary' => null,    'rate_percentage' => 38, 'deduction_amount' => 24400],
        ];
        foreach ($brackets as $b) {
            IrBracket::create($b);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_cnss_correctly_below_plafond(): void
    {
        // Brut 5000 < plafond 6000 → CNSS sur 5000
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000);

        $expectedCnss = round(5000 * 4.48 / 100, 2); // 224.00
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caps_cnss_at_plafond(): void
    {
        // Brut 10000 > plafond 6000 → CNSS plafonné à 6000
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 10000);

        $expectedCnss = round(6000 * 4.48 / 100, 2); // 268.80
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_amo_without_plafond(): void
    {
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 10000);

        // AMO sur brut complet (pas de plafond)
        $expectedAmo = round(10000 * 2.26 / 100, 2); // 226.00
        $this->assertEquals($expectedAmo, (float) $payroll->amo_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_employer_contributions(): void
    {
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000);

        $expectedCnssEmployer = round(5000 * 10.77 / 100, 2);
        $expectedAmoEmployer  = round(5000 * 4.11 / 100, 2);

        $this->assertEquals($expectedCnssEmployer, (float) $payroll->total_cnss_employer);
        $this->assertEquals($expectedAmoEmployer, (float) $payroll->amo_employer);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_zero_ir_below_threshold(): void
    {
        // Salaire mensuel 2500 → annuel 30000 → tranche 0%
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 2500);

        $this->assertEquals(0.0, (float) $payroll->ir);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_ir_progressively(): void
    {
        // Brut mensuel 5000 → annuel 60000
        // CNSS = min(5000, 6000) * 4.48% = 224
        // Frais pro = 5000 * 20% = 1000 (< 2500 plafond mensuel)
        // Base IR mensuelle = 5000 - 224 - 1000 = 3776
        // Base IR annuelle = 45312
        // Tranche 30001–50000 : 10% → 45312 * 10% - 3000 = 1531.2/an → 127.6/mois
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000);

        $this->assertGreaterThan(0, (float) $payroll->ir);
        $this->assertLessThan(500, (float) $payroll->ir); // sanity check
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_applies_child_deduction_to_ir(): void
    {
        $employeeWithChildren = Employee::create([
            'company_id'         => $this->company->id,
            'first_name'         => 'Fatima',
            'last_name'          => 'Zahra',
            'contract_type'      => 'CDI',
            'marital_status'     => 'marie',
            'number_of_children' => 3,
            'status'             => 'actif',
        ]);

        $payrollNoChildren  = $this->calculator->calculate($this->employee, 1, 2025, 5000);
        $payrollWithChildren = $this->calculator->calculate($employeeWithChildren, 1, 2025, 5000);

        // 3 enfants × 360 MAD/an / 12 = 90 MAD/mois de déduction IR
        $this->assertLessThan(
            (float) $payrollNoChildren->ir,
            (float) $payrollWithChildren->ir
        );
        $this->assertEqualsWithDelta(
            (float) $payrollNoChildren->ir - 90,
            (float) $payrollWithChildren->ir,
            0.01
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caps_child_deduction_at_six_children(): void
    {
        $employee6 = Employee::create([
            'company_id'         => $this->company->id,
            'first_name'         => 'Hassan',
            'last_name'          => 'Test',
            'contract_type'      => 'CDI',
            'marital_status'     => 'marie',
            'number_of_children' => 6,
            'status'             => 'actif',
        ]);

        $employee8 = Employee::create([
            'company_id'         => $this->company->id,
            'first_name'         => 'Youssef',
            'last_name'          => 'Test',
            'contract_type'      => 'CDI',
            'marital_status'     => 'marie',
            'number_of_children' => 8,
            'status'             => 'actif',
        ]);

        $payroll6 = $this->calculator->calculate($employee6, 1, 2025, 6000);
        $payroll8 = $this->calculator->calculate($employee8, 1, 2025, 6000);

        // Au-delà de 6 enfants, la déduction ne change plus
        $this->assertEquals((float) $payroll6->ir, (float) $payroll8->ir);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_salaire_net_correctly(): void
    {
        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000);

        $expectedNet = 5000
            - (float) $payroll->total_cnss_employee
            - (float) $payroll->amo_employee
            - (float) $payroll->ir;

        $this->assertEqualsWithDelta($expectedNet, (float) $payroll->salaire_net, 0.01);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_taxable_primes_in_gross(): void
    {
        $components = [
            ['type' => 'prime', 'label' => 'Prime performance', 'amount' => 500, 'taxable' => true],
        ];

        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000, $components);

        // Base imposable = 5000 + 500 = 5500
        // CNSS plafonné à 5500 (< 6000)
        $expectedCnss = round(5500 * 4.48 / 100, 2);
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_excludes_nontaxable_primes_from_cnss(): void
    {
        $components = [
            ['type' => 'prime', 'label' => 'Avantage nature', 'amount' => 500, 'taxable' => false],
        ];

        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000, $components);

        // Non-taxable → pas inclus dans brut imposable
        $expectedCnss = round(5000 * 4.48 / 100, 2);
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_persists_payroll_components(): void
    {
        $components = [
            ['type' => 'prime',   'label' => 'Prime ancienneté', 'amount' => 300, 'taxable' => true],
            ['type' => 'retenue', 'label' => 'Avance sur salaire', 'amount' => 200, 'taxable' => false],
        ];

        $payroll = $this->calculator->calculate($this->employee, 1, 2025, 5000, $components);

        $this->assertCount(2, $payroll->components);
        $this->assertEquals('Prime ancienneté', $payroll->components->firstWhere('type', 'prime')->label);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_upserts_payroll_on_recalculate(): void
    {
        $this->calculator->calculate($this->employee, 1, 2025, 5000);
        $this->calculator->calculate($this->employee, 1, 2025, 6000);

        // Doit n'avoir qu'une seule fiche par (company, employee, month, year)
        $this->assertEquals(1, \App\Models\Payroll::withoutGlobalScopes()
            ->where('employee_id', $this->employee->id)
            ->where('month', 1)
            ->where('year', 2025)
            ->count());
    }

    // --- Tests taux depuis la DB ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_global_db_rate_when_available(): void
    {
        // Seed un taux CNSS global différent des constantes (6% au lieu de 4.48%)
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => null, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 6.00, 'plafond' => 6000,
        ]);

        $payroll = $this->calculator->calculate($this->employee, 2, 2025, 5000);

        $expectedCnss = round(5000 * 6.00 / 100, 2);
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_rate_overrides_global_rate(): void
    {
        // Taux global
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => null, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 4.48, 'plafond' => 6000,
        ]);

        // Taux spécifique à la company (override)
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => $this->company->id, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 5.50, 'plafond' => 6000,
        ]);

        $payroll = $this->calculator->calculate($this->employee, 3, 2025, 5000);

        $expectedCnss = round(5000 * 5.50 / 100, 2);
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_falls_back_to_constant_when_no_db_rate(): void
    {
        // Aucun taux en DB → fallback constante 4.48%
        $payroll = $this->calculator->calculate($this->employee, 4, 2025, 5000);

        $expectedCnss = round(5000 * 4.48 / 100, 2);
        $this->assertEquals($expectedCnss, (float) $payroll->total_cnss_employee);
    }
}
