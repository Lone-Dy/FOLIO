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
        <h1>
        <img src="/assets/img/SVG/logo_folio_noir.svg" alt="Logo Folio" width="150">
        </h1>
    </a>
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
            <a href="/login/logout">DÉCONNEXION</a>
        <?php else: ?>
            <a href="/login">CONNEXION</a>
        <?php endif; ?>
    </nav>
</header>
