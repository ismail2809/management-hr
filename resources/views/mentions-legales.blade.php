<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales — Gestion RH</title>
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
    <a href="{{ route('mentions-legales.pdf') }}" class="pdf-btn" target="_blank">
        ⬇ Télécharger PDF
    </a>
    <a href="/admin/login" class="back-link">
        ← Retour à la connexion
    </a>
</div>

<div class="container">
    <div class="card">
        <div class="card-title">Mentions Légales</div>
        <div class="last-updated">Dernière mise à jour : Septembre 2026</div>

        <div class="intro">
            Conformément aux dispositions légales en vigueur, nous vous présentons ci-dessous
            les informations légales relatives à la plateforme de gestion RH des Écoles AL BARAIME.
        </div>

        <h2>1. Éditeur de la plateforme</h2>
        <p>La plateforme de gestion RH est éditée par :</p>
        <div class="contact-box">
            <p><strong>Les Écoles AL BARAIME</strong><br>
            Réseau d'établissements scolaires privés — Maroc<br>
            Niveaux : Préscolaire · Primaire · Collège · Lycée</p>
        </div>

        <h2>2. Hébergement</h2>
        <p>
            La plateforme est hébergée sur des serveurs sécurisés. Pour toute question relative
            à l'hébergement, veuillez contacter l'administrateur système de l'établissement.
        </p>

        <h2>3. Propriété intellectuelle</h2>
        <p>
            L'ensemble des contenus présents sur cette plateforme (textes, graphismes, logiciels, icônes,
            interfaces) est la propriété exclusive des Écoles AL BARAIME ou de ses partenaires, et est
            protégé par les lois marocaines et internationales relatives à la propriété intellectuelle.
        </p>
        <p>
            Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie
            des éléments de la plateforme, quel que soit le moyen ou le procédé utilisé, est interdite
            sans autorisation préalable écrite.
        </p>

        <h2>4. Accès à la plateforme</h2>
        <p>
            L'accès à la plateforme est strictement réservé au personnel autorisé des Écoles AL BARAIME
            disposant d'un identifiant et d'un mot de passe valides. Tout accès non autorisé est interdit
            et susceptible de faire l'objet de poursuites.
        </p>
        <ul>
            <li>Les accès sont nominatifs et non cessibles</li>
            <li>Chaque utilisateur est responsable de la confidentialité de ses identifiants</li>
            <li>Tout incident de sécurité doit être signalé immédiatement à l'administrateur</li>
        </ul>

        <h2>5. Responsabilité</h2>
        <p>
            Les Écoles AL BARAIME s'engagent à assurer la disponibilité de la plateforme dans la mesure
            du possible, mais ne peuvent être tenues responsables en cas d'interruption de service,
            de perte de données ou de tout dommage indirect lié à l'utilisation de la plateforme.
        </p>
        <p>
            Les utilisateurs sont responsables de l'exactitude des informations qu'ils saisissent
            dans le système.
        </p>

        <h2>6. Protection des données personnelles</h2>
        <p>
            Le traitement des données personnelles sur cette plateforme est régi par la loi marocaine
            n° 09-08 relative à la protection des personnes physiques à l'égard du traitement des
            données à caractère personnel.
        </p>
        <p>
            Pour plus d'informations, consultez notre
            <a href="/politique-confidentialite" style="color: #1e40af; font-weight: 500;">Politique de Confidentialité</a>.
        </p>

        <h2>7. Droit applicable</h2>
        <p>
            Les présentes mentions légales sont soumises au droit marocain. En cas de litige,
            et à défaut de résolution amiable, les tribunaux marocains compétents seront seuls habilités
            à connaître du différend.
        </p>

        <h2>8. Contact</h2>
        <p>Pour toute question relative aux présentes mentions légales :</p>
        <div class="contact-box">
            <p>Contactez l'administrateur de votre établissement ou adressez votre demande
            via les coordonnées communiquées par votre responsable RH.</p>
        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} Gestion RH — Tous droits réservés &nbsp;·&nbsp;
            <a href="/politique-confidentialite">Politique de confidentialité</a>
        </div>
    </div>
</div>

</body>
</html>
