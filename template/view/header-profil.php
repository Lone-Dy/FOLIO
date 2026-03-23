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