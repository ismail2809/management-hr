# GestionHR — Récapitulatif des Modules

> SaaS RH Maroc · Laravel 12 + Filament v4 · Panel unique `/app`
> Multi-tenancy logique par `company_id` · Conformité CNSS / IR / AMO

---

## Accès par rôle

| Rôle         | Accès                                                  |
|--------------|--------------------------------------------------------|
| `super-admin`| Tout (Administration + tous les modules RH)            |
| `admin`      | Tous les modules RH de sa company                      |
| `rh`         | Tous les modules RH de sa company                      |
| `manager`    | Tous les modules RH de sa company                      |
| `comptable`  | Tous les modules RH de sa company                      |

---

## 1. Administration *(super-admin uniquement)*

### 1.1 Companies — `CompanyResource`
Gestion de toutes les entreprises clientes du SaaS.

| Champ                        | Description                              |
|------------------------------|------------------------------------------|
| Raison sociale               | Nom légal de l'entreprise                |
| ICE / RC / Patente           | Identifiants fiscaux marocains           |
| N° affiliation CNSS          | Numéro CNSS patronal                     |
| Ville / Email / Téléphone    | Coordonnées de l'entreprise              |

**Actions** : Créer, lire, modifier, supprimer (CRUD complet).

---

### 1.2 Utilisateurs — `UserResource`
Gestion des comptes utilisateurs de toutes les companies.

| Champ      | Description                                        |
|------------|----------------------------------------------------|
| Nom        | Nom complet de l'utilisateur                       |
| Email      | Identifiant de connexion                           |
| Rôle       | `super-admin`, `admin`, `rh`, `manager`, `comptable` |
| Company    | Entreprise rattachée                               |
| Employé lié| Liaison optionnelle à une fiche employé            |

**Actions** : CRUD complet + réinitialisation de mot de passe.

---

### 1.3 Taux CNSS / AMO — `CnssRateResource`
Paramètres légaux de cotisation sociale (globaux ou par company).

| Champ              | Description                                      |
|--------------------|--------------------------------------------------|
| Type               | `employee` (salarié) ou `employer` (patronal)    |
| Label              | CNSS / AMO / CIMR / Formation professionnelle    |
| Taux (%)           | Pourcentage de cotisation                        |
| Plafond (MAD/mois) | Base de calcul plafonnée                         |
| Company            | NULL = global, sinon spécifique à une company    |

**Taux légaux en vigueur** :
- CNSS salarié : **4.48%** (plafond 6 000 MAD)
- CNSS patronal : **10.77%**
- AMO salarié : **2.26%** | AMO patronal : **4.11%**

---

### 1.4 Barème IR — `IrBracketResource`
Tranches progressives de l'impôt sur le revenu (table globale, pas de company_id).

| Champ              | Description                              |
|--------------------|------------------------------------------|
| Salaire min (MAD/an) | Borne inférieure de la tranche         |
| Salaire max (MAD/an) | Borne supérieure (null = illimité)     |
| Taux (%)           | Taux marginal de la tranche              |
| Déduction fixe     | Montant à déduire de l'impôt brut        |

**Règle** : déduction forfaitaire frais pro 20% (plafond 30 000 MAD/an) avant application du barème.

---

## 2. Organisation

### 2.1 Départements — `DepartmentResource`
Structure organisationnelle de l'entreprise.

| Champ      | Description                              |
|------------|------------------------------------------|
| Nom        | Intitulé du département                  |
| Responsable| Employé désigné comme manager            |
| Effectif   | Nombre d'employés (calculé)              |

**Actions** : CRUD complet · Filtre par nom · Tri alphabétique.

---

### 2.2 Postes — `PositionResource`
Référentiel des fonctions et grilles salariales.

| Champ           | Description                           |
|-----------------|---------------------------------------|
| Intitulé        | Titre du poste (ex: Ingénieur, RH)    |
| Salaire de base | Salaire brut de référence en MAD      |

**Actions** : CRUD complet · Recherche par intitulé.

---

## 3. Employés

### 3.1 Fiche Employé — `EmployeeResource`
Dossier complet de chaque salarié, isolé par company.

**Identité**
| Champ                | Description                          |
|----------------------|--------------------------------------|
| Matricule            | Identifiant interne                  |
| Prénom / Nom         | État civil                           |
| CIN                  | Carte d'identité nationale           |
| N° CNSS              | Numéro d'affiliation salarié         |
| RIB                  | Coordonnées bancaires                |
| Email / Téléphone    | Contact                              |
| Ville / Adresse      | Résidence                            |
| Date de naissance    |                                      |
| Date d'embauche      |                                      |

**Affectation**
| Champ            | Description                             |
|------------------|-----------------------------------------|
| Type de contrat  | CDI / CDD / Stage / ANAPEC              |
| Statut           | Actif / Inactif / Suspendu              |
| Département      | Lien vers DepartmentResource            |
| Poste            | Lien vers PositionResource              |

