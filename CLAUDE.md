# CLAUDE.md — SaaS RH Maroc (Multi-Tenancy)

## Projet
SaaS Laravel pour gestion RH des PME marocaines. Multi-tenancy par `company_id` (logique, pas de DB séparées). Conformité CNSS / IR / AMO.

## Stack
- **Backend**: Laravel 12 + MySQL
- **Admin UI**: Filament v4 — 2 panels séparés
- **Auth**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Multi-tenancy**: logique via `company_id` (scope global sur tous les modèles)

## Architecture Multi-Tenancy
Toutes les tables (sauf `ir_brackets`, `users` globaux) ont `company_id FK → companies.id`.
Utiliser un **GlobalScope** `CompanyScope` injecté via `HasCompanyScope` trait sur chaque modèle.
Le `company_id` actif vient de `auth()->user()->company_id`.

## Modules & Tables

### Core
- `companies` — ice, rc, patente, cnss_affiliation, city, email, phone
- `users` — company_id, name, email, password, role ENUM(admin,rh,manager,comptable)

### RH
- `employees` — company_id, matricule, cin, cnss_number, contract_type ENUM(CDI,CDD,Stage,ANAPEC), marital_status, number_of_children, department_id, position_id, rib, status
- `departments` — company_id, name, manager_id FK employees
- `positions` — company_id, title, base_salary
- `contracts` — company_id, employee_id, contract_type, start_date, end_date, salary_base, trial_period_end, working_hours_per_week, status

### Présence & Congés
- `attendances` — company_id, employee_id, date, check_in, check_out, worked_hours, overtime_hours
- `leave_types` — company_id, name(Annuel/Maladie/Maternité/Sans solde), legal_days_per_year
- `leaves` — company_id, employee_id, leave_type_id, start_date, end_date, status ENUM(en_attente,approuvé,refusé), approved_by FK users

### Paie (spécifique Maroc)
- `payrolls` — company_id, employee_id, month, year, salaire_brut, total_cnss_employee, total_cnss_employer, amo, ir, salaire_net, status
- `payroll_components` — payroll_id, type ENUM(prime,retenue,avantage), label, amount, taxable BOOL

### Légal Maroc
- `cnss_rates` — company_id NULLABLE, type ENUM(employee,employer), label(CNSS/AMO/CIMR/Formation), rate_percentage, plafond
- `ir_brackets` — min_salary, max_salary, rate_percentage, deduction_amount (table globale, pas de company_id)

### Déclarations
- `declarations` — company_id, type ENUM(CNSS,IR,Etat_9421), month, year, generated_file_path, status

## Règles de Code

### Conventions
- Français pour les labels UI / commentaires métier, anglais pour le code
- Modèles : singulier PascalCase (`Employee`, `Payroll`)
- Migrations : snake_case pluriel (`create_employees_table`)
- Toujours `->nullable()` explicite si nullable, jamais implicite

### GlobalScope Multi-Tenancy
```php
// App\Models\Traits\HasCompanyScope.php
// Ajouter ce trait sur TOUS les modèles sauf IrBracket
protected static function booted(): void
{
    static::addGlobalScope(new CompanyScope);
    static::creating(fn($m) => $m->company_id ??= auth()->user()?->company_id);
}
```

### Calcul Paie Maroc
- CNSS salarié : 4.48% (plafond 6000 MAD/mois)
- CNSS patronal : 10.77%
- AMO salarié : 2.26% | AMO patronal : 4.11%
- IR : barème progressif via `ir_brackets` + déduction forfaitaire frais pro 20% (plafond 30000/an)
- Ordre calcul : `salaire_brut` → déd. CNSS → base IR → IR → `salaire_net`

### Filament v4 — Architecture 2 Panels

#### Installation
```bash
composer require filament/filament:"^4.0"
php artisan filament:install --panels
```

#### Panel 1 : `admin` (Super-Admin SaaS)
- **Chemin** : `/admin`
- **Provider** : `app/Providers/Filament/AdminPanelProvider.php`
- **Rôle** : gestion de la plateforme — CRUD companies, supervision globale, taux légaux (CNSS/IR)
- **Guard** : `web`, middleware `role:super-admin` via Spatie
- **Pas de** `company_id` scope — voit toutes les companies
- **Resources** : `CompanyResource`, `IrBracketResource`, `CnssRateResource`

```php
// AdminPanelProvider — points clés
->id('admin')
->path('admin')
->authMiddleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::using('super-admin')])
->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
```

#### Panel 2 : `app` (Panel RH par company)
- **Chemin** : `/app`
- **Provider** : `app/Providers/Filament/AppPanelProvider.php`
- **Rôle** : gestion RH d'une company — employees, paie, congés, déclarations
- **Guard** : `web`, middleware `role:admin|rh|manager|comptable`
- **GlobalScope actif** — tout est filtré par `auth()->user()->company_id`
- **Resources** : `EmployeeResource`, `PayrollResource`, `LeaveResource`, etc.

```php
// AppPanelProvider — points clés
->id('app')
->path('app')
->authMiddleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::using('admin|rh|manager|comptable')])
->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
->tenant(Company::class, ownershipRelationship: 'company') // optionnel si tu veux le tenant switcher natif
```

#### Structure des dossiers
```
app/Filament/
├── Admin/
│   └── Resources/
│       ├── CompanyResource.php
│       ├── IrBracketResource.php
│       └── CnssRateResource.php
└── App/
    └── Resources/
        ├── EmployeeResource.php
        ├── DepartmentResource.php
        ├── ContractResource.php
        ├── PayrollResource.php
        ├── LeaveResource.php
        └── DeclarationResource.php
```

#### Conventions Filament v4
- Namespaces : `Filament\Forms\Components\*`, `Filament\Tables\Columns\*`
- Icons : `heroicon-o-*` (outline) ou `heroicon-s-*` (solid)
- **PAS** de `Schema::make()` → utiliser `Form::make()` et `Table::make()`
- Génération resource : `php artisan make:filament-resource Employee --panel=app --generate`

## Commandes Utiles
```bash
# Setup initial Filament
composer require filament/filament:"^4.0"
php artisan filament:install --panels          # génère AdminPanelProvider
php artisan make:filament-panel app            # crée le 2ème panel

# Models + migrations
php artisan make:model Employee -mfs          # model + migration + factory + seeder
php artisan make:policy EmployeePolicy --model=Employee

# Resources (toujours préciser --panel)
php artisan make:filament-resource Employee --panel=app --generate
php artisan make:filament-resource Company --panel=admin --generate

# DB
php artisan migrate:fresh --seed              # reset complet dev
```

## Priorité de Développement (MVP)
1. **Setup Filament** : installer, créer les 2 panels, configurer guards/middleware
2. **Auth + companies + users + roles** (Spatie) + `CompanyScope` trait
3. Employees + departments + positions + contracts (panel `app`)
4. Paie : payrolls + calcul CNSS/IR (service dédié `PayrollCalculator`)
5. Congés
6. Présence
7. Déclarations

## Ce qu'on NE fait PAS encore (V2)
- Portail employé self-service
- Génération XML CNSS
- Signature électronique
- Intégration comptable
- Dashboard BI

## Tests
- Feature tests pour le calcul de paie (CNSS, IR)
- Policy tests pour l'isolation multi-tenant (un user ne peut pas accéder aux données d'une autre company)
