<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?=$title?></title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style/style.css" />
    <?php if (isset($style)):?>
    <link rel="stylesheet" href="style/<?=$style?>.css" />
    <?php endif;?>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">
            <img src="img/icon.png" alt="icon du site">
        </a>
    </div>
    <nav>
        <ul>
            <li><a class="select-nav" href="index.php">Connexion</a></li>
        </ul>
    </nav>
</header>
<main>