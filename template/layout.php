<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FOLIO</title>
    <link rel="icon" type="image/png" href="/assets/ico/favicon-folio.png">
    <link rel="stylesheet" href="/assets/css/style-constant.css">
    <link rel="stylesheet" href="/assets/css/style-special.css">
    <link rel="stylesheet" href="/assets/css/style-folio.css">
</head>
<body>
<header class="header">
    <a href="/home" class="logo">
        <img src="/assets/img/SVG/logo_folio_noir.svg" alt="Logo Folio - identité visuelle" width="150">
    </a>
    <h1>Le portfolio qui se construit aussi vite qu'une idée.</h1>
    <div class="header-search">
        <form action="/folio" method="GET">
            <input type="text" name="q" placeholder="Rechercher un créatif..." aria-label="Rechercher">
            <button type="submit">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </form>
    </div>
    <nav class="nav">
        <?php if (isset($_SESSION['user'])): ?>
            <a href="/projet">MON FOLIO</a>
            <a href="/profile">MON PROFIL</a>
            <a href="/login">DÉCONNEXION</a>
        <?php else: ?>
            <a href="/login">CONNEXION</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?= $ob_content ?>
</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-brand">
            <a href="/home">
                <img src="/assets/img/SVG/logo_folio_blanc.svg" alt="Logo Folio" width="150">
            </a>
            <p class="description-folio">Plateforme de partage de portfolios créatifs.</p>
        </div>

        <div class="footer-links">
            <h4>Navigation</h4>
            <ul>
                <li><a href="/home">Accueil</a></li>
                <li><a href="/folio">Galerie</a></li>
                <li><a href="/login">Connexion</a></li>
            </ul>
        </div>

        <div class="footer-legal">
            <h4>Légal</h4>
            <ul>
                <li><a href="/condition">Conditions générales d'utilisation</a></li>
                <li><a href="/condition/privacy">Politique de confidentialité</a></li>
                <li><a href="/condition/mentions">Mentions légales</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y'); ?> FOLIO. Tous droits réservés.</p>
    </div>
</footer>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
<script src="/assets/js/script.js" defer></script>

</html>