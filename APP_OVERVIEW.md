# gestion-hr — Vue d'ensemble pour modifications

SaaS RH pour PME marocaines. Multi-tenancy logique par `company_id` (pas de DB séparées).

---

## Stack technique

| Couche | Technologie |
|---|---|
| Backend | Laravel 12 + PHP 8.2 |
| DB (dev) | SQLite |
| UI Admin | Filament v4.11.1 (2 panels) |
| Auth | Laravel Sanctum + sessions web |
| Permissions | Spatie Laravel Permission 6.x |
| Audit log | Spatie Activity Log |
| PDF | (génération via controller dédié) |

---

## Architecture Multi-Tenancy

Toutes les tables (sauf `ir_brackets`, tables globales Spatie) ont `company_id`.

**Trait `HasCompanyScope`** — a ajouter sur tous les modèles RH :
```php
use App\Models\Traits\HasCompanyScope;
// Dans booted() : ajoute CompanyScope + auto-set company_id au creating
```

**Scope `CompanyScope`** (`app/Models/Scopes/CompanyScope.php`) — filtre par `auth()->user()->company_id`.

**Scope `GlobalOrCompanyScope`** — pour les tables à `company_id NULLABLE` (ex: `CnssRate`) : retourne les enregistrements globaux (NULL) + ceux de la company courante.

---

## Filament v4 — Conventions importantes

```php
// Signature form() — PAS Form $form
public static function form(Schema $schema): Schema
{
    return $schema->columns(1)->components([...]);
}

// Types proprietes
protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-xxx';
protected static \UnitEnum|string|null   $navigationGroup = 'Groupe';

// Imports
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
// etc.
```

---

## Panels Filament

### Panel `admin` — `/admin`
- Provider : `app/Providers/Filament/AdminPanelProvider.php`
- Role requis : `super-admin`
- Resources : `app/Filament/Admin/Resources/`
  - `CompanyResource` — CRUD entreprises
  - `IrBracketResource` — tranches IR barème progressif
  - `CnssRateResource` — taux CNSS/AMO (global ou par company)
- Pas de GlobalScope actif — voit toutes les companies

### Panel `app` — `/app`
- Provider : `app/Providers/Filament/AppPanelProvider.php`
- Roles : `admin | rh | manager | comptable | employee`
- Resources : `app/Filament/App/Resources/`
  - `EmployeeResource` — fiche employé complète
  - `DepartmentResource` — départements
  - `PositionResource` — postes (avec `base_salary`, `category`)
  - `ContractResource` — contrats CDI/CDD/Stage/ANAPEC
  - `PayrollResource` — fiches de paie (calcul automatique)
  - `LeaveTypeResource` — types de congés
  - `LeaveResource` — demandes de congés (workflow approbation)
  - `AttendanceResource` — pointages (calcul auto heures travaillées/sup)
  - `DeclarationResource` — déclarations CNSS/IR/Etat_9421
  - `DocumentRequestResource` — demandes de documents RH
  - `AuditLogResource` — journal d'activité (read-only)
  - `UserResource` — gestion utilisateurs de la company

---

## Modeles principaux

### `Company` (`app/Models/Company.php`)
Champs : `name`, `ice`, `rc`, `patente`, `cnss_affiliation`, `city`, `email`, `phone`

### `User` (`app/Models/User.php`)
Champs : `company_id`, `name`, `email`, `password`, `employee_id` (nullable, lien vers Employee)
Roles Spatie : `super-admin`, `admin`, `rh`, `manager`, `comptable`, `employee`

### `Employee` (`app/Models/Employee.php`)
Champs : `company_id`, `matricule`, `cin`, `cnss_number`, `first_name`, `last_name`, `email`, `phone`, `birth_date`, `hire_date`, `contract_type` ENUM(CDI,CDD,Stage,ANAPEC), `marital_status`, `number_of_children`, `department_id`, `position_id`, `rib`, `status`, `photo`, `address`, `city`
Computed : `full_name` (`first_name last_name`)
Relations : `company`, `department`, `position`, `contracts`, `documents`, `leaves`
Trait : `HasCompanyScope`, `LogsActivity`

### `Department` (`app/Models/Department.php`)
Champs : `company_id`, `name`, `manager_id` (FK employees nullable)

### `Position` (`app/Models/Position.php`)
Champs : `company_id`, `title`, `base_salary`, `category`

