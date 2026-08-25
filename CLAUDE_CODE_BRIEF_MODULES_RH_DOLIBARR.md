# Brief pour Claude Code — Ajout de 4 modules RH (inspirés Dolibarr)

Contexte : projet `gestion-hr`, SaaS RH multi-tenant (Laravel 12 + Filament v4.11.1).
Voir `APP_OVERVIEW.md` à la racine du projet pour toutes les conventions déjà en place
(multi-tenancy `HasCompanyScope`, signature `form(Schema $schema): Schema`, etc.).
**Respecter strictement ces conventions existantes** — ne rien réinventer.

Objectif : ajouter 4 modules au panel `app` existant, inspirés des modules RH de Dolibarr :
1. Recrutement
2. Formation et développement
3. Gestion de la performance
4. Notes de frais

Tu peux traiter les modules dans l'ordre ci-dessous, un par un, avec un commit par module.
Ne touche à aucun modèle/resource existant sauf mention explicite (Employee, Payroll).

---

## Règles transverses (s'appliquent aux 4 modules)

- Tous les nouveaux modèles ont `company_id` + trait `HasCompanyScope` + `LogsActivity` (Spatie).
- Migrations : `snake_case` pluriel, `->nullable()` explicite partout où c'est le cas, FK avec
  `->constrained()->cascadeOnDelete()` sauf indication contraire.
- Modèles : singulier PascalCase, factories + seeders pour chaque nouveau modèle
  (`php artisan make:model Xxx -mfs`).
- Filament resources : générées avec `php artisan make:filament-resource Xxx --panel=app --generate`
  puis adaptées à la main. Toujours `use Filament\Schemas\Schema;` et la signature
  `form(Schema $schema): Schema` — jamais `Schema::make()`.
- Icônes : `heroicon-o-*` (nav) cohérentes avec le reste du panel `app`.
- Une **Policy** par nouveau modèle dans `app/Policies/`, respectant la table de rôles
  existante (`admin`, `rh`, `manager`, `comptable`, `employee`).
- Labels UI et commentaires métier en **français**, code PHP (noms de variables/méthodes) en **anglais**.
- Chaque module ajoute son `NavigationGroup` Filament : `'Recrutement'`, `'Formation'`,
  `'Performance'`, `'Notes de frais'`.