**Situation familiale**
| Champ                | Description                          |
|----------------------|--------------------------------------|
| Situation familiale  | Célibataire / Marié / Divorcé / Veuf |
| Nombre d'enfants     | Pour déductions fiscales IR          |

**Photo** : upload image, recadrage circulaire, stockage `public/employees/photos`.

**Actions** : Créer, voir (profil complet), modifier · Filtres : statut, type contrat, département · Tri par nom.

---

## 4. Paie

### 4.1 Contrats — `ContractResource`
Historique des contrats de travail par employé.

| Champ                  | Description                          |
|------------------------|--------------------------------------|
| Employé                | Lien vers fiche employé              |
| Type de contrat        | CDI / CDD / Stage / ANAPEC           |
| Statut                 | Actif / Terminé / Suspendu           |
| Date de début          | Prise d'effet                        |
| Date de fin            | Null pour un CDI                     |
| Fin période d'essai    | Optionnelle                          |
| Salaire de base (MAD)  | Rémunération contractuelle           |
| Heures / semaine       | Par défaut 44h (droit marocain)      |

**Actions** : CRUD complet · Filtres : statut, type · Tri par date de début.

---

### 4.2 Fiches de paie — `PayrollResource`
Calcul et gestion des bulletins de salaire mensuels.

**Données de paie**
| Champ                | Description                          |
|----------------------|--------------------------------------|
| Employé              | Lien vers fiche employé              |
| Mois / Année         | Période de paie                      |
| Salaire brut (MAD)   | Base de calcul                       |
| Statut               | Brouillon → Validé → Payé            |

**Heures supplémentaires & Prorata**
| Champ             | Description                             |
|-------------------|-----------------------------------------|
| Heures sup.       | Nombre d'heures au-delà du contrat      |
| Montant heures sup| Calculé automatiquement                 |
| Mois incomplet    | Mode prorata : jours travaillés / ouvrables |

**Résultat du calcul (calculé par `PayrollCalculator`)**
| Champ                 | Description                       |
|-----------------------|-----------------------------------|
| CNSS salarié          | 4.48% plafonné 6 000 MAD          |
| AMO salarié           | 2.26%                             |
| CNSS patronal         | 10.77%                            |
| AMO patronal          | 4.11%                             |
| IR                    | Barème progressif après déductions|
| **Salaire net**       | Brut − CNSS − AMO − IR           |

**Composantes (Repeater)**
| Type      | Description                              |
|-----------|------------------------------------------|
| Prime     | Prime de transport, rendement, etc.      |
| Retenue   | Absence, avance sur salaire, etc.        |
| Avantage  | Voiture, logement, téléphone             |
| Imposable | Toggle : inclus dans la base IR ou non   |

**Actions par fiche** :
- **Recalculer** — recalcul CNSS/AMO/IR sur brouillon
- **Valider** — passage de brouillon → validé
- **Marquer payé** — passage de validé → payé
- **Bulletin PDF** — téléchargement du bulletin individuel

**Actions groupées** :
- **Télécharger bulletins (ZIP)** — export PDF multi-employés

---

### 4.3 Génération groupée — Page `GeneratePayroll`
Page dédiée à la génération de paie pour tous les employés d'un mois donné en un clic.

---

## 5. Congés & Présence

### 5.1 Types de congé — `LeaveTypeResource`
Référentiel des catégories de congé de l'entreprise.

| Champ                 | Description                          |
|-----------------------|--------------------------------------|
| Nom                   | Annuel / Maladie / Maternité / Sans solde |
| Jours légaux par an   | Quota légal du type de congé         |

**Actions** : CRUD complet.

---

### 5.2 Demandes de congé — `LeaveResource`
Workflow de demande et d'approbation des congés.

| Champ          | Description                                   |
|----------------|-----------------------------------------------|
| Employé        | Demandeur                                     |
| Type de congé  | Lien vers LeaveTypeResource                   |
| Date de début  |                                               |
| Date de fin    |                                               |
| Durée (jours)  | Calculée automatiquement                      |
| Motif          | Optionnel                                     |
| Statut         | En attente → Approuvé / Refusé                |
| Approuvé par   | Utilisateur ayant traité la demande           |

**Actions inline** :
- **Approuver** — visible si `en_attente` → enregistre approbateur + date
- **Refuser** — visible si `en_attente` → enregistre refuseur + date

**Filtres** : statut, type de congé · Tri par date de début décroissant.

---

### 5.3 Présences / Pointage — `AttendanceResource`
Suivi quotidien des entrées/sorties des employés.

