<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 11px;
        color: #1a1a1a;
        background: #fff;
    }

    /* Bande décorative haut */
    .top-band {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: 8px;
        background: #1d4ed8;
    }

    /* Pied de page fixé */
    .doc-footer {
        position: fixed;
        bottom: 8mm;
        left: 18mm;
        right: 18mm;
        border-top: 1px solid #c7d2fe;
        padding-top: 5px;
        display: flex;
        justify-content: space-between;
        font-size: 9px;
        color: #6b7280;
    }

    .page {
        padding: 22mm 18mm 18mm;
    }

    /* En-tête */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #1d4ed8;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-left img {
        height: 60px;
        width: auto;
    }

    .school-info {
        font-size: 10px;
        color: #374151;
        line-height: 1.6;
    }

    .school-name {
        font-size: 13px;
        font-weight: bold;
        color: #1d4ed8;
    }

    .doc-title-block {
        text-align: right;
    }

    .doc-title {
        font-size: 13px;
        font-weight: bold;
        color: #1d4ed8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .doc-date {
        font-size: 9px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* Intro box */
    .intro-box {
        background: #eff6ff;
        border-left: 4px solid #1d4ed8;
        padding: 10px 14px;
        margin-bottom: 18px;
        font-size: 10.5px;
        color: #1e3a8a;
        line-height: 1.65;
    }

    /* Sections */
    h2 {
        font-size: 10.5px;
        font-weight: bold;
        color: #ffffff;
        background: #1d4ed8;
        padding: 5px 10px;
        margin-top: 14px;
        margin-bottom: 7px;
        letter-spacing: 0.3px;
    }

    p {
        font-size: 10.5px;
        line-height: 1.7;
        color: #374151;
        margin-bottom: 6px;
    }

    ul {
        padding-left: 16px;
        margin-bottom: 6px;
    }

    ul li {
        font-size: 10.5px;
        line-height: 1.7;
        color: #374151;
        margin-bottom: 2px;
    }

    .contact-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 10px 14px;
        margin-top: 8px;
        font-size: 10.5px;
        line-height: 1.65;
        color: #374151;
    }
</style>
</head>
<body>

<div class="top-band"></div>

<div class="doc-footer">
    <span>{{ $settings->nom_ecole ?? 'Gestion RH' }} — Politique de Confidentialité</span>
    <span>Septembre 2026</span>
</div>

<div class="page">

    <div class="header">
        <div class="header-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="{{ $settings->nom_ecole ?? '' }}">
            @endif
            <div class="school-info">
                <div class="school-name">{{ $settings->nom_ecole ?? 'Les Écoles AL BARAIME' }}</div>
                <div>Réseau d'établissements scolaires privés — Maroc</div>
                <div>Préscolaire · Primaire · Collège · Lycée</div>
            </div>
        </div>
        <div class="doc-title-block">
            <div class="doc-title">Politique de Confidentialité</div>
            <div class="doc-date">Septembre 2026</div>
        </div>
    </div>

    <div class="intro-box">
        La présente politique de confidentialité régit la manière dont notre plateforme de gestion RH
        collecte, utilise et protège les informations que vous nous fournissez lorsque vous utilisez notre application.
    </div>

    <h2>1. Collecte des informations personnelles</h2>
    <p>
        Nous collectons des informations personnelles uniquement lorsque vous nous les fournissez volontairement,
        notamment lors de l'inscription, de la saisie de vos données RH ou lors de l'utilisation de nos formulaires.
    </p>
    <p>Les données collectées peuvent inclure :</p>
    <ul>
        <li>Nom, prénom et coordonnées (adresse e-mail, téléphone)</li>
        <li>Numéro CIN, numéro CNSS</li>
        <li>Informations d'emploi (poste, département, contrat, salaire)</li>
        <li>Données de présence et de congés</li>
        <li>Documents administratifs (bulletins de paie, attestations)</li>
    </ul>
    <p>
        Le refus de fournir certaines données personnelles peut limiter votre accès à certaines fonctionnalités
        de la plateforme.
    </p>

    <h2>2. Utilisation des données</h2>
    <p>Les informations collectées sont utilisées exclusivement pour :</p>
    <ul>
        <li>La gestion administrative du personnel de votre entreprise</li>
        <li>Le calcul des salaires, des cotisations CNSS/AMO et de l'IR</li>
        <li>La génération des déclarations légales (CNSS, IR, État 9421)</li>
        <li>La gestion des congés et des absences</li>
        <li>L'amélioration des fonctionnalités de la plateforme</li>
    </ul>
    <p>
        Vos données ne sont ni vendues, ni échangées, ni transférées à des tiers sans votre consentement,
        sauf obligation légale.
    </p>

    <h2>3. Protection des données</h2>
    <p>
        Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos
        informations contre tout accès non autorisé, modification, divulgation ou destruction.
    </p>
    <ul>
        <li>Chiffrement des mots de passe (bcrypt)</li>
        <li>Accès restreint aux données par rôle (super-admin, secrétaire, employé)</li>
        <li>Isolation des données par entreprise (multi-tenancy)</li>
        <li>Connexions sécurisées (HTTPS)</li>
    </ul>

    <h2>4. Cookies</h2>
    <p>
        Notre application utilise des cookies de session pour assurer le bon fonctionnement de l'authentification
        et améliorer votre expérience utilisateur. Ces cookies sont strictement nécessaires et ne servent pas
        à des fins publicitaires ou de traçage.
    </p>
    <p>
        Vous pouvez configurer votre navigateur pour refuser les cookies, mais cela pourrait affecter
        le fonctionnement de l'application.
    </p>

    <h2>5. Conservation des données</h2>
    <p>
        Les données personnelles sont conservées pendant toute la durée de la relation contractuelle
        et pendant la période requise par la législation marocaine en vigueur (notamment la loi 09-08
        relative à la protection des personnes physiques à l'égard du traitement des données à caractère personnel).
    </p>

    <h2>6. Vos droits</h2>
    <p>Conformément à la loi 09-08, vous disposez des droits suivants :</p>
    <ul>
        <li><strong>Droit d'accès</strong> : consulter les données vous concernant</li>
        <li><strong>Droit de rectification</strong> : corriger des données inexactes</li>
        <li><strong>Droit d'opposition</strong> : vous opposer à certains traitements</li>
        <li><strong>Droit à l'effacement</strong> : demander la suppression de vos données</li>
    </ul>

    <h2>7. Modifications de la politique</h2>
    <p>
        Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment.
        Toute modification sera signalée sur cette page avec une date de mise à jour.
    </p>
    <p>
        La poursuite de l'utilisation de la plateforme après une modification constitue votre acceptation
        des nouvelles conditions.
    </p>

    <h2>8. Nous contacter</h2>
    <p>Pour toute question concernant cette politique de confidentialité :</p>
    <div class="contact-box">
        Via la page de contact de votre administrateur RH ou par e-mail à l'adresse communiquée
        par votre entreprise.
    </div>

</div>

</body>
</html>