- Écrire les tests Pest/PHPUnit de base (au moins : policy access + calcul métier s'il y en a)
  pour chaque module.

---

## Module 1 — Recrutement

### Migrations / Modèles

**`job_openings`**
```
company_id, title, department_id (FK departments, nullable), position_id (FK positions, nullable),
description (text), status ENUM(ouvert,pourvu,fermé) default ouvert, published_at (nullable datetime)
```
Modèle `JobOpening` : relations `department()`, `position()`, `candidates()` (hasMany).

**`candidates`**
```
company_id, job_opening_id (FK), first_name, last_name, email, phone (nullable),
cv_path (nullable string), status ENUM(nouveau,présélectionné,entretien,offre,embauché,rejeté) default nouveau,
notes (text nullable)
```
Modèle `Candidate` : relations `jobOpening()`, `interviewSchedules()` (hasMany).

**`interview_schedules`**
```
company_id, candidate_id (FK), scheduled_at (datetime), interviewer_id (FK users, nullable),
notes (text nullable), outcome ENUM(en_attente,positif,négatif) default en_attente
```
Modèle `InterviewSchedule` : relations `candidate()`, `interviewer()` (belongsTo User).

### Filament — `app/Filament/App/Resources/`

- `JobOpeningResource` : formulaire (title, department, position, description, status, published_at),
  colonnes liste avec badge coloré sur `status`. `RelationManager` `CandidatesRelationManager`
  listant les candidats de l'offre.
- `CandidateResource` : formulaire avec upload CV (`FileUpload` → disque `local`, dossier
  `candidates/cvs`), colonnes liste avec badge `status`. Filtre par `job_opening_id`.
  Action de ligne **"Convertir en Employee"** visible uniquement quand `status = embauché` :
  ouvre un formulaire pré-rempli (`first_name`, `last_name`, `email`) qui crée un `Employee`
  (statut `actif`, `hire_date` = aujourd'hui) puis marque le candidat comme traité.
  `RelationManager` `InterviewSchedulesRelationManager`.

### Policy
`CandidateResource` et `JobOpeningResource` : accès complet `admin`/`rh`, lecture seule `manager`,
aucun accès `comptable`/`employee`.

---

## Module 2 — Formation et développement

### Migrations / Modèles

**`training_courses`**
```
company_id, title, description (text nullable), instructor (string nullable),
start_date (date), end_date (date), capacity (integer nullable)
```
Modèle `TrainingCourse` : relation `enrollments()` (hasMany `TrainingEnrollment`).

**`training_enrollments`**
```
company_id, training_course_id (FK), employee_id (FK employees),
status ENUM(inscrit,en_cours,terminé,abandonné) default inscrit,
completion_date (date nullable), certificate_path (string nullable)
```
Modèle `TrainingEnrollment` : relations `trainingCourse()`, `employee()`.
Contrainte unique `(training_course_id, employee_id)`.

### Filament

- `TrainingCourseResource` : formulaire complet, colonne calculée "places restantes"
  (`capacity - enrollments_count`), `RelationManager` `EnrollmentsRelationManager`
  (ajouter/retirer des employés, upload certificat quand `status = terminé`).
- Ajouter un **`RelationManager` `TrainingsRelationManager`** sur `EmployeeResource` existant
  (lecture seule) pour voir l'historique de formation de l'employé — **c'est la seule
  modification apportée à un resource existant**.

### Policy
Accès complet `admin`/`rh`, lecture seule `manager` (formations de son équipe uniquement),
`employee` voit uniquement ses propres inscriptions (scope sur `employee_id = auth user's employee_id`).

---

## Module 3 — Gestion de la performance

### Migrations / Modèles

**`performance_review_cycles`**
```
company_id, name (ex: "Évaluation annuelle 2026"), period_start (date), period_end (date),
status ENUM(brouillon,actif,clôturé) default brouillon
```
Modèle `PerformanceReviewCycle` : relations `goals()`, `reviews()`.

**`performance_goals`**
```
company_id, employee_id (FK), cycle_id (FK performance_review_cycles),
title, description (text nullable), status ENUM(en_cours,atteint,non_atteint) default en_cours
```
Modèle `PerformanceGoal` : relations `employee()`, `cycle()`.

**`performance_reviews`**
```
company_id, employee_id (FK), cycle_id (FK), reviewer_id (FK users),
rating (tinyint, 1 à 5), strengths (text nullable), areas_to_improve (text nullable),
status ENUM(brouillon,soumis,validé) default brouillon
```
Modèle `PerformanceReview` : relations `employee()`, `cycle()`, `reviewer()`.
Ajouter validation `rating between:1,5`.

### Filament

- `PerformanceReviewCycleResource` : réservé `admin`/`rh`, gère les cycles.
  `RelationManager` `GoalsRelationManager` et `ReviewsRelationManager`.
- `PerformanceGoalResource` : `manager` peut créer/éditer les objectifs de son équipe,
  `employee` voit ses propres objectifs en lecture seule.
- `PerformanceReviewResource` : `manager` remplit la review de ses employés
  (scope `EmployeesUnderManagerScope` — **à créer**, filtre `employee_id` sur les employés
  dont `department.manager_id = auth()->user()->employee_id`), `rh`/`admin` voient tout,
  `employee` voit sa propre review uniquement après passage à `status = validé`.

### Policy
Le point sensible est le scope manager → équipe uniquement. Écrire un test Pest dédié qui
vérifie qu'un manager ne peut pas voir/éditer la review d'un employé hors de son département.

---

## Module 4 — Notes de frais

### Migrations / Modèles

**`expense_reports`**
```
company_id, employee_id (FK), month (tinyint 1-12), year (smallint),
status ENUM(brouillon,soumis,approuvé,rejeté,remboursé) default brouillon,
total_amount (decimal 10,2 default 0), approved_by (FK users nullable),
approved_at (datetime nullable)
```
Modèle `ExpenseReport` : relations `employee()`, `lines()` (hasMany `ExpenseLine`), `approver()`.
Contrainte unique `(employee_id, month, year)`.
Méthode `recalculateTotal()` : somme des `expense_lines.amount`, appelée dans `saved()` de `ExpenseLine`.

**`expense_lines`**
```
expense_report_id (FK), date (date), category ENUM(transport,repas,hébergement,autre),
amount (decimal 10,2), description (text nullable), receipt_path (string nullable)
```
Modèle `ExpenseLine` : relation `expenseReport()`.

### Filament

- `ExpenseReportResource` : formulaire (employee_id, month, year), `RelationManager`
  `LinesRelationManager` (CRUD des lignes avec upload justificatif), colonne `total_amount`
  en lecture seule (calculée), badge `status`.
  Actions de workflow (comme `LeaveResource` existant) :
  - `employee` : "Soumettre" (`brouillon` → `soumis`), verrouille l'édition des lignes.
  - `comptable` : "Approuver" / "Rejeter" (`soumis` → `approuvé`/`rejeté`), renseigne
    `approved_by`/`approved_at`.
  - `comptable` : "Marquer remboursé" (`approuvé` → `remboursé`) → **crée automatiquement**
    un `PayrollComponent` de type `retenue` inversé (remboursement, montant négatif ou type
    dédié à ajouter à l'ENUM existant si besoin) sur la `Payroll` du mois correspondant si elle
    existe déjà, sinon log un avertissement (ne pas créer la Payroll automatiquement).

### Policy
`employee` : CRUD sur ses propres `ExpenseReport` tant que `status = brouillon`.
`comptable`/`admin` : accès complet + actions d'approbation. `manager`/`rh` : lecture seule
sur les rapports de leur périmètre.

⚠️ Vérifier avec l'utilisateur si l'ENUM `PayrollComponent.type` doit être étendu
(`remboursement_frais`) avant de faire l'intégration paie — ne pas modifier le calcul de
`PayrollCalculator` sans confirmation, juste préparer le hook.

---

## Ordre de livraison suggéré

1. Recrutement (aucune dépendance)
2. Formation (dépend de `Employee` existant uniquement)
3. Performance (dépend de `Employee`/`Department` existants)
4. Notes de frais (dépend de `Employee` + touche potentiellement `PayrollComponent` → demander
   confirmation avant d'étendre l'ENUM)

Pour chaque module : migration → modèle → factory/seeder → policy → Filament resource →
relation managers → tests Pest → commit.
