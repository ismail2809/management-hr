# BUGS.md — Anomalies détectées & procédures de reproduction

> Audit statique de l'application (19 resources Filament, 5 relation managers, 8 widgets,
> 2 contrôleurs PDF, service d'import, modèles/policies/scopes, seeder de rôles).
> Chaque anomalie ci-dessous indique **où cliquer dans l'application** pour la constater.

**Légende du statut de vérification**

| Statut | Signification |
|---|---|
| ✅ **Vérifié** | Confirmé par exécution (requête DB, probe d'autorisation, calcul réel) |
| 📖 **Lu dans le code** | Déduit d'une lecture du code applicatif et/ou du code de Filament — non exécuté |
| ⚠️ **Latent** | Le défaut existe mais nécessite des conditions particulières pour se manifester |

---

## 0. Préparation de l'environnement de test

```bash
# Depuis WSL (les commandes PHP échouent depuis Windows sur un chemin UNC)
wsl -d Ubuntu
cd /var/www/management-hr

php artisan migrate:fresh --seed
php artisan serve
```

### Comptes disponibles après le seed

| Email | Mot de passe | Rôle | Panel |
|---|---|---|---|
| `admin@gestion-hr.ma` | `password` | `super-admin` | `/super-admin` **et** `/admin` |
| `secretaire@albaraime.ma` | `password` | `secretaire` | `/admin` |

> ⚠️ Le seed ne crée **aucun compte `directeur`, `surveillante` ni `enseignant`.
> `EmployeeUserSeeder` n'est pas appelé par `DatabaseSeeder`, et les 10 employés seedés
> n'ont pas d'adresse email (ce seeder filtre sur `whereNotNull('email')`).

### Créer les comptes de test manquants

Plusieurs scénarios en ont besoin. Créez ce fichier puis exécutez-le :

```bash
cat > /tmp/testusers.php <<'EOF'
<?php

use App\Models\EcoleSettings;
use App\Models\Employee;
use App\Models\User;

$ecoleId = EcoleSettings::withoutGlobalScopes()->value('id');

// Un enseignant lié à une fiche employé (obligatoire pour les rôles à accès limité)
$ens = Employee::withoutGlobalScopes()
    ->whereHas('profession', fn ($q) => $q->whereIn('name', ['Enseignant', 'Enseignante']))
    ->first();

$comptes = [
    ['directeur@test.ma',    'Directeur Test',    'directeur',    null],
    ['surveillante@test.ma', 'Surveillante Test', 'surveillante', null],
    ['enseignant@test.ma',   'Enseignant Test',   'enseignant',   $ens?->id],
];

foreach ($comptes as $c) {
    [$email, $name, $role, $empId] = $c;

    $u = User::withoutGlobalScopes()->updateOrCreate(
        ['email' => $email],
        [
            'name'             => $name,
            'password'         => bcrypt('password'),
            'ecole_setting_id' => $ecoleId,
            'employee_id'      => $empId,
        ]
    );

    $u->syncRoles([$role]);

    echo $email . ' / password  -> ' . $role . ($empId ? ' (employe #' . $empId . ')' : '') . PHP_EOL;
}
EOF
php artisan tinker /tmp/testusers.php
```

Sortie attendue :

```
directeur@test.ma / password  -> directeur
surveillante@test.ma / password  -> surveillante
enseignant@test.ma / password  -> enseignant (employe #2)
```

> ⚠️ Écrivez les `use` **un par ligne**. La syntaxe groupée `use App\Models\{User, Employee};`
> fait échouer PsySH avec `ParseError: syntax error, unexpected token "="`.

### URLs utiles (panel `/admin`)

| Module | URL |
|---|---|
| Tableau de bord | `/admin` |
| Employés | `/admin/employees` |
| Absences / Congés | `/admin/leaves` |
| Documents administratifs | `/admin/documents-administratifs` |
| Autres demandes | `/admin/autres-demandes` |
| Utilisateurs | `/admin/team` |
| Demandes (resource legacy, hors menu) | `/admin/document-requests` |
| Professions | `/admin/professions` |
| Types de congé | `/admin/leave-types` |

---

# 🔴 Anomalies critiques

## BUG-01 — Le `directeur` peut supprimer employés, congés, demandes et utilisateurs

✅ **Vérifié** · `HasRoleBasedDelete.php`, `EmployeeResource.php:330`, `LeaveResource.php:75`,
`DocumentAdministratifResource.php:95`, `AutreDemandeResource.php:113`, `UserResource.php:201`

### Cause

Tout le code protège la suppression via `canDelete()` / `canDeleteAny()` / `canForceDelete()`.
**Filament v4 n'appelle jamais ces méthodes** — 0 occurrence dans `vendor/filament/` :

```bash
grep -rn "canDeleteAny()" vendor/filament/ --include=*.php | grep -v "function canDeleteAny"
# (aucun résultat)
```

C'est donc la **policy** qui décide, et `RolesPermissionsSeeder.php:70-76` accorde
explicitement au `directeur` : `Delete:Employee`, `DeleteAny:Employee`, `Delete:Leave`,
`DeleteAny:Leave`, `Delete:DocumentRequest`, `Delete:User`, `Delete:LeaveType`.

### Reproduction (UI)

1. Créer le compte `directeur@test.ma` (voir § 0).
2. Se connecter sur `/admin` avec `directeur@test.ma` / `password`.
3. Aller sur `/admin/employees`.
4. Cocher la case d'une ligne → menu **Actions groupées** → **Supprimer** → confirmer.
5. **Observé** : l'employé disparaît de la liste (soft delete).
   **Attendu d'après le code** : action refusée (`canDeleteAny()` ne renvoie `true` que pour `super-admin`).
6. Même chose en ouvrant un employé → **Modifier** → le bouton **Supprimer** est présent et fonctionnel.

### Reproduction (preuve sans UI)

```bash
cat > /tmp/probe.php <<'EOF'
<?php
use App\Models\{Employee, User};
use Illuminate\Support\Facades\Gate;
$emp = Employee::withoutGlobalScopes()->first();
foreach (['super-admin','directeur','secretaire'] as $r) {
    $u = User::withoutGlobalScopes()->whereHas('roles', fn($q)=>$q->where('name',$r))->first();
    if (!$u) { echo "$r : aucun utilisateur\n"; continue; }
    printf("%-12s | policy delete=%-3s | Resource::canDelete()=%-3s\n", $r,
        Gate::forUser($u)->allows('delete', $emp) ? 'OUI':'non',
        (function() use ($u,$emp){ auth()->login($u); $r = \App\Filament\Admin\Resources\EmployeeResource::canDelete($emp); auth()->logout(); return $r?'OUI':'non'; })());
}
EOF
php artisan tinker /tmp/probe.php
```

Résultat obtenu :

```
super-admin  | policy delete=OUI | Resource::canDelete()=OUI
directeur    | policy delete=OUI | Resource::canDelete()=non   ← la policy gagne
secretaire   | policy delete=non | Resource::canDelete()=non
```

### Atténuation

Toutes les tables ont `SoftDeletes` : rien n'est perdu en base, mais les enregistrements
disparaissent de l'interface et **aucun écran de restauration n'existe**.

### Correctif suggéré

Corriger dans `RolesPermissionsSeeder` (retirer les `Delete*` du `directeur`), **pas** dans les
resources — ou ajouter `->visible(fn () => auth()->user()?->hasRole('super-admin'))` sur chaque
`DeleteAction` / `DeleteBulkAction`. Les méthodes `canDelete*` peuvent être supprimées : elles ne
servent à rien.

---

## BUG-02 — Aucun enseignant ne peut être affecté à un groupe / une classe

✅ **Vérifié** · `EmployeeResource.php:190`, `EmployeeResource.php:199`, `GroupesRelationManager.php:23`

### Cause

`ProfessionSeeder.php:20-30` crée les professions `Enseignante` et `Enseignant`.
**La profession `Professeur` n'existe pas.** Or trois emplacements comparent à la chaîne littérale `'Professeur'` :

| Fichier | Effet |
|---|---|
| `EmployeeResource.php:190` | La section **Affectations** n'apparaît jamais |
| `EmployeeResource.php:199` | Le select **Groupes affectés** n'apparaît jamais |
| `GroupesRelationManager.php:23` | L'onglet **Groupes affectés** n'apparaît sur **aucun** employé |

À l'inverse, `Employee::isProfesseur()` (`Employee.php:84`) et `LeaveResource.php:249` gèrent
correctement les trois orthographes — l'incohérence est donc limitée à ces 3 appels.

### Reproduction

1. Se connecter en `admin@gestion-hr.ma`.
2. `/admin/professions` → constater la liste : `Enseignante`, `Enseignant`, `Femme de ménage`,
   `Directeur`, `Chauffeur`, `Gardien`, `Assistante de transport`, `Surveillant général`, `Secrétaire`.
   **Il n'y a pas de `Professeur`.**
3. `/admin/employees` → ouvrir **Fatima BENNANI** (profession *Enseignante*).
4. **Observé** : aucun onglet « Groupes affectés » sur la fiche.
5. Cliquer **Modifier** → **Observé** : aucune section « Affectations ».
6. `/admin/groupes` → la colonne **Professeurs affectés** affiche `0` sur les 13 groupes.

### Reproduction de la cause (prouve le lien de causalité)

7. `/admin/professions/create` → créer une profession nommée exactement **`Professeur`**.
8. `/admin/employees` → modifier Fatima BENNANI → changer sa profession en `Professeur` → Enregistrer.
9. **Observé** : l'onglet « Groupes affectés » et la section « Affectations » apparaissent enfin.

### Correctif suggéré

Remplacer les 3 comparaisons littérales par `$record->isProfesseur()` /
`$ownerRecord->isProfesseur()`, qui couvre déjà `Professeur|Enseignant|Enseignante`.

---

## BUG-03 — Les boutons « Voir PDF » / « Télécharger PDF » n'apparaissent jamais

✅ **Vérifié** · `ViewDocumentAdministratif.php:22,30`, `ViewDocumentRequest.php:22,30`

### Cause

Les deux boutons sont conditionnés à `filled($this->record->generated_file_path)`.
Or cette colonne **n'est jamais écrite nulle part** :

```bash
grep -rn "generated_file_path" app/ database/ resources/ routes/
# → migration + $fillable + 4 lectures dans les conditions ->visible()
# → AUCUNE écriture
```

`DocumentPdfController` génère le PDF à la volée et ne persiste jamais de chemin.

### Conséquence

La route `documents.pdf` — **celle qui passe la demande en `approuvé`** — est **injoignable
depuis l'interface**. Le seul point d'entrée fonctionnel est l'action de ligne *Aperçu PDF*
(`DocumentAdministratifResource.php:258`), qui est en lecture seule.

### Reproduction

1. Se connecter en `secretaire@albaraime.ma`.
2. `/admin/documents-administratifs/create` → Employé : *Fatima BENNANI* ·
   Type : **Attestation de travail** · Format : **Version digitale (PDF)** ·
   Description : `test` → **Créer**.
3. Dans la liste, sur la ligne créée : menu **⋯** → **Approuver** → confirmer.
4. Menu **⋯** → **Voir** (page de détail).
5. **Observé** : seuls « Modifier » et le fil d'ariane sont présents.
   Aucun bouton « Voir PDF » ni « Télécharger PDF ».
   **Attendu** : pouvoir télécharger l'attestation.
6. Vérification en base :
   ```bash
   php artisan tinker --execute="echo App\Models\DocumentRequest::withoutGlobalScopes()->pluck('generated_file_path')->filter()->count();"
   # → 0
   ```

### Reproduction de la cause

7. Forcer une valeur quelconque :
   ```bash
   php artisan tinker --execute="App\Models\DocumentRequest::withoutGlobalScopes()->latest('id')->first()->update(['generated_file_path' => 'x']);"
   ```
8. Recharger la page de détail → **les deux boutons apparaissent** et fonctionnent.

### Correctif suggéré

Soit retirer la condition `generated_file_path` (le PDF est généré à la volée, le fichier n'a pas
besoin d'exister), soit persister le chemin dans `DocumentPdfController`.

---

## BUG-04 — 3 types de documents sur 6 ne peuvent pas produire de PDF, et le gabarit de repli est absent

✅ **Vérifié** · `DocumentPdfController.php:48`, `DocumentTypeSeeder.php`, `resources/views/pdf/documents/`

### Cause

| Type proposé dans le formulaire | Gabarit Blade présent ? |
|---|---|
| `attestation_travail`, `attestation_salaire`, `attestation_cnss` | ✅ |
| **`bulletin_paie`, `attestation_ir`, `credit_irrevocable`** | ❌ **absent** |
| `certificat_travail`, `ordre_mission` | gabarit présent mais **non seedé** → non sélectionnable |

`DocumentPdfController.php:48` bascule sur `pdf.documents.generic` si le gabarit manque.
**Ce fichier n'existe pas** :

```bash
ls resources/views/pdf/documents/
# attestation_cnss  attestation_salaire  attestation_travail  certificat_travail  ordre_mission
# → pas de generic.blade.php
```

### Reproduction

1. Connecté en `secretaire@albaraime.ma`, aller sur `/admin/documents-administratifs/create`.
2. Type de document → **Attestation IR** (ou *Bulletin de paie*, ou *Crédit irrévocable*) →
   remplir le reste → **Créer**.
3. Approuver la demande (menu **⋯** → **Approuver**).
4. **Observé** : l'action **Aperçu PDF** n'apparaît pas dans le menu (elle est protégée par
   `view()->exists(...)`), l'utilisateur n'a donc aucun moyen d'obtenir son document.
5. Forcer l'accès direct à la route pour voir l'erreur réelle — relever l'`id` de la demande puis :
   ```
   http://127.0.0.1:8000/documents/<id>/preview
   ```
   **Observé** : `InvalidArgumentException — View [pdf.documents.generic] not found.` (HTTP 500).
6. Constater l'inverse : la liste déroulante ne propose **ni** *Certificat de travail* **ni**
   *Ordre de mission*, alors que leurs gabarits existent.

### Correctif suggéré

Créer `resources/views/pdf/documents/generic.blade.php`, ajouter `certificat_travail` et
`ordre_mission` dans `DocumentTypeSeeder`, et désactiver (`active = false`) les types sans gabarit.

---

# 🟠 Anomalies majeures

## BUG-05 — Le tableau de bord gonfle le compteur « Congés en attente »

📖 **Lu dans le code** · `HrStatsOverview.php:28`

```php
$congesEnAttente = Leave::where('status', 'en_attente')->count();        // ← pas de filtre categorie
$absencesEnAttente = Leave::where('categorie','absence')->where('status','en_attente')->count();
```

« Congés en attente » compte donc **congés + absences**.

### Reproduction

1. Connecté en `secretaire@albaraime.ma` → `/admin/leaves/create`.
2. Créer une demande avec **Catégorie = Congé**, statut *En attente* → Enregistrer.
3. Créer une seconde demande avec **Catégorie = Absence**, statut *En attente* → Enregistrer.
4. Aller sur `/admin`.
5. **Observé** : tuile « Congés en attente » = **2**, tuile « Absences en attente » = **1**.
   **Attendu** : 1 et 1.

### Correctif suggéré

Ajouter `->where('categorie', 'conge')` sur la première requête.

---

## BUG-06 — Un `enseignant` peut auto-approuver sa demande / déposer au nom d'un autre

📖 **Lu dans le code** (mécanisme confirmé dans les sources de Filament, **exploit non rejoué**)
· `DocumentAdministratifResource.php:124-135`, `AutreDemandeResource.php:138-152`

### Cause

Les champs `status` et `employee_id` utilisent `->disabled($isEmployee)->dehydrated()`.

Dans Filament v4, `disabled()` pose `saved(false)` (`CanBeDisabled.php:24-25`) et la
déshydratation se résout ainsi (`HasState.php:765`) :

```php
$isDehydrated = $this->evaluate($this->isDehydrated) ?? $this->isSaved();
```

L'appel explicite à `->dehydrated()` **court-circuite la protection** : la valeur pilotée par le
client atteint `mutateFormDataBeforeCreate()` puis est persistée. Les sources de Filament
avertissent exactement de ce cas (`CanBeDisabled.php:18-22`) :

> *"skilled users can manipulate Livewire's JavaScript to bypass the disabled state on the client.
> Always enforce authorization on the backend."*

Or `CreateAutreDemande::mutateFormDataBeforeCreate()` ne force que `categorie` et
`ecole_setting_id` — **ni `status`, ni `employee_id`**.

### Reproduction

1. Créer le compte `enseignant@test.ma` (voir § 0) — il doit être lié à une fiche employé.
2. Se connecter avec `enseignant@test.ma` / `password`.
3. `/admin/autres-demandes/create` → remplir le formulaire.
   Constater que le select **Statut** est grisé et bloqué sur *En attente*.
4. Ouvrir la console du navigateur (F12) et forcer l'état Livewire :
   ```js
   Livewire.all()[0].set('data.status', 'approuvé')
   ```
5. Soumettre le formulaire.
6. **Attendu** : la demande est créée en *En attente*.
   **À vérifier** : si la demande est créée en *Approuvé*, la faille est confirmée.
7. Même manipulation avec `data.employee_id` pour déposer une demande au nom d'un collègue.

> L'étape 4 n'a pas été rejouée en conditions réelles ; seule la mécanique côté framework a été
> vérifiée dans le code. À confirmer avant de conclure.

### Correctif suggéré

Forcer les deux valeurs côté serveur dans les pages Create/Edit :

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    if (auth()->user()?->isBasicRole()) {
        $data['employee_id'] = auth()->user()->employee_id;
        $data['status']      = 'en_attente';
    }
    return $data;
}
```

---

## ~~BUG-07 — Fuite inter-écoles dans la liste « Rencontre direction »~~ → FAUX POSITIF

❌ **Invalidé par les tests** · `AutreDemandeResource.php:263-268`

### Ce qui avait été supposé

```php
Employee::with('profession')
    ->whereNotIn('profession_id', $excludedIds)
    ->orWhereNull('profession_id')     // ← OR supposé non groupé
```

On craignait que le `OR`, combiné au `CompanyScope`, produise
`(ecole_setting_id = 1 AND ...) OR profession_id IS NULL` et laisse fuiter les employés
d'une autre école.

### Ce que le test a démontré

Laravel **regroupe automatiquement** les clauses antérieures entre parenthèses au moment
d'appliquer un global scope (`Builder::applyScopes()` → `addNewWheresWithinGroup()`).
SQL réellement produit :

```sql
select * from `employees`
where (`profession_id` not in (?, ?) or `profession_id` is null)
  and `employees`.`deleted_at` is null
  and `employees`.`ecole_setting_id` = ?
```

**L'isolation est intacte, il n'y a rien à corriger.** Le test
`CompanyScopeTest::un_orWhere_ne_contourne_pas_lisolation_multi_ecoles` verrouille
désormais cette garantie.

> Envelopper la condition dans une closure resterait une bonne pratique défensive, mais
> ce n'est pas un correctif de sécurité.

---

## BUG-08 — Le badge « Utilisateurs » compte toutes les écoles

✅ **Vérifié** · `UserResource.php:35` vs `UserResource.php:44-52`

Le badge fait `User::count()` — le modèle `User` **n'a pas** de `CompanyScope` — alors que le
tableau filtre sur `ecole_setting_id`.

### Reproduction (aucune préparation, juste après le seed)

1. Se connecter en `secretaire@albaraime.ma`.
2. Regarder l'entrée **Utilisateurs** dans la barre latérale.
3. **Observé** : le badge affiche **2**.
4. Ouvrir `/admin/team`.
5. **Observé** : le tableau ne contient qu'**1 ligne** (le super-admin a `ecole_setting_id = NULL`,
   il est donc filtré du tableau mais compté dans le badge).

### Correctif suggéré

Réutiliser la requête filtrée : `return (string) static::getEloquentQuery()->count();`

---

## BUG-09 — Le compteur `nb_telechargements` ne s'incrémente jamais

📖 **Lu dans le code** · `DocumentAdministratifResource.php:270-277`

L'action possède **à la fois** `->url(...)` et `->action(...)`. Quand une URL est définie, le bouton
est rendu comme un lien : la closure `action()` n'est jamais exécutée.

### Reproduction

1. Connecté en `secretaire@albaraime.ma`, créer une demande de document, l'**Approuver**.
2. La **Modifier** → champ **Fichier final (uploadé par l'admin)** → téléverser un PDF → Enregistrer.
3. Dans la liste : menu **⋯** → **Télécharger fichier uploadé** → le fichier s'ouvre. Répéter 3 fois.
4. Vérifier :
   ```bash
   php artisan tinker --execute="echo App\Models\DocumentRequest::withoutGlobalScopes()->latest('id')->value('nb_telechargements');"
   ```
   **Observé** : `0`. **Attendu** : `3`.

---

## BUG-10 — Deux calculs de durée différents pour le même congé

✅ **Vérifié** (valeurs calculées) · `Leave.php:73` (`diffInWeekdays`) vs `ViewEmployee.php:96` (`diffInDays`)

| Période | Liste des congés (`diffInWeekdays+1`) | Profil employé (`diffInDays+1`) |
|---|---|---|
| ven. 18/09 → lun. 21/09 | **2 j** | **4 j** |
| lun. 14/09 → ven. 18/09 | 5 j | 5 j |
| sam. 19/09 → dim. 20/09 | **1 j** | **2 j** |

### Reproduction

1. Connecté en `secretaire@albaraime.ma` → `/admin/leaves/create`.
2. Employé *Fatima BENNANI*, Catégorie *Congé*, **du vendredi 18/09/2026 au lundi 21/09/2026**,
   statut **Approuvé** → Enregistrer.
3. `/admin/leaves` → colonne **Durée** → **Observé : `2 j`**.
4. `/admin/employees` → ouvrir *Fatima BENNANI* → bloc statistiques congés.
5. **Observé : `4 j`** pour le même congé.

### Correctif suggéré

Utiliser `$l->duration_days` (l'accesseur du modèle) dans `ViewEmployee::getLeaveStats()`.

---

## ~~BUG-11 — Profil employé : erreur 500 si `start_date` est nul~~ → NON ATTEIGNABLE

❌ **Requalifié par les tests** · `ViewEmployee.php:96`

Le défaut de code est réel — `getLeaveStats()` appelle `diffInDays()` sans garde de
nullité — mais **le scénario est inatteignable** : le schéma déclare
`leaves.start_date` et `leaves.end_date` en `DATE NOT NULL`. MySQL refuse d'y écrire
`NULL`, comme le prouve
`LeaveWorkflowTest::le_schema_interdit_une_date_de_conge_nulle`.

La garde de `Leave::castAttribute()` est donc purement défensive. **Rien à corriger** —
seul BUG-10 (divergence des deux calculs de durée) reste valable, et son correctif
(`$l->duration_days`) supprime au passage cette fragilité.

<details>
<summary>Analyse initiale conservée pour mémoire</summary>

Mesures hors base (objets construits en mémoire) :

| Cas | Résultat |
|---|---|
| `end_date` NULL | pas de crash — Carbon substitue `now()` → ~262 jours |
| `start_date` NULL | `Error: Call to a member function diffInDays() on null` |

Ces deux états ne peuvent pas provenir de la base.

</details>


## BUG-12 — Le `directeur` ne peut administrer aucune donnée de référence

✅ **Vérifié** · les 10 resources du groupe *Paramétrage* + `AuditLogResource`

Toutes déclarent `canViewAny() => auth()->user()?->hasRole('super-admin')` :
`AnneeScolaire`, `LeaveType`, `DocumentType`, `EmployeeDocumentType`, `NatureDocument`,
`CommunicationMethod`, `Profession`, `NiveauScolaire`, `Groupe`, `Transport`.

Cela **contredit** `RolesPermissionsSeeder`, qui accorde au `directeur` le CRUD complet sur
`LeaveType` (y compris `Delete:LeaveType`).

### Reproduction

1. Se connecter avec `directeur@test.ma` / `password`.
2. **Observé** : la barre latérale ne contient **aucun groupe « Paramétrage »**.
3. Forcer l'URL `/admin/leave-types` → **403 Forbidden**.
4. Pourtant :
   ```bash
   php artisan tinker --execute="echo App\Models\User::where('email','directeur@test.ma')->first()->can('Delete:LeaveType') ? 'OUI' : 'non';"
   # → OUI
   ```
   Le directeur a la permission de **supprimer** des types de congé mais ne peut pas les **voir**.

Même contradiction pour la `surveillante` : le seeder lui donne `ViewAny:Employee` et
`ViewAny:User`, mais `EmployeeResource.php:301` et `UserResource.php:195` la bloquent.

---

# 🟡 Anomalies mineures

## BUG-13 — La resource legacy `/admin/document-requests` reste accessible

📖 **Lu dans le code** · `DocumentRequestResource.php:37`

`$shouldRegisterNavigation = false` masque l'entrée de menu mais **les routes restent enregistrées**.
Cette 3ᵉ resource pointe sur le même modèle sans filtre `categorie` et sans les garde-fous des deux
resources scindées.

### Reproduction

1. Se connecter avec `surveillante@test.ma` / `password`.
2. `/admin/documents-administratifs` → **Observé** : pas de bouton « Créer » (lecture seule voulue,
   `DocumentAdministratifResource.php:78`).
3. Saisir directement `/admin/document-requests` dans la barre d'adresse.
4. **Observé** : la page s'ouvre, mélange documents **et** autres demandes, et le bouton
   **Créer** est disponible — le garde-fou est contourné.

---

## BUG-14 — Groupes de navigation non déclarés (icône et ordre perdus)

📖 **Lu dans le code** · `LeaveResource.php:66`, `AppPanelProvider.php:78-91`

`AppPanelProvider` déclare : `Personnel`, `Congés & Absences`, `Demandes`, `Administration`,
`Paramétrage`, `Journal d'audit`.
Mais `LeaveResource::getNavigationGroup()` renvoie **`Congés & Présence`** (nom différent), et
`Mes demandes` / `Rôles et Utilisateurs` ne sont pas déclarés non plus.

### Reproduction

1. Se connecter en `secretaire@albaraime.ma`.
2. Observer la barre latérale.
3. **Observé** : un groupe **« Congés & Présence » sans icône**, alors que le groupe déclaré
   « Congés & Absences » (avec `heroicon-o-calendar-days`) reste vide et n'apparaît pas.
   Idem pour « Rôles et Utilisateurs ».

---

## BUG-15/16/17 — Import Excel : trois pertes de données silencieuses

📖 **Lu dans le code** · `EmployeeImportService.php:186`, `:216`, `:245`

| # | Défaut | Ligne |
|---|---|---|
| 15 | Si la cellule de date est vide, le service lit **les 1 à 2 colonnes à gauche**. Un nombre > 1000 (un matricule !) est converti en date sérielle Excel → date de naissance fantaisiste. | `:186-196` |
| 16 | `isTitleRow()` ignore toute ligne dont le nom **contient** `nom` → un vrai patronyme comme **BE·NOM·AR** est supprimé sans avertissement. | `:216` |
| 17 | `parseCnss()` fait `(string)(int)$raw` → les zéros de tête sautent, une valeur non numérique devient `"0"`. | `:245` |

### Reproduction

Créer un `.xlsx` contenant exactement :

| Matricule | Nom | Prénom | CNSS | Date naissance |
|---|---|---|---|---|
| 45000 | BENOMAR | Youssef | 0123456 | *(laisser vide)* |
| 1002 | ALAMI | Salma | 0987654 | 15/03/1990 |

1. Se connecter en `secretaire@albaraime.ma` → `/admin/employees`.
2. Bouton **Importer Excel** → déposer le fichier → **Importer**.
3. **Observé (BUG-16)** : la notification annonce « 1 employé(e) importé(s), 1 ignoré(s) ».
   **BENOMAR a été supprimé silencieusement** car son nom contient `nom`.
4. Renommer `BENOMAR` en `BENALI` dans le fichier et réimporter.
5. **Observé (BUG-17)** : le CNSS de BENALI vaut `123456` (au lieu de `0123456`).
6. **Observé (BUG-15)** : sa date de naissance est renseignée alors que la cellule était vide —
   le matricule `45000` a été interprété comme un numéro de série Excel.

---

## BUG-18 — Rôle de repli `employee` inexistant

⚠️ **Latent** · `CreateUser.php:28`

```php
$role = $this->data['roles'] ?? 'employee';   // ← rôle supprimé par RolesPermissionsSeeder:97
```

Si `roles` est absent de l'état du formulaire, `syncRoles(['employee'])` lève
`Spatie\Permission\Exceptions\RoleDoesNotExist`. Le champ étant `required()`, le cas est difficile
à atteindre depuis l'interface — mais le repli est mort.

---

## BUG-19 — Méthodes Livewire de suppression de document non protégées

📖 **Lu dans le code** · `ViewEmployee.php:84-95`

`deleteDocument()` et `uploadDocument()` sont des méthodes Livewire **publiques** sans contrôle de
rôle, et le gabarit `view-employee.blade.php` **ne rend aucun bouton** qui les appelle. Interface
morte, mais méthodes invocables à distance par tout utilisateur pouvant ouvrir la page.

### Reproduction

1. Se connecter avec `enseignant@test.ma` (lié à une fiche employé).
2. Ouvrir sa propre fiche via **Mon profil** → onglet **Documents**.
3. Relever l'`id` d'un document en base, puis dans la console (F12) :
   ```js
   Livewire.all()[0].call('deleteDocument', <id>)
   ```
4. **À vérifier** : le document est supprimé alors qu'aucun bouton ne le permet dans l'interface.

---

## BUG-20 — Le super-admin édite toujours l'école n°1

📖 **Lu dans le code** · `EcoleSettings.php:60-69`

`EcoleSettings::get()` retombe sur `self::firstOrNew([])` quand l'utilisateur n'a pas de
`ecole_setting_id` — ce qui est **le cas du super-admin** (`ecole_setting_id = NULL`).
Avec plusieurs écoles, `/admin/ecole-settings` modifiera toujours la première.
Sans importance tant qu'il n'y a qu'une école.

---

## BUG-21 — Le tri « Date souhaitée » ignore les demandes de photocopie

📖 **Lu dans le code** · `AutreDemandeResource.php:346-351`

La colonne **affiche** `date_souhaitee ?? photocopie_date_souhaitee` via `getStateUsing()`, mais
`->sortable()` trie sur la seule colonne SQL `date_souhaitee`.

### Reproduction

1. `/admin/autres-demandes/create` → type **Photocopie** → renseigner *Date souhaitée* → Créer.
2. Créer une 2ᵉ demande de type **Formation** avec une date souhaitée antérieure.
3. Dans la liste, cliquer sur l'en-tête **Date souhaitée**.
4. **Observé** : la demande de photocopie se trie comme si sa date était vide (`NULL`), bien
   qu'une date s'affiche dans la cellule.

---

## BUG-22 — Bouton « Télécharger document » impossible à activer sur une autre demande

📖 **Lu dans le code** · `ViewAutreDemande.php:21`

Le bouton est conditionné à `fichier_final`, mais le formulaire de `AutreDemandeResource`
**ne contient pas ce champ** (il n'a que `fichier_joint`, `:295`). Seul
`DocumentAdministratifResource` expose l'upload de `fichier_final`.

### Reproduction

1. `/admin/autres-demandes` → ouvrir n'importe quelle demande → **Approuver** → **Voir**.
2. **Observé** : aucun bouton « Télécharger document », et aucun champ dans le formulaire
   d'édition ne permet de joindre le document final.

---

## BUG-23 — Import Excel : erreur 500 si le fichier n'est pas un vrai classeur

✅ **Vérifié** · `EmployeeImportService.php:52`, `ListEmployees.php:59-62`

`IOFactory::load($filePath)` n'est **pas** entouré d'un `try/catch`, ni dans le service ni dans
l'action Filament. Or `ListEmployees.php:40` accepte `application/octet-stream` parmi les types
MIME, ce qui laisse passer à peu près n'importe quel fichier.

Comportement mesuré :

| Fichier envoyé | Résultat |
|---|---|
| PDF renommé en `.xlsx` | `Exception: Unable to identify a reader for this file` → **500** |
| Binaire quelconque en `.xlsx` | `Exception: Unable to identify a reader for this file` → **500** |
| Fichier vide | pas d'erreur (0 importé) |
| Fichier texte | pas d'erreur (lu comme CSV) |

### Reproduction

1. Prendre n'importe quel PDF et le renommer en `employes.xlsx`.
2. Se connecter en `secretaire@albaraime.ma` → `/admin/employees`.
3. Bouton **Importer Excel** → déposer `employes.xlsx` → **Importer**.
4. **Observé** : page d'erreur Laravel `Unable to identify a reader for this file`.
   **Attendu** : une notification d'erreur lisible (« format de fichier non reconnu »).

### Correctif suggéré

```php
try {
    $result = $service->import($path, $companyId);
} catch (\Throwable $e) {
    Notification::make()->danger()
        ->title('Fichier illisible')
        ->body('Le fichier n\'est pas un classeur Excel valide.')->send();
    Storage::disk('local')->delete($data['file']);
    return;
}
```

> Noter aussi qu'en cas d'exception le fichier temporaire uploadé **n'est jamais supprimé**
> (`Storage::delete()` est appelé après l'import, `ListEmployees.php:66`).

---

## BUG-24 — 🔒 `APP_DEBUG` n'est jamais forcé à `false` pour la production

✅ **Vérifié** · `.env.example`, `.cpanel.yml`, `nixpacks.toml`

**C'est la raison pour laquelle une page de debug Laravel est visible en production.**

```bash
grep -E "^APP_(ENV|DEBUG)" .env.example
# APP_ENV=local
# APP_DEBUG=true      ← valeur livrée par défaut

grep -in "APP_DEBUG\|APP_ENV" .cpanel.yml nixpacks.toml Dockerfile docker/*
# (aucun résultat — aucun script de déploiement ne force APP_DEBUG=false)
```

Si le `.env` du serveur a été copié depuis `.env.example`, la production tourne avec
`APP_DEBUG=true`. Chaque erreur 500 affiche alors la page Whoops, qui expose **la totalité des
variables d'environnement** : identifiants MySQL, `APP_KEY`, identifiants SMTP, chemins serveur,
et le code source autour de l'exception.

### Reproduction

1. Déclencher n'importe quelle erreur 500 en production (le plus simple : BUG-04, en ouvrant
   `/documents/<id>/preview` sur une demande de type *Attestation IR*).
2. **Observé** : page d'erreur Laravel détaillée au lieu d'une page 500 générique.
3. Faire défiler jusqu'à l'onglet **Environment & details** / la section des variables.

### Correctif (à faire en premier, avant tout le reste)

Sur le serveur de production :

```env
APP_ENV=production
APP_DEBUG=false
```

puis `php artisan config:cache`. Et **changer `APP_KEY` et le mot de passe MySQL** s'il est
avéré que la page de debug a été exposée publiquement.

> ⚠️ Le `.env` de développement de ce dépôt contient un mot de passe MySQL en clair
> (`DB_PASSWORD`). Vérifier qu'il n'a jamais été commité et que la production utilise un autre
> mot de passe.

---

## BUG-25 — Erreur 500 au lieu d'une redirection quand la session a expiré

✅ **Vérifié par test automatisé** · `routes/web.php:24-30`

Les routes PDF sont protégées par le middleware `auth`. Quand un visiteur non authentifié
les atteint, Laravel appelle `route('login')` pour construire la redirection. Or **aucune
route ne porte ce nom** dans l'application :

```bash
php artisan route:list | grep -i login
#  admin/login        filament.app.auth.login
#  super-admin/login  filament.admin.auth.login
```

→ `RouteNotFoundException: Route [login] not defined.` → **HTTP 500**.

### Reproduction

1. Se connecter, ouvrir une demande de document, copier l'URL
   `/documents/<id>/preview`.
2. Se déconnecter (ou attendre l'expiration de session, ou ouvrir l'URL en navigation privée).
3. Charger l'URL.
4. **Observé** : page d'erreur Laravel `Route [login] not defined`.
   **Attendu** : redirection vers `/admin/login`.

C'est le scénario le plus probable en production : un lien PDF ouvert depuis un favori ou
un e-mail après expiration de la session.

Test : `CrashRegressionTest::bug25_un_visiteur_anonyme_doit_etre_redirige_et_non_planter`.

### Correctif suggéré

Dans `AppServiceProvider::boot()` :

```php
\Illuminate\Auth\Middleware\Authenticate::redirectUsing(
    fn () => route('filament.app.auth.login')
);
```

---

## BUG-26 — Deux gabarits PDF plantent : un tiret cadratin nu dans une expression Blade

✅ **Vérifié par test automatisé** · `certificat_travail.blade.php:57`, `ordre_mission.blade.php:59`

```blade
<div class="row"><span class="label">Département :</span><span class="value">{{ — }}</span></div>
```

`{{ — }}` compile en `<?php echo e(—); ?>` : PHP interprète le tiret cadratin comme une
**constante** et lève `Error: Undefined constant "—"`. Vestige de la table `departments`
supprimée avec l'ancien SaaS de paie.

Sur les 8 types de documents :

| État | Types |
|---|---|
| ✅ fonctionnels | `attestation_travail`, `attestation_salaire`, `attestation_cnss` |
| ❌ gabarit absent (BUG-04) | `bulletin_paie`, `attestation_ir`, `credit_irrevocable` |
| ❌ **gabarit cassé (BUG-26)** | `certificat_travail`, `ordre_mission` |

**Aucun type sur 8 n'est fiable au-delà des 3 premiers.**

### Reproduction

1. Ces deux types ne sont pas seedés par `DocumentTypeSeeder` — vérifier d'abord s'ils sont
   actifs en production :
   ```bash
   php artisan tinker --execute="echo App\Models\DocumentType::withoutGlobalScopes()->whereIn('code',['certificat_travail','ordre_mission'])->where('active',true)->count();"
   ```
2. S'ils le sont, les sélectionner dans `/admin/documents-administratifs/create`, approuver,
   puis cliquer **Aperçu PDF**.
3. Sinon, forcer le type sur une demande existante puis ouvrir `/documents/<id>/preview`.
4. **Observé** : `Undefined constant "—"` (HTTP 500).

Tests : `CrashRegressionTest::bug26_*`.

### Correctif suggéré

Sortir le tiret des accolades dans les deux fichiers :

```blade
<span class="value">—</span>
```

---

# 🚨 Quelles anomalies produisent une page d'erreur 500 ?

Sous-ensemble des anomalies ci-dessus capables de faire apparaître la page de debug Laravel,
classées par probabilité de survenue réelle.

| # | Déclencheur | Exception | Reproductible ? |
|---|---|---|---|
| **25** | Ouvrir un lien PDF après expiration de la session | `RouteNotFoundException: Route [login] not defined` | ✅ très facile |
| **26** | Générer un `certificat_travail` ou un `ordre_mission` | `Error: Undefined constant "—"` | ✅ facile |
| **04** | Générer un `bulletin_paie`, `attestation_ir` ou `credit_irrevocable` | `InvalidArgumentException: View [pdf.documents.generic] not found` | ✅ facile |
| **23** | Importer un fichier qui n'est pas un vrai classeur (PDF renommé…) | `Exception: Unable to identify a reader for this file` | ✅ facile |
| **18** | `CreateUser` / `EditUser` si `roles` est absent de l'état du formulaire | `RoleDoesNotExist: There is no role named 'employee'` | ⚠️ difficile (champ `required`) |
| — | Créer un enregistrement alors que la table `ecole_settings` est vide | `QueryException` (contrainte NOT NULL sur `ecole_setting_id`) | ⚠️ base non seedée uniquement |

> **BUG-25 + BUG-26 + BUG-04 se cumulent sur le même écran** : la génération de documents
> est la fonction la plus exposée de l'application, et 5 types de documents sur 8 y
> échouent par une erreur 500.

**Ne produisent PAS de 500** (vérifié par test, contrairement à ce qu'on pourrait croire) :

- Un employé **soft-deleted** rend la relation `employee` nulle dans `DocumentPdfController`,
  mais les gabarits PDF ne font que **lire** des propriétés → simples *Warning* PHP, champs vides
  dans le document, pas de plantage.
- **BUG-11 est inatteignable** : `leaves.start_date` et `end_date` sont `DATE NOT NULL`,
  MySQL refuse d'y écrire NULL.
- Un fichier texte ou vide envoyé à l'import : lu comme CSV, aucune erreur.
- Un accès inter-écoles à un PDF renvoie **404** (route-model binding filtré par le
  `CompanyScope`), pas 500 ni 403.

---

# Récapitulatif

| # | Anomalie | Gravité | Statut |
|---|---|---|---|
| 01 | `directeur` peut supprimer employés / congés / demandes / users | 🔴 | ✅ Vérifié |
| 02 | Aucun enseignant affectable à un groupe (`Professeur` inexistant) | 🔴 | ✅ Vérifié |
| 03 | Boutons PDF jamais visibles (`generated_file_path` jamais écrit) | 🔴 | ✅ Vérifié |
| 04 | 3 types de documents sans gabarit + gabarit `generic` absent | 🔴 | ✅ Vérifié |
| 05 | « Congés en attente » compte aussi les absences | 🟠 | 📖 Lu |
| 06 | Auto-approbation possible (`disabled()->dehydrated()`) | 🟠 | 📖 Lu |
| ~~07~~ | ~~Fuite inter-écoles (`orWhere` non groupé)~~ | — | ❌ **Faux positif** |
| 08 | Badge « Utilisateurs » non filtré par école | 🟠 | ✅ Vérifié |
| 09 | `nb_telechargements` jamais incrémenté | 🟠 | 📖 Lu |
| 10 | Deux calculs de durée divergents | 🟠 | ✅ Vérifié |
| ~~11~~ | ~~Profil employé : 500 si date nulle~~ | — | ❌ **Non atteignable** |
| 12 | `directeur` privé de tout le Paramétrage | 🟠 | ✅ Vérifié |
| 13 | Resource legacy `/admin/document-requests` accessible | 🟡 | 📖 Lu |
| 14 | Groupes de navigation non déclarés | 🟡 | 📖 Lu |
| 15-17 | Import Excel : 3 pertes de données silencieuses | 🟡 | 📖 Lu |
| 18 | Rôle de repli `employee` inexistant | 🟡 | ⚠️ Latent |
| 19 | Méthodes Livewire de suppression non protégées | 🟡 | 📖 Lu |
| 20 | Super-admin édite toujours l'école n°1 | 🟡 | 📖 Lu |
| 21 | Tri « Date souhaitée » incohérent | 🟡 | 📖 Lu |
| 22 | « Télécharger document » inactivable sur autre demande | 🟡 | 📖 Lu |
| 23 | Import Excel : 500 si le fichier n'est pas un vrai classeur | 🟠 | ✅ Vérifié |
| 24 | 🔒 `APP_DEBUG` jamais forcé à `false` en production | 🔴 | ✅ Vérifié |
| 25 | 500 au lieu d'une redirection quand la session expire | 🔴 | ✅ Test auto |
| 26 | `{{ — }}` casse 2 gabarits PDF sur 5 | 🔴 | ✅ Test auto |

**Ordre de traitement conseillé**

1. **BUG-24** en premier : tant que `APP_DEBUG=true` en production, chaque 500 publie les
   identifiants de la base. Correction de configuration, sans déploiement de code.
2. **BUG-26** puis **BUG-25** : deux correctifs d'une ligne qui suppriment les erreurs 500
   les plus fréquemment rencontrées par les utilisateurs.
3. **BUG-04** : créer le gabarit de repli manquant.
4. **BUG-01** et **BUG-02** : corrections d'une ligne, très fort impact fonctionnel.
5. **BUG-03** + **BUG-04** + **BUG-26** expliquent ensemble toute plainte du type
   *« je n'arrive pas à télécharger mon attestation »*.

Chaque anomalie encore ouverte dispose d'un test dans `tests/Feature/Bugs/` :
`php artisan test --group=bugs`. Le compteur d'échecs descend à mesure des correctifs.
