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
        font-size: 15px;
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

    a {
        color: #1d4ed8;
    }
</style>
</head>
<body>

<div class="top-band"></div>

<div class="doc-footer">
    <span>{{ $settings->nom_ecole ?? 'Gestion RH' }} — Mentions Légales</span>
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
            <div class="doc-title">Mentions Légales</div>
            <div class="doc-date">Septembre 2026</div>
        </div>
    </div>

    <div class="intro-box">
        Conformément aux dispositions légales en vigueur, nous vous présentons ci-dessous les informations
        légales relatives à la plateforme de gestion RH des Écoles AL BARAIME.
    </div>

    <h2>1. Éditeur de la plateforme</h2>
    <p>La plateforme de gestion RH est éditée par :</p>
    <div class="contact-box">
        <strong>{{ $settings->nom_ecole ?? 'Les Écoles AL BARAIME' }}</strong><br>
        Réseau d'établissements scolaires privés — Maroc<br>
        Niveaux : Préscolaire · Primaire · Collège · Lycée
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
        Les utilisateurs sont responsables de l'exactitude des informations qu'ils saisissent dans le système.
    </p>

    <h2>6. Protection des données personnelles</h2>
    <p>
        Le traitement des données personnelles sur cette plateforme est régi par la loi marocaine
        n° 09-08 relative à la protection des personnes physiques à l'égard du traitement des
        données à caractère personnel.
    </p>
    <p>
        Pour plus d'informations, consultez notre Politique de Confidentialité disponible sur la plateforme.
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
        Contactez l'administrateur de votre établissement ou adressez votre demande
        via les coordonnées communiquées par votre responsable RH.
    </div>

</div>

</body>
</html>
