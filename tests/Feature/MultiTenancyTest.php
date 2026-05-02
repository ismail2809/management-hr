<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CnssRate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    private Company $companyA;
    private Company $companyB;
    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create(['name' => 'Company A']);
        $this->companyB = Company::create(['name' => 'Company B']);

        $this->userA = User::create([
            'name'       => 'User A',
            'email'      => 'a@test.ma',
            'password'   => bcrypt('password'),
            'company_id' => $this->companyA->id,
        ]);

        $this->userB = User::create([
            'name'       => 'User B',
            'email'      => 'b@test.ma',
            'password'   => bcrypt('password'),
            'company_id' => $this->companyB->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_only_sees_own_company_employees(): void
    {
        $this->actingAs($this->userA);

        Employee::create([
            'company_id' => $this->companyA->id, 'first_name' => 'Alice', 'last_name' => 'A',
            'contract_type' => 'CDI', 'marital_status' => 'celibataire', 'status' => 'actif',
        ]);

        $this->actingAs($this->userB);

        Employee::create([
            'company_id' => $this->companyB->id, 'first_name' => 'Bob', 'last_name' => 'B',
            'contract_type' => 'CDI', 'marital_status' => 'celibataire', 'status' => 'actif',
        ]);

        // UserB ne doit voir que ses propres employés
        $this->actingAs($this->userB);
        $employees = Employee::all();

        $this->assertCount(1, $employees);
        $this->assertEquals('Bob', $employees->first()->first_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_access_other_company_employee_by_id(): void
    {
        $this->actingAs($this->userA);

        $employeeA = Employee::create([
            'company_id' => $this->companyA->id, 'first_name' => 'Alice', 'last_name' => 'A',
            'contract_type' => 'CDI', 'marital_status' => 'celibataire', 'status' => 'actif',
        ]);

        // UserB essaie de trouver l'employé de A par ID
        $this->actingAs($this->userB);
        $found = Employee::find($employeeA->id);

        $this->assertNull($found, 'Le CompanyScope doit empêcher l\'accès inter-company');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function creating_record_automatically_sets_company_id(): void
    {
        $this->actingAs($this->userA);

        $dept = Department::create([
            'name' => 'IT',
        ]);

        $this->assertEquals($this->companyA->id, $dept->company_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_only_sees_own_company_departments(): void
    {
        $this->actingAs($this->userA);
        Department::create(['company_id' => $this->companyA->id, 'name' => 'Dept A']);

        $this->actingAs($this->userB);
        Department::create(['company_id' => $this->companyB->id, 'name' => 'Dept B']);

        $this->actingAs($this->userA);
        $departments = Department::all();

        $this->assertCount(1, $departments);
        $this->assertEquals('Dept A', $departments->first()->name);
    }

    // --- Tests GlobalOrCompanyScope (CnssRate) ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function global_cnss_rates_are_visible_to_all_companies(): void
    {
        // Taux global (company_id = null)
        CnssRate::withoutGlobalScopes()->create([
            'company_id'      => null,
            'type'            => 'employee',
            'label'           => 'CNSS',
            'rate_percentage' => 4.48,
            'plafond'         => 6000,
        ]);

        // UserA doit voir le taux global
        $this->actingAs($this->userA);
        $rates = CnssRate::all();

        $this->assertCount(1, $rates);
        $this->assertNull($rates->first()->company_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_specific_rate_is_visible_only_to_its_company(): void
    {
        // Taux global
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => null, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 4.48, 'plafond' => 6000,
        ]);

        // Taux spécifique à Company A
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => $this->companyA->id, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 5.00, 'plafond' => 6000,
        ]);

        // UserA voit : taux global + son taux = 2
        $this->actingAs($this->userA);
        $this->assertCount(2, CnssRate::all());

        // UserB voit : seulement le taux global = 1
        $this->actingAs($this->userB);
        $this->assertCount(1, CnssRate::all());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function global_scope_bypassed_shows_all_rates(): void
    {
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => null, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 4.48, 'plafond' => 6000,
        ]);
        CnssRate::withoutGlobalScopes()->create([
            'company_id' => $this->companyA->id, 'type' => 'employee', 'label' => 'CNSS',
            'rate_percentage' => 5.00, 'plafond' => 6000,
        ]);

        $this->actingAs($this->userB);

        // withoutGlobalScopes → voit tout (utile pour admin et PayrollCalculator)
        $this->assertCount(2, CnssRate::withoutGlobalScopes()->get());
    }
}
