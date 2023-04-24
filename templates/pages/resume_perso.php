<?php
/*
Template de page : Mise en forme du résumé du perso
Paramètres :
        $action : connecter ou creer
        $titre : Connecter ou Créer
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "templates/fragments/head.php" ?>
    <title>Résumé de <?= $perso->html("pseudo") ?></title>
</head>

<body class="container">
    <div class="recap">
        <h1>Résumé de <?= $perso->html("pseudo") ?></h1>
        <p><img src="<?= $perso->get("portrait") ?>" alt=""></p>
        <p>PV : <?= $perso->html("pv") ?></p>
        <p>Force : <?= $perso->html("force") ?></p>
        <p>Agilité : <?= $perso->html("agilite") ?></p>
        <p>Résistance : <?= $perso->html("resistance") ?></p>
    </div>
    <a href="lancer_jeu.php?id=<?= $perso->id() ?>" class="btn">Commencer</a>
</body>

</html>