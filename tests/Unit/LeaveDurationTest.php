<?php

namespace Tests\Unit;

use App\Models\Leave;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Accesseur Leave::duration_days — jours OUVRABLES, week-ends exclus,
 * bornes incluses. C'est la référence métier de la durée d'une absence.
 */
class LeaveDurationTest extends TestCase
{
    private function leave(?string $start, ?string $end): Leave
    {
        return new Leave(['start_date' => $start, 'end_date' => $end]);
    }

    public static function periodes(): array
    {
        return [
            'un seul jour ouvrable'        => ['2026-09-17', '2026-09-17', 1],
            'semaine complete lun-ven'     => ['2026-09-14', '2026-09-18', 5],
            'vendredi a lundi (we exclu)'  => ['2026-09-18', '2026-09-21', 2],
            'week-end seul'                => ['2026-09-19', '2026-09-20', 1],
            'deux semaines'                => ['2026-09-14', '2026-09-25', 10],
        ];
    }

    #[Test]
    #[DataProvider('periodes')]
    public function la_duree_compte_les_jours_ouvrables(string $start, string $end, int $attendu): void
    {
        $this->assertSame($attendu, $this->leave($start, $end)->duration_days);
    }

    #[Test]
    public function la_duree_vaut_zero_si_une_date_manque(): void
    {
        $this->assertSame(0, $this->leave(null, '2026-09-18')->duration_days);
        $this->assertSame(0, $this->leave('2026-09-14', null)->duration_days);
        $this->assertSame(0, $this->leave(null, null)->duration_days);
    }

    #[Test]
    public function une_date_illisible_lue_en_base_est_neutralisee(): void
    {
        // Leave::castAttribute() avale les dates non analysables À LA LECTURE.
        // setRawAttributes court-circuite le mutateur, comme une hydratation
        // depuis une ligne SQL corrompue.
        $leave = new Leave();
        $leave->setRawAttributes(['start_date' => 'date-invalide', 'end_date' => '2026-09-18']);

        $this->assertNull($leave->start_date);
        $this->assertSame(0, $leave->duration_days);
    }

    #[Test]
    public function une_date_illisible_AFFECTEE_leve_bien_une_exception(): void
    {
        // La protection ne couvre que la lecture : l'affectation passe par le
        // mutateur de date d'Eloquent, qui lui n'est pas protégé.
        $this->expectException(\Carbon\Exceptions\InvalidFormatException::class);

        (new Leave(['start_date' => 'date-invalide']))->start_date;
    }
}
