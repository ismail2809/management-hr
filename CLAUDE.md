# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Projet

Application RH pour une **école** (établissement scolaire marocain) : dossiers employés, congés/absences, demandes de documents administratifs, autres demandes (photocopies, salles, rencontres), journal d'audit. Laravel 12 + Filament v4, UI 100 % française.

Multi-tenancy **logique par `ecole_setting_id`** (une ligne `ecole_settings` = une école). Le nommage historique dit « company » partout dans le code (`CompanyScope`, `HasCompanyScope`, `HasCompanyField`, relation `company()`) mais la colonne est **toujours `ecole_setting_id`** — il n'y a pas de table `companies`.

## Environnement de développement

Le projet vit dans WSL (`/var/www/management-hr`) mais Claude Code tourne côté Windows sur un chemin UNC. **Les commandes PHP/composer doivent passer par WSL**, sinon `cmd.exe` retombe sur le répertoire Windows et les chemins relatifs cassent (`Could not open input file: vendor/...`) :

```bash
wsl.exe -d Ubuntu -- bash -lc "cd /var/www/management-hr && php artisan ..."
```

## Commandes

```bash
composer setup          # install + key:generate + migrate + npm install + npm run build
composer dev            # serve + queue:listen + pail + vite (concurrently)
composer test           # config:clear + artisan test   ⚠ voir « État des tests »
npm run dev / build     # Vite seul

php artisan migrate:fresh --seed   # reset dev complet
php artisan test --filter=NomDuTest
php artisan shield:generate --all --panel=app   # régénère les permissions Shield
vendor/bin/pint         # formatage
```

DB de dev : **MySQL** (`management-hr`), pas SQLite malgré `.env.example`.

### Seeding

`DatabaseSeeder` a un ordre imposé : données de référence → `shield:generate --all --panel=app` → `RolesPermissionsSeeder`. Les permissions doivent exister avant l'assignation des rôles ; ne pas réordonner.

### État des tests

`tests/Feature/MultiTenancyTest.php` et `PayrollCalculatorTest.php` sont des **vestiges morts** de l'ancien concept « SaaS paie multi-entreprises » : ils référencent `Company`, `Department`, `CnssRate`, `IrBracket`, `PayrollCalculator` — aucun de ces modèles/services n'existe. De plus `phpunit.xml` pointe sur sqlite `:memory:` alors que le PHP de WSL n'a pas le driver sqlite. Résultat actuel : 23 échecs, seuls les deux `ExampleTest` passent. Toute nouvelle suite de tests doit être écrite contre `EcoleSettings`/`ecole_setting_id`, et le driver sqlite installé (ou `phpunit.xml` basculé sur MySQL).

Autres vestiges paie sans code actif derrière : `resources/views/pdf/bulletin-paie.blade.php`, `resources/views/filament/app/pages/generate-payroll.blade.php`, les labels `payroll`/`declaration`/`attendance` dans `config/audit_labels.php`. `APP_OVERVIEW.md` et `MODULES.md` décrivent eux aussi l'ancienne application (companies, CNSS, IR, bulletins) — **ne pas s'y fier**.

## Architecture

### Les deux panels Filament — piège de nommage

Les noms de panel, les chemins d'URL et les dossiers ne correspondent pas à l'intuition :

| Panel | `->id()` | URL | Rôle | Provider |
|---|---|---|---|---|
| Panel RH (défaut) | `app` | **`/admin`** | tout le personnel de l'école | `AppPanelProvider` |
| Panel plateforme | `admin` | **`/super-admin`** | `super-admin` uniquement | `AdminPanelProvider` |

**Les deux panels découvrent le même dossier** `app/Filament/Admin/{Resources,Pages,Widgets}` avec le namespace `App\Filament\Admin\*`. Il n'existe pas de `app/Filament/App/`. Une resource ajoutée là apparaît donc dans les deux panels ; la différenciation se fait par rôle/permission (`canViewAny()`, policies), pas par emplacement. Générer avec `--panel=app` puis vérifier que le fichier atterrit bien sous `Filament/Admin/`.

`AppPanelProvider` est `->default()`, protégé par `App\Http\Middleware\RequireAppRole` (rôle autorisé **et** `ecole_setting_id` non nul, sauf super-admin). `AdminPanelProvider` utilise `RoleMiddleware::using('super-admin')`.

### Multi-tenancy

Deux traits dans `app/Models/Traits/`, à poser sur tout nouveau modèle métier :

- **`HasCompanyScope`** — tables à `ecole_setting_id` obligatoire. Filtre sur l'école de l'utilisateur, **court-circuité pour `super-admin`** (il voit tout). Au `creating`, remplit `ecole_setting_id` depuis `auth()` ou `Filament::auth()`, avec repli sur la première `EcoleSettings`.
  Modèles : `Employee`, `Leave`, `LeaveType`, `DocumentRequest`, `EmployeeDocument`, `EmployeeAccident`, `EmployeeCredit`, `EmployeeFondationM6`, `Profession`, `Groupe`, `NiveauScolaire`, `Transport`, `AnneeScolaire`.
- **`HasGlobalOrCompanyScope`** — tables à `ecole_setting_id` NULLABLE : renvoie les lignes globales (`NULL`) **plus** celles de l'école. Attention : ce scope **ne fait pas** d'exception pour `super-admin` (asymétrie avec le précédent).
  Modèles : `DocumentType`, `NatureDocument`, `CommunicationMethod`, `EmployeeDocumentType`.

Hors scope : `User` (porte `ecole_setting_id` mais filtré par resource), `EcoleSettings`.

