<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::create(['name' => 'Test Co']);

        $user = User::create([
            'name' => 'RH', 'email' => 'rh@test.ma',
            'password' => bcrypt('password'), 'company_id' => $company->id,
        ]);
        $this->actingAs($user);

        $this->employee = Employee::create([
            'company_id'     => $company->id,
            'first_name'     => 'Ahmed',
            'last_name'      => 'Test',
            'contract_type'  => 'CDI',
            'marital_status' => 'celibataire',
            'status'         => 'actif',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_worked_hours_from_check_in_and_check_out(): void
    {
        $attendance = Attendance::create([
            'company_id'  => $this->employee->company_id,
            'employee_id' => $this->employee->id,
            'date'        => '2025-01-15',
            'check_in'    => '08:00',
            'check_out'   => '17:00',
        ]);

        $this->assertEquals(9.0, (float) $attendance->worked_hours);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calculates_overtime_above_8_hours(): void
    {
        $attendance = Attendance::create([
            'company_id'  => $this->employee->company_id,
            'employee_id' => $this->employee->id,
            'date'        => '2025-01-15',
            'check_in'    => '08:00',
            'check_out'   => '19:00', // 11h de travail
        ]);

        $this->assertEquals(11.0, (float) $attendance->worked_hours);
        $this->assertEquals(3.0,  (float) $attendance->overtime_hours);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_zero_overtime_when_under_8_hours(): void
    {
        $attendance = Attendance::create([
            'company_id'  => $this->employee->company_id,
            'employee_id' => $this->employee->id,
            'date'        => '2025-01-15',
            'check_in'    => '09:00',
            'check_out'   => '13:00', // 4h
        ]);

        $this->assertEquals(4.0, (float) $attendance->worked_hours);
        $this->assertEquals(0.0, (float) $attendance->overtime_hours);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_night_shift_crossing_midnight(): void
    {
        $attendance = Attendance::create([
            'company_id'  => $this->employee->company_id,
            'employee_id' => $this->employee->id,
            'date'        => '2025-01-15',
            'check_in'    => '22:00',
            'check_out'   => '06:00', // 8h de nuit
        ]);

        $this->assertEquals(8.0, (float) $attendance->worked_hours);
        $this->assertEquals(0.0, (float) $attendance->overtime_hours);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_calculate_when_check_out_is_missing(): void
    {
        $attendance = Attendance::create([
            'company_id'  => $this->employee->company_id,
            'employee_id' => $this->employee->id,
            'date'        => '2025-01-15',
            'check_in'    => '08:00',
            'check_out'   => null,
        ]);

        // Pas de check_out → worked_hours reste à 0 (valeur par défaut)
        $this->assertEquals(0.0, (float) $attendance->worked_hours);
        $this->assertEquals(0.0, (float) $attendance->overtime_hours);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_recalculates_on_update(): void
    {
        $attendance = Attendance::create([
            'company_id'  => $this->employee->company_id,
            'employee_id' => $this->employee->id,
            'date'        => '2025-01-15',
            'check_in'    => '08:00',
            'check_out'   => '16:00', // 8h
        ]);

        $this->assertEquals(8.0, (float) $attendance->worked_hours);

        // Mise à jour du check_out → recalcul
        $attendance->update(['check_out' => '18:00']); // 10h

        $this->assertEquals(10.0, (float) $attendance->fresh()->worked_hours);
        $this->assertEquals(2.0,  (float) $attendance->fresh()->overtime_hours);
    }
}
