<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité — Gestion RH</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: #334155;
            min-height: 100vh;
        }

        .header {
            background: #ffffff;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header h1 {
            color: #fff;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .back-link {
            color: #000000;
            text-decoration: none;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            margin-left: auto;
            transition: color 0.15s;
        }

        .back-link:hover { color: #000000; }

        .pdf-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: #1e40af;
            color: #fff;
            text-decoration: none;
            font-size: 0.8125rem;
            font-weight: 500;
            padding: 0.4rem 0.875rem;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .pdf-btn:hover { background: #1e3a8a; }

        .container {
            max-width: 820px;
            margin: 2.5rem auto;
            padding: 0 1.5rem 3rem;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.04);
            padding: 2.5rem 3rem;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .last-updated {
            font-size: 0.8125rem;
            color: #94a3b8;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .intro {
            background: #eff6ff;
            border-left: 3px solid #1e40af;
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
            font-size: 0.9375rem;
            color: #1e3a8a;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        h2 {
            font-size: 1.0625rem;
            font-weight: 600;
            color: #1e293b;
            margin: 1.75rem 0 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 18px;
            background: #1e40af;
            border-radius: 2px;
            flex-shrink: 0;
        }

        p {
            font-size: 0.9375rem;
            line-height: 1.75;
            color: #475569;
            margin-bottom: 0.75rem;
        }

        ul {
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }

        ul li {
            font-size: 0.9375rem;
            line-height: 1.75;
            color: #475569;
            margin-bottom: 0.25rem;
        }

        .contact-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            margin-top: 1rem;
        }

        .contact-box p { margin: 0; }

        .contact-box a {
            color: #1e40af;
            text-decoration: none;
            font-weight: 500;
        }

        .contact-box a:hover { text-decoration: underline; }

        .footer-note {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8125rem;
            color: #94a3b8;
        }

        .footer-note a {
            color: #94a3b8;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .card { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="header">
    <img src="/images/logo.png" alt="Gestion RH" style="max-height: 60px;">
    <a href="{{ route('politique-confidentialite.pdf') }}" class="pdf-btn" target="_blank">
        ⬇ Télécharger PDF
    </a>
    <a href="/admin/login" class="back-link">
        ← Retour à la connexion
    </a>
</div>

<div class="container">
    <div class="card">
        <div class="card-title">Politique de Confidentialité</div>
        <div class="last-updated">Dernière mise à jour : Mai 2024</div>

        <div class="intro">
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
            Toute modification sera signalée sur cette page avec une date de mise à jour. Nous vous encourageons
            à consulter régulièrement cette page.
        </p>
        <p>
            La poursuite de l'utilisation de la plateforme après une modification constitue votre acceptation
            des nouvelles conditions.
        </p>

        <h2>8. Nous contacter</h2>
        <p>Pour toute question concernant cette politique de confidentialité, veuillez nous contacter :</p>
        <div class="contact-box">
            <p>
                Via la page de contact de votre administrateur RH ou par e-mail à l'adresse communiquée
                par votre entreprise.
            </p>
        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} Gestion RH — Tous droits réservés &nbsp;·&nbsp;
            <a href="/mentions-legales">Mentions légales</a>
        </div>
    </div>
</div>

</body>
</html>