Côté Filament, trois concerns dans `app/Filament/Admin/Concerns/` :
- `HasCompanyField` — `static::companyField()` injecte une Section cachée avec le `Hidden::make('ecole_setting_id')` ; à appeler en tête de `form()`.
- `InjectsCompanyId` — pour les pages `Create`, remplit `ecole_setting_id` dans `mutateFormDataBeforeCreate()`.
- `HasRoleBasedDelete` — suppression réservée à `super-admin`, `forceDelete` interdit à tous.

Les resources qui doivent laisser le super-admin voir toutes les écoles surchargent `getEloquentQuery()` avec `->withoutGlobalScopes()` (cf. `TransportResource`).

### Rôles et permissions

Spatie Permission + Filament Shield. Convention Shield : `Ability:Model` (`ViewAny:Employee`, `ApproveLeave`, `View:Dashboard`…). Les policies de `app/Policies/` délèguent toutes à `$user->can('Ability:Model')`.

Hiérarchie (définie dans `RolesPermissionsSeeder` et `App\Models\User`) :
- `super-admin` — tout, seul à pouvoir supprimer (soft) et à voir le journal d'audit.
- `directeur` — gestion complète de son école + suppressions.
- `secretaire`, `surveillante` — gestion RH partielle (nuances sur Documents administratifs vs Autres demandes).
- **Rôles à accès limité** (`User::ALL_LIMITED_ROLES`) : `BASIC_ROLES` = `femme-de-menage`, `chauffeur`, `gardien` ; `EXTENDED_ROLES` = `enseignant`, `assistante-transport` (les seuls à avoir « Autres demandes »).

`User::isBasicRole()` est le levier de personnalisation le plus utilisé dans les resources — il pilote à la fois la visibilité de sections de formulaire, le verrouillage de champs, les labels de navigation (« Mes documents » vs « Documents administratifs ») et le filtrage `where('employee_id', $user->employee_id)` dans `getEloquentQuery()`. Un utilisateur à accès limité est relié à sa fiche via `users.employee_id`.

### DocumentRequest : un modèle, deux resources

La table `document_requests` est découpée par la colonne `categorie` :
- `DocumentAdministratifResource` (slug `documents-administratifs`) → `categorie = 'document'`, types dans `DocumentRequest::$documentTypes`.
- `AutreDemandeResource` (slug `autres-demandes`) → `categorie = 'autre'`, types dans `DocumentRequest::$autreTypes`.

Chacune filtre dans `getEloquentQuery()` et impose son `categorie` à la création. Ajouter un type de demande = ajouter une entrée dans le tableau statique correspondant, pas une nouvelle table.

### Génération PDF

`DocumentPdfController` (dompdf) : vérifie l'appartenance à l'école, valide le `type` contre les tableaux statiques (liste blanche), rend `resources/views/pdf/documents/{type}.blade.php` avec repli sur `generic`. **Effet de bord :** le téléchargement passe la demande en `approuvé` et horodate `processed_at` ; l'aperçu (`preview`) non.

### Divers structurant

- **Soft deletes partout** (migration `add_soft_deletes_to_all_tables`) ; aucune suppression définitive n'est exposée.
- **Audit** : Spatie Activitylog via `LogsActivity` sur la plupart des modèles, chacun avec son `useLogName()`. `AuditLogResource` est en lecture seule et `canViewAny()` → `super-admin` seulement (le modèle `Activity` n'a pas de scope tenant, d'où la restriction).
- **`Employee::getRouteKeyName()` = `uuid`** — les URL Filament utilisent l'uuid, pas l'id.
- **`AnneeScolaire`** : un `saved()` hook garantit une seule année active par école. Plusieurs tables portent une colonne texte `annee_scolaire` dénormalisée.
- **Règles métier configurables** dans `config/hr.php` : délai minimum photocopie, natures d'évaluation en conflit, niveaux concernés, professions exclues des rencontres direction. Y ajouter les nouvelles règles plutôt que de les coder en dur.
- **Uploads** : valider avec `App\Rules\SafeFileUpload` (liste noire d'extensions exécutables, svg inclus).
- `EmployeeImportService` importe des employés depuis Excel (phpspreadsheet) avec une `COLUMN_MAP` d'en-têtes FR tolérante aux variantes.

## Conventions de code

- **Français** pour labels UI, `modelLabel`, groupes de navigation et commentaires métier ; **anglais** pour le code (classes, méthodes, colonnes).
- Modèles singulier PascalCase, migrations snake_case pluriel, `->nullable()` toujours explicite.

### Filament v4

Signatures et types réellement utilisés dans ce projet :

```php
public static function form(Schema $schema): Schema   // pas Form $form
{
    return $schema->columns(1)->components([...]);
}

protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-users';
protected static \UnitEnum|string|null   $navigationGroup = 'Personnel';
```

Imports à respecter : `Filament\Schemas\Schema`, `Filament\Schemas\Components\{Section,Grid}`, `Filament\Forms\Components\*` pour les champs, `Filament\Actions\*` (et **pas** `Filament\Tables\Actions\*`) pour les actions de table, `Filament\Tables\Columns\*`.

Deux styles de resources coexistent : le style plat historique (`EmployeeResource.php` + sous-dossier `Pages/`) et le style généré v4 (`Transports/TransportResource.php` + `Schemas/`, `Tables/`, `Pages/`). Suivre le style du dossier que l'on modifie ; pour une nouvelle resource, le style généré est celui de `php artisan make:filament-resource`.

Groupes de navigation déclarés dans `AppPanelProvider` : `Personnel`, `Congés & Absences`, `Demandes`, `Administration`, `Paramétrage`, `Journal d'audit`.

## Déploiement

Deux cibles : cPanel (`.cpanel.yml` — composer install, migrate --force, caches, storage:link) et Nixpacks/Docker (`nixpacks.toml` — PHP 8.3, build Vite, `filament:cache-components`). `AppServiceProvider` force HTTPS en production.
