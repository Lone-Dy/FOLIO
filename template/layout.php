<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?=  $title ?? 'Folio - Plateforme de partage de portfolios créatifs'?></title> <!-- définit le titre de la page qui apparaît dans l'onglet du navigateur. -->
    <meta name="description" content="<?=  $description ?? 'Plateforme de partage de portfolios créatifs.' ?>"> <!-- Fournit une description de la page. -->
    <meta name="author" content="<?= $author ?? 'Nom de l’auteur' ?>"> <!-- Spécifie le nom de l'auteur de la page. -->
    <meta name="copyright" content="<?= $copyright ?? 'Propriétaire du copyright et année' ?>"> <!-- Spécifie les informations de copyright pour la page. -->
    <meta name="robots" content="<?= $robots ?? 'index, follow' ?>"> <!-- Fournit des directives aux moteurs de recherche sur la manière d'indexer -->
    <meta http-equiv="refresh" content="5; url=https://exemple.fr/" />
    <link rel="icon" type="image/png" href="/assets/ico/favicon-folio.png">
    <link rel="stylesheet" href="/assets/css/style-constant.css">
    <link rel="stylesheet" href="/assets/css/style-special.css">
    <link rel="stylesheet" href="/assets/css/style-folio.css">
</head>

<body>

    <?php
    include_once(__DIR__ . '/view/header.php');
    ?>

    <main>
        <?= $ob_content ?>
    </main>

    <?= $ob_modal ?>

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