<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FOLIO</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<header class="header">
    <a href="/home">
        <img src="assets/img/SVG/logo_folio_noir.svg" alt="Logo Folio" width="150">
    </a>
    <nav class="nav">
        <a href="/folio">FOLIOS</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="/profile">MON PROFIL</a>
            <a href="/login/logout">DÉCONNEXION</a>
        <?php else: ?>
            <a href="/login">CONNEXION</a>
        <?php endif; ?>
    </nav>
</header>

<body>