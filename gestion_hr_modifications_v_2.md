# GestionHR — Plan des Modifications (V2)

> Objectif : Améliorer la robustesse légale, la sécurité et la scalabilité SaaS de l’application RH Maroc.

---

# 1️⃣ Sécurité & Permissions (PRIORITÉ HAUTE)

## 🔒 Implémenter permissions fines (Spatie Permission)
utilise un plugin filament v 4 c mieux
### À faire :
- Remplacer logique basée uniquement sur `role`
- Créer permissions par module :

```
view employees
create employees
edit employees
delete employees

view payroll
create payroll
validate payroll
mark payroll paid

manage declarations
approve leaves
view attendance
```

### Objectif :
- RH → gestion employés + congés
- Manager → validation congés uniquement
- Comptable → gestion paie + déclarations
- Admin → accès complet

---

# 2️⃣ Module Paie — Améliorations légales

## 🧮 2.1 Prime d’ancienneté automatique

### Règles Maroc :
- 5 ans → 5%
- 12 ans → 10%
- 20 ans → 15%
- 25 ans → 20%

### À implémenter :
- Calcul automatique selon date d’embauche
- Intégration dans `PayrollCalculator`
- Affichage distinct dans bulletin

---

## 🔁 2.2 Snapshot des taux

### Problème :
Les taux CNSS/AMO peuvent changer.

### Solution :
Stocker dans `payrolls` :
- cnss_employee_rate
- cnss_employer_rate
- amo_employee_rate
- amo_employer_rate
- ir_rate_applied

Objectif : éviter modification rétroactive.

---

## 📅 2.3 Lier Congés & Absences à la Paie

### À faire :
- Déduire automatiquement absences non payées
- Intégrer jours validés dans calcul prorata
- Bloquer génération paie si congés non traités

---

## ⏱ 2.4 Heures supplémentaires conformes

### Corriger :
- Base 44h / semaine (pas 8h fixe)
- Majoration 25% ou 50%
- Gestion jours fériés marocains

---

## 🔐 2.5 Verrouillage paie

Quand statut = `payé` :
- Désactiver édition
- Désactiver suppression

---

# 3️⃣ Module Déclarations — Professionnalisation

## 📄 À ajouter :
- Service CNSS Monthly Report
- Service IR Monthly Report
- Totaux automatiques
- Historique version

### Export :
- Génération fichier conforme format CNSS réel
- Validation avant soumission

---

# 4️⃣ Module Employés — Champs à ajouter

## 💳 Informations bancaires :
- IBAN
- Banque
- Mode paiement (virement / chèque / cash)

## 📎 Documents upload :
- Contrat PDF
- CIN scan
- Attestation CNSS

## 👥 Informations sociales :
- Temps partiel (bool)
- Handicap (optionnel IR)

---

# 5️⃣ Attendance — Amélioration logique

## À corriger :
- Calcul heures sup hebdomadaire
- Gestion jours fériés
- Rapport mensuel résumé

---

# 6️⃣ UX & SaaS Scaling

## 📊 Dashboard KPI

Ajouter :
- Masse salariale du mois
- Total CNSS
- Total IR
- Effectif actif
- Taux absentéisme

---

## 📝 Audit Log (OBLIGATOIRE)

Tracer :
- Modification paie
- Validation déclaration
- Suppression employé
- Changement taux CNSS

---

## 👤 Self-service Employé (Phase 2)

Créer panel employé :
- Télécharger bulletins
- Voir solde congés
- Demander congé
- Mettre à jour coordonnées

---

# 7️⃣ Nettoyage & Ajustements

## ❌ Supprimer :
- Toggle libre "Imposable"

## ✅ Remplacer par :
Types fixes :
- Prime imposable
- Prime non imposable
- Avantage imposable
- Retenue

---

# 🎯 Résumé des priorités

## Priorité 1 (Sécurité & conformité)
- Permissions fines
- Snapshot taux
- Verrouillage paie
- Prime ancienneté

## Priorité 2 (Professionnalisation)
- Déclarations avancées
- Intégration congés → paie
- Heures sup conformes

## Priorité 3 (SaaS Premium)
- Dashboard KPI
- Audit log
- Self-service employé

---

# 📌 Objectif final

Passer d’un système RH fonctionnel à une plateforme SaaS RH marocaine robuste, conforme et scalable.

