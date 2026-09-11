<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Profession;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EmployeeImportService
{
    /** Noms de colonnes reconnus → champ Employee */
    private const COLUMN_MAP = [
        'matricule'            => 'matricule',
        'nom'                  => 'last_name',
        'prénom'               => 'first_name',
        'prenom'               => 'first_name',
        'sexe'                 => 'gender',
        'date naissance'       => 'birth_date',
        'date de naissance'    => 'birth_date',
        'cin'                  => 'cin',
        'cnss'                 => 'cnss_number',
        'n° cnss'              => 'cnss_number',
        'date recrutement'     => 'hire_date',
        "date d'embauche"      => 'hire_date',
        'date embauche'        => 'hire_date',
        'nationalité'          => 'nationality',
        'nationalite'          => 'nationality',
        'adresse'              => 'address',
        'téléphone'            => 'phone',
        'telephone'            => 'phone',
        'téléphone portable'   => 'phone',
        'telephone portable'   => 'phone',
        'mobile'               => 'phone',
        'adresse mail'         => 'email',
        'email'                => 'email',
        'e-mail'               => 'email',
        'mail'                 => 'email',
        'diplôme'              => 'diploma',
        'diplome'              => 'diploma',
        'profession'           => 'profession_name',
        'métier'               => 'profession_name',
        'metier'               => 'profession_name',
        'fonction'             => 'profession_name',
        'poste'                => 'profession_name',
    ];

    public function import(string $filePath, int $companyId): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false);

        // Trouver la ligne d'en-tête (max de cellules non vides)
        $headerRowIndex = $this->findHeaderRow($rows);
        if ($headerRowIndex === null) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => ['Impossible de détecter la ligne d\'en-tête.']];
        }

        $columnMap = $this->buildColumnMap($rows[$headerRowIndex]);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $data = $this->mapRow($row, $columnMap, $sheet, $i);

            // Résoudre profession_name → profession_id
            if (! empty($data['profession_name'])) {
                $profession = Profession::withoutGlobalScopes()
                    ->firstOrCreate(
                        ['ecole_setting_id' => $companyId, 'name' => $data['profession_name']],
                        ['ecole_setting_id' => $companyId, 'name' => $data['profession_name']]
                    );
                $data['profession_id'] = $profession->id;
            }
            unset($data['profession_name']);

            // Ignorer les lignes sans nom ou qui ressemblent à des titres/totaux
            if (empty($data['last_name']) && empty($data['first_name'])) {
                $skipped++;
                continue;
            }

            $nameValue = $data['last_name'] ?? $data['first_name'] ?? '';
            if ($this->isTitleRow($nameValue)) {
                $skipped++;
                continue;
            }

            $data['ecole_setting_id'] = $companyId;
            $data['status']           = $data['status'] ?? 'actif';

            try {
                if (! empty($data['cin'])) {
                    $existing = Employee::withoutGlobalScopes()
                        ->where('ecole_setting_id', $companyId)
                        ->where('cin', $data['cin'])
                        ->first();
                    if ($existing) {
                        $existing->fill($this->onlyEmptyFields($existing, $data))->save();
                    } else {
                        Employee::withoutGlobalScopes()->create($data);
                    }
                } elseif (! empty($data['matricule'])) {
                    $existing = Employee::withoutGlobalScopes()
                        ->where('ecole_setting_id', $companyId)
                        ->where('matricule', $data['matricule'])
                        ->first();
                    if ($existing) {
                        $existing->fill($this->onlyEmptyFields($existing, $data))->save();
                    } else {
                        Employee::withoutGlobalScopes()->create($data);
                    }
                } else {
                    Employee::withoutGlobalScopes()->create($data);
                }
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne " . ($i + 1) . " : " . $e->getMessage();
                $skipped++;
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    /**
     * Retourne uniquement les champs de $data dont la valeur en DB est null ou vide.
     * Les champs déjà remplis ne sont pas écrasés.
     */
    private function onlyEmptyFields(Employee $existing, array $data): array
    {
        return array_filter($data, function ($value, string $field) use ($existing) {
            $current = $existing->getAttribute($field);
            return $current === null || $current === '';
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function findHeaderRow(array $rows): ?int
    {
        $best      = 0;
        $bestIndex = null;
        foreach ($rows as $i => $row) {
            $count = count(array_filter($row, fn($v) => $v !== null && $v !== ''));
            if ($count > $best) {
                $best      = $count;
                $bestIndex = $i;
            }
        }
        return $bestIndex;
    }

    /** Retourne un tableau [champ_employee => index_colonne] */
    private function buildColumnMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $colIndex => $cell) {
            if ($cell === null || $cell === '') {
                continue;
            }
            $normalized = mb_strtolower(trim((string) $cell));
            if (isset(self::COLUMN_MAP[$normalized])) {
                $field = self::COLUMN_MAP[$normalized];
                // Ne pas écraser un mapping déjà trouvé
                $map[$field] ??= $colIndex;
            }
        }
        return $map;
    }

    private function mapRow(array $row, array $columnMap, $sheet, int $rowIndex): array
    {
        $data = [];
        foreach ($columnMap as $field => $colIndex) {
            $raw = null;
            if (in_array($field, ['birth_date', 'hire_date', 'exit_date'])) {
                foreach ([$colIndex, $colIndex - 1, $colIndex - 2] as $tryCol) {
                    if ($tryCol < 0) {
                        continue;
                    }
                    $val = $row[$tryCol] ?? null;
                    if ($val !== null && $val !== '') {
                        $raw = $val;
                        break;
                    }
                }
            } else {
                $raw = $row[$colIndex] ?? null;
            }

            if ($raw === null || $raw === '') {
                continue;
            }

            $data[$field] = match (true) {
                in_array($field, ['birth_date', 'hire_date', 'exit_date']) => $this->parseDate($raw, $sheet, $rowIndex, $colIndex),
                $field === 'gender'                                         => $this->parseGender($raw),
                $field === 'cnss_number'                                    => $this->parseCnss($raw),
                $field === 'matricule'                                      => $this->parseMatricule($raw),
                default                                                     => trim((string) $raw),
            };
        }
        return $data;
    }

    private function isTitleRow(string $value): bool
    {
        $lower = mb_strtolower(trim($value));
        $keywords = ['liste', 'personnel', 'total', 'sous-total', 'nom', 'prénom', 'prenom', 'matricule', 'scolaire', 'page'];
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) {
                return true;
            }
        }
        return strlen($value) > 60;
    }

    private function parseDate(mixed $raw, $sheet, int $rowIndex, int $colIndex): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        // Si c'est un nombre Excel (date sérialisée)
        if (is_numeric($raw) && $raw > 1000) {
            try {
                return ExcelDate::excelToDateTimeObject($raw)->format('Y-m-d');
            } catch (\Throwable) {}
        }

        // Sinon tenter Carbon
        $str = trim((string) $raw);
        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $str)->format('Y-m-d');
            } catch (\Throwable) {}
        }

        return null;
    }

    private function parseGender(mixed $raw): ?string
    {
        $val = mb_strtoupper(trim((string) $raw));
        return in_array($val, ['M', 'F']) ? $val : null;
    }

    private function parseCnss(mixed $raw): string
    {
        // Supprimer .0 des flottants Excel
        return (string) (int) $raw;
    }

    private function parseMatricule(mixed $raw): string
    {
        // Supprimer .0 des flottants Excel
        if (is_float($raw) || (is_string($raw) && str_ends_with($raw, '.0'))) {
            return (string) (int) $raw;
        }
        return trim((string) $raw);
    }
}