| Champ                 | Description                          |
|-----------------------|--------------------------------------|
| Employé               |                                      |
| Date                  | Jour concerné                        |
| Heure d'arrivée       | `check_in`                           |
| Heure de départ       | `check_out`                          |
| Heures travaillées    | Calculé auto à la sauvegarde         |
| Heures supplémentaires| Au-delà de 8h/jour, calculé auto     |

**Calcul automatique** : hook `saving` sur le modèle `Attendance` recalcule `worked_hours` et `overtime_hours`.

**Filtres** : employé, plage de dates (du … au) · Tri par date décroissant.

---

## 6. Légal

### 6.1 Déclarations — `DeclarationResource`
Suivi des déclarations sociales et fiscales périodiques.

| Champ          | Description                                   |
|----------------|-----------------------------------------------|
| Type           | CNSS / IR / État 9421                         |
| Mois / Année   | Période de la déclaration                     |
| Statut         | En cours → Générée → Soumise                  |
| Fichier généré | Chemin vers le fichier exporté                |

**Actions** :
- **Marquer soumise** — visible si statut `générée`

**Filtres** : type, statut · Tri par date de création décroissant.

---

### 6.2 Demandes de documents — `DocumentRequestResource`
Gestion des demandes de documents administratifs par les employés.

**Types de documents disponibles** :
- Attestation de travail
- Attestation de salaire
- Attestation d'ancienneté
- Certificat de présence
- Ordre de mission

| Champ      | Description                                   |
|------------|-----------------------------------------------|
| Employé    | Demandeur                                     |
| Type       | Nature du document demandé                    |
| Format     | Digitale (PDF) ou Papier                      |
| Remarques  | Précisions optionnelles                       |
| Statut     | En attente → Traité / Refusé                  |

**Actions** :
- **Télécharger PDF** — génère et télécharge le document (format digital)
- **Aperçu PDF** — prévisualisation dans un nouvel onglet
- **Marquer imprimé** — pour les demandes papier, passe à `traité`
- **Refuser** — visible si `en_attente`

**Filtres** : statut, format · Tri par date décroissant.

---

## Résumé des modules

| Module                  | Resource / Page             | Groupe nav.     | Rôles autorisés      |
|-------------------------|-----------------------------|-----------------|----------------------|
| Companies               | `CompanyResource`           | Administration  | super-admin          |
| Utilisateurs            | `UserResource`              | Administration  | super-admin          |
| Taux CNSS/AMO           | `CnssRateResource`          | Administration  | super-admin          |
| Barème IR               | `IrBracketResource`         | Administration  | super-admin          |
| Départements            | `DepartmentResource`        | Organisation    | Tous                 |
| Postes                  | `PositionResource`          | Organisation    | Tous                 |
| Employés                | `EmployeeResource`          | Employés        | Tous                 |
| Contrats                | `ContractResource`          | Paie            | Tous                 |
| Fiches de paie          | `PayrollResource`           | Paie            | Tous                 |
| Génération groupée      | `GeneratePayroll` (page)    | Paie            | Tous                 |
| Types de congé          | `LeaveTypeResource`         | Congés & Présence | Tous               |
| Demandes de congé       | `LeaveResource`             | Congés & Présence | Tous               |
| Présences / Pointage    | `AttendanceResource`        | Congés & Présence | Tous               |
| Déclarations            | `DeclarationResource`       | Légal           | Tous                 |
| Demandes de documents   | `DocumentRequestResource`   | Légal           | Tous                 |

---

## Architecture technique

```
app/Filament/
├── Admin/Resources/          # Ressources super-admin uniquement
│   ├── CompanyResource.php
│   ├── UserResource.php
│   ├── CnssRateResource.php
│   └── IrBracketResource.php
└── App/Resources/            # Ressources RH (tous rôles)
    ├── EmployeeResource.php
    ├── DepartmentResource.php
    ├── PositionResource.php
    ├── ContractResource.php
    ├── PayrollResource.php
    ├── LeaveTypeResource.php
    ├── LeaveResource.php
    ├── AttendanceResource.php
    ├── DeclarationResource.php
    └── DocumentRequestResource.php

app/Services/
└── PayrollCalculator.php     # Calcul CNSS / AMO / IR

app/Models/Scopes/
└── CompanyScope.php          # Isolation multi-tenant (bypass pour super-admin)
```

---

## Services métier

### `PayrollCalculator`
Service dédié au calcul de la paie marocaine.

**Ordre de calcul** :
1. Salaire brut + primes imposables + avantages
2. Déduction CNSS salarié (4.48%, plafond 6 000 MAD)
3. Déduction AMO salarié (2.26%)
4. Base IR = brut imposable − CNSS − AMO − frais pro 20% (max 30 000/an)
5. IR = application du barème `ir_brackets`
6. Salaire net = brut − CNSS − AMO − IR + avantages non imposables

**Chargement des taux** : priorité company > global, fallback sur constantes codées.
