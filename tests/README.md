# Suite de tests

## Lancer les tests

```bash
# depuis WSL — les commandes PHP échouent depuis Windows sur un chemin UNC
wsl -d Ubuntu
cd /var/www/management-hr

php artisan test                      # suite principale : doit être 100 % verte
php artisan test --group=bugs         # régressions ouvertes : ÉCHOUE par construction
php artisan test --testsuite=Unit
php artisan test tests/Feature/Leave/LeaveWorkflowTest.php
php artisan test --filter=duree
```

## Base de données

Les tests tournent sur **MySQL**, base dédiée `management_hr_test`, et non sur SQLite :
le PHP de cet environnement n'embarque aucun driver sqlite
(`PDO::getAvailableDrivers()` ne renvoie que `mysql`).

Création unique de la base :

```bash
mysql -uroot -p -e "CREATE DATABASE management_hr_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

`RefreshDatabase` migre une fois par exécution puis enveloppe chaque test dans une
transaction. La première migration prend ~90 s ; les tests suivants tournent en ~0,2 s.
La base de développement `management-hr` n'est jamais touchée.

## Organisation

```
tests/
├── TestCase.php                     helpers partagés (voir plus bas)
├── Unit/                            pas d'accès base
│   ├── LeaveDurationTest            accesseur duration_days (jours ouvrables)
│   ├── SafeFileUploadRuleTest       liste noire d'extensions
│   └── DocumentRequestTypeLabelTest libellés + cohérence des catalogues
└── Feature/
    ├── Auth/
    │   ├── PanelAccessTest          accès HTTP aux deux panels, RequireAppRole
    │   └── RolePermissionMatrixTest matrice rôles × permissions du seeder
    ├── MultiTenancy/
    │   ├── CompanyScopeTest         isolation ecole_setting_id NOT NULL
    │   └── GlobalOrCompanyScopeTest tables de référence NULLABLE
    ├── Employee/
    │   ├── EmployeeModelTest        uuid, full_name, isProfesseur, soft delete, audit
    │   └── EmployeeImportServiceTest import Excel (vrais fichiers .xlsx générés)
    ├── Leave/LeaveWorkflowTest      cycle approbation / refus
    ├── Document/
    │   ├── DocumentPdfControllerTest autorisations, liste blanche, effet de bord
    │   └── DocumentRequestCategorieTest scission document/autre, conflits photocopie
    ├── Filament/ResourceVisibilityTest visibilité des resources par rôle
    └── Bugs/                        ⚠️ #[Group('bugs')] — échouent volontairement
        ├── CrashRegressionTest      anomalies produisant une erreur 500
        └── BehaviourRegressionTest  anomalies fonctionnelles
```

## La suite « bugs »

Les tests de `tests/Feature/Bugs/` décrivent le comportement **attendu** d'anomalies
documentées dans [`BUGS.md`](../BUGS.md), pas le comportement actuel. Ils sont donc
**rouges tant que le correctif n'est pas livré**, et exclus du lot par défaut via
`<groups><exclude>` dans `phpunit.xml`.

Chaque test nomme son anomalie (`bug04_…`, `bug25_…`) et son correctif en docblock.
Quand vous corrigez un bug, le test correspondant passe au vert : c'est le critère
d'acceptation. Déplacez-le alors vers la suite principale.

État actuel : **22 échecs attendus**.

## Helpers de `Tests\TestCase`

```php
$this->seedRoles();                      // joue le VRAI RolesPermissionsSeeder
$this->ecole(['nom_ecole' => 'A']);      // crée une école
$this->defaultEcole();                   // école par défaut, mémorisée
$this->profession('Enseignante');        // profession rattachée à l'école courante
$this->employee(['first_name' => 'X']);  // employé rattaché à l'école courante
$this->userWithRole('directeur');        // utilisateur + rôle (sans authentifier)
$this->actingAsRole('directeur');        // idem + authentification
```

`seedRoles()` exécute le seeder de production : les tests valident la configuration
réellement livrée, et non une copie de circonstance. Il crée en plus le rôle
`super-admin`, absent de ce seeder (il vient de `SuperAdminSeeder` en production).

## Pièges rencontrés — à connaître avant d'ajouter des tests

| Piège | Conséquence |
|---|---|
| `Model::withoutGlobalScopes()` retire **aussi** le `SoftDeletingScope` | inutilisable pour vérifier une suppression douce — utiliser `Model::find()` / `withTrashed()` |
| Authentifier plusieurs utilisateurs dans un même test HTTP | `AuthenticateSession` invalide la session → 302 erratiques vers `/admin/login`. Utiliser un `#[DataProvider]`, un rôle par test |
| Affecter une date illisible à un modèle | le mutateur d'Eloquent lève `InvalidFormatException`. La garde de `Leave::castAttribute()` ne couvre que la **lecture** — utiliser `setRawAttributes()` |
| Ligne entièrement vide dans un `.xlsx` de test | PhpSpreadsheet ne la matérialise pas : mettre au moins une cellule non vide |
| Accès inter-écoles via une route | renvoie **404** (route-model binding filtré par le scope), pas 403 |
| `Delete*` dans un docblock PHP | `*/` ferme le commentaire — écrire « les permissions Delete » en toutes lettres |