### `Contract` (`app/Models/Contract.php`)
Champs : `company_id`, `employee_id`, `contract_type`, `start_date`, `end_date`, `salary_base`, `trial_period_end`, `working_hours_per_week`, `status`

### `Payroll` (`app/Models/Payroll.php`)
Champs clés :
```
company_id, employee_id, month, year
salaire_brut, overtime_hours, overtime_amount, overtime_hours_night, overtime_amount_night
is_prorata, worked_days, total_working_days
absence_days, absence_deduction
total_cnss_employee, total_cnss_employer, amo_employee, amo_employer, ir, salaire_net
anciennete_years, anciennete_rate, anciennete_amount
cnss_employee_rate, cnss_employer_rate, amo_employee_rate, amo_employer_rate, ir_rate_applied (snapshot)
status ENUM(brouillon, valide, paye)
```
- `isLocked()` : retourne true si status = 'payé' (bloque l'édition)
- `getPeriodeLabelAttribute()` : "Janvier 2026"
- Trait : `HasCompanyScope`, `LogsActivity`

### `PayrollComponent` (`app/Models/PayrollComponent.php`)
Champs : `payroll_id`, `type` ENUM(prime_imposable, prime_non_imposable, avantage_imposable, retenue), `label`, `amount`

### `LeaveType` (`app/Models/LeaveType.php`)
Champs : `company_id`, `name` (Annuel/Maladie/Maternité/Sans solde), `legal_days_per_year`

### `Leave` (`app/Models/Leave.php`)
Champs : `company_id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `status` ENUM(en_attente, approuvé, refusé), `approved_by` FK users

### `LeaveBalance` (`app/Models/LeaveBalance.php`)
Champs : `company_id`, `employee_id`, `leave_type_id`, `year`, `allocated_days`, `used_days`, `remaining_days`

### `Attendance` (`app/Models/Attendance.php`)
Champs : `company_id`, `employee_id`, `date`, `check_in`, `check_out`, `worked_hours`, `overtime_hours`
- `booted()` calcule auto `worked_hours` et `overtime_hours` sur `saving`

### `Declaration` (`app/Models/Declaration.php`)
Champs : `company_id`, `type` ENUM(CNSS,IR,Etat_9421), `month`, `year`, `generated_file_path`, `status`, `total_salaires_bruts`, `total_cnss_employee`, `total_cnss_employer`, `total_ir`, `employee_count`

### `CnssRate` (`app/Models/CnssRate.php`)
Champs : `company_id` NULLABLE, `type` ENUM(employee,employer), `label`(CNSS/AMO/CIMR/Formation), `rate_percentage`, `plafond`
Scope : `GlobalOrCompanyScope` (NULL = taux global plateforme)

### `IrBracket` (`app/Models/IrBracket.php`)
Champs : `min_salary`, `max_salary` (nullable = infini), `rate_percentage`, `deduction_amount`
Pas de `company_id` — table globale

### `DocumentRequest` (`app/Models/DocumentRequest.php`)
Champs : `company_id`, `employee_id`, `type`, `status`, `notes`, `generated_file_path`

### `EmployeeDocument` (`app/Models/EmployeeDocument.php`)
Champs : `company_id`, `employee_id`, `type`, `file_path`, `issued_at`, `expiry_date`

---

## Service PayrollCalculator (`app/Services/PayrollCalculator.php`)

Point central du calcul de paie marocain.

### Methodes publiques
- `computeValues(Employee, salaireBrut, components, overtimeHours, isProrata, workedDays, totalWorkingDays): array` — calcul sans persistance (prévisualisation)
- `calculate(Employee, month, year, salaireBrut, components, overtimeHours, isProrata, ...): Payroll` — calcul + `updateOrCreate` en DB
- `fetchOvertimeHours(employeeId, month, year): float` — somme depuis `attendances`
- `countWorkingDays(month, year): int` — jours lundi-vendredi

### Logique calcul (ordre)
```
1. salaire_brut (base) + prorata si mois incomplet
2. overtime_amount = heures_sup_jour * taux_horaire * 1.25
   overtime_amount_night = heures_sup_nuit * taux_horaire * 1.50
3. anciennete_amount = salaire_brut * taux (5/10/15/20% selon années)
   - 5% >= 5 ans, 10% >= 12 ans, 15% >= 20 ans, 20% >= 25 ans
4. absence_deduction = jours_sans_solde_approuves * (salaire / jours_ouvrables)
5. brut_imposable = salaire_brut_effectif + overtime + anciennete + primes_imposables - absence_deduction
6. base_cnss = min(brut_imposable, plafond_cnss=6000)
   cnss_employee = base_cnss * 4.48%
   cnss_employer = base_cnss * 10.77%
   amo_employee = brut_imposable * 2.26%
   amo_employer = brut_imposable * 4.11%
7. frais_pro = min(brut_imposable * 20%, 30000/12)
   base_ir = brut_imposable - cnss_employee - frais_pro
   ir = ir_annuel(base_ir * 12) / 12 - deduction_familiale(nb_enfants * 360 / 12)
8. salaire_net = brut_imposable - cnss_employee - amo_employee - ir - retenues_sup
```

Les taux sont lus depuis `cnss_rates` (priorité : company > global > constantes fallback).

---

## Policies (controle d'acces)

Chaque ressource a sa policy dans `app/Policies/`. Roles et permissions :

| Role | Acces |
|---|---|
| `super-admin` | Panel `/admin` uniquement, toutes companies |
| `admin` | Panel `/app` : acces complet company |
| `rh` | Panel `/app` : gestion RH complète |
| `manager` | Panel `/app` : lecture + approbation congés |
| `comptable` | Panel `/app` : paie + déclarations |
| `employee` | Panel `/app` : lecture seule (ses fiches payé uniquement) |

---

## Routes specifiques (hors Filament)

```php
// app/Http/Controllers/PayrollPdfController.php
Route::get('payrolls/{payroll}/pdf', ...) // bulletin de paie PDF
// app/Http/Controllers/PayrollBulkPdfController.php
Route::get('payrolls/bulk-pdf', ...)      // ZIP multi-bulletins
// app/Http/Controllers/DocumentPdfController.php
Route::get('documents/{document}/pdf', ...) // attestations PDF
```

---

## Conventions de code

- **Labels UI / commentaires metier** : francais
- **Code PHP** : anglais
- **Modeles** : singulier PascalCase (`Employee`, `Payroll`)
- **Migrations** : snake_case pluriel (`create_employees_table`)
- **Toujours** `->nullable()` explicite, jamais implicite
- **PAS** de `Schema::make()` — utiliser la signature `form(Schema $schema): Schema`
- **Icons** : `heroicon-o-*` (outline) ou `heroicon-s-*` (solid)

---

## Structure des dossiers cles

```
app/
├── Filament/
│   ├── Admin/
│   │   ├── Pages/Auth/Login.php
│   │   ├── Resources/
│   │   │   ├── CompanyResource.php
│   │   │   ├── IrBracketResource.php
│   │   │   └── CnssRateResource.php
│   │   └── Widgets/EmployeeStatsWidget.php
│   └── App/
│       └── Resources/
│           ├── EmployeeResource.php
│           ├── DepartmentResource.php
│           ├── PositionResource.php
│           ├── ContractResource.php
│           ├── PayrollResource.php
│           ├── LeaveTypeResource.php
│           ├── LeaveResource.php
│           ├── AttendanceResource.php
│           ├── DeclarationResource.php
│           ├── DocumentRequestResource.php
│           ├── AuditLogResource.php
│           └── UserResource.php
├── Models/
│   ├── Scopes/
│   │   ├── CompanyScope.php
│   │   └── GlobalOrCompanyScope.php
│   ├── Traits/
│   │   └── HasCompanyScope.php
│   ├── Company.php, User.php, Employee.php
│   ├── Department.php, Position.php, Contract.php
│   ├── Payroll.php, PayrollComponent.php
│   ├── LeaveType.php, Leave.php, LeaveBalance.php
│   ├── Attendance.php, Declaration.php
│   ├── CnssRate.php, IrBracket.php
│   ├── DocumentRequest.php, EmployeeDocument.php
├── Policies/         (une policy par modele)
├── Services/
│   ├── PayrollCalculator.php
│   └── DeclarationReportService.php
└── Http/Controllers/
    ├── PayrollPdfController.php
    ├── PayrollBulkPdfController.php
    └── DocumentPdfController.php

database/migrations/   (ordre par timestamp)
```

---

## Commandes utiles (dev)

```bash
php artisan migrate:fresh --seed              # reset DB complet
php artisan make:filament-resource Xxx --panel=app --generate
php artisan make:filament-resource Xxx --panel=admin --generate
php artisan make:model Xxx -mfs              # model + migration + factory + seeder
```
