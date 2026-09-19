<?php

namespace Tests\Feature\Leave;

use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cycle de vie d'une demande d'absence / congé.
 */
class LeaveWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function demande(array $attributes = []): Leave
    {
        return Leave::create(array_merge([
            'employee_id' => $this->employee()->id,
            'categorie'   => 'conge',
            'start_date'  => '2026-09-14',
            'end_date'    => '2026-09-18',
            'status'      => 'en_attente',
        ], $attributes));
    }

    #[Test]
    public function une_demande_est_creee_en_attente_par_defaut(): void
    {
        $this->actingAsRole('secretaire');

        $leave = $this->demande();

        $this->assertSame('en_attente', $leave->status);
        $this->assertNull($leave->approved_by);
        $this->assertNull($leave->approved_at);
    }

    #[Test]
    public function lapprobation_horodate_et_trace_lapprobateur(): void
    {
        $user  = $this->actingAsRole('secretaire');
        $leave = $this->demande();

        // Reproduit l'action « Approuver » de LeaveResource.
        $leave->update([
            'status'      => 'approuvé',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $leave->refresh();

        $this->assertSame('approuvé', $leave->status);
        $this->assertSame($user->id, $leave->approved_by);
        $this->assertNotNull($leave->approved_at);
        $this->assertTrue($leave->approver->is($user));
    }

    #[Test]
    public function le_refus_trace_aussi_lauteur(): void
    {
        $user  = $this->actingAsRole('directeur');
        $leave = $this->demande();

        $leave->update([
            'status'      => 'refusé',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->assertSame('refusé', $leave->fresh()->status);
    }

    #[Test]
    public function les_relations_employe_et_type_sont_operationnelles(): void
    {
        $this->actingAsRole('secretaire');

        $type     = LeaveType::create(['name' => 'Congé annuel']);
        $employe  = $this->employee(['first_name' => 'Amina', 'last_name' => 'TAZI']);
        $leave    = $this->demande(['employee_id' => $employe->id, 'leave_type_id' => $type->id]);

        $this->assertSame('Amina TAZI', $leave->employee->full_name);
        $this->assertSame('Congé annuel', $leave->leaveType->name);
    }

    #[Test]
    public function la_suppression_est_douce(): void
    {
        $this->actingAsRole('super-admin', $this->defaultEcole());

        $leave = $this->demande();
        $id    = $leave->id;

        $leave->delete();

        // Attention : withoutGlobalScopes() retire AUSSI le SoftDeletingScope,
        // il ne faut donc pas s'en servir pour vérifier une suppression douce.
        $this->assertNull(Leave::find($id));
        $this->assertNotNull(Leave::withTrashed()->find($id));
        $this->assertDatabaseHas('leaves', ['id' => $id]);
        $this->assertNotNull(Leave::withTrashed()->find($id)->deleted_at);
    }

    #[Test]
    public function un_remplacant_peut_etre_rattache(): void
    {
        $this->actingAsRole('secretaire');

        $titulaire  = $this->employee(['first_name' => 'Titulaire']);
        $remplacant = $this->employee(['first_name' => 'Remplacant']);

        $leave = $this->demande([
            'employee_id'   => $titulaire->id,
            'remplacant_id' => $remplacant->id,
        ]);

        $this->assertSame('Remplacant', $leave->remplacant->first_name);
    }

    /**
     * Le schéma (DATE NOT NULL) garantit que start_date/end_date ne peuvent
     * jamais être nulles. C'est ce qui met ViewEmployee::getLeaveStats() à
     * l'abri du diffInDays() sur null — sa garde manquante reste théorique.
     */
    #[Test]
    public function le_schema_interdit_une_date_de_conge_nulle(): void
    {
        $this->actingAsRole('secretaire');
        $leave = $this->demande();

        $this->expectException(\Illuminate\Database\QueryException::class);

        \DB::table('leaves')->where('id', $leave->id)->update(['start_date' => null]);
    }

    #[Test]
    public function la_categorie_distingue_conge_et_absence(): void
    {
        $this->actingAsRole('secretaire');

        $this->demande(['categorie' => 'conge']);
        $this->demande(['categorie' => 'absence']);

        $this->assertSame(1, Leave::where('categorie', 'conge')->count());
        $this->assertSame(1, Leave::where('categorie', 'absence')->count());
    }
}
