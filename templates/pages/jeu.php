<?php
/*
Template de page : Mise en forme du jeu
Paramètres :
        $perso : objet de la classe personnage courant
        $personnages : liste des personnages dont la salle est la salle du perso
        $logs : liste d'objet de la classe log
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "templates/fragments/head.php" ?>
    <title>Combat</title>
</head>
<div>
    <div id="jeu" class="flex">
        <div class="large-75" id="salle">
            <?php
            include "templates/fragments/salle.php";
            ?>
        </div>
        <div class="large-25" id="ecran-log">
            <?php
            include "templates/fragments/logs.php";
            ?>
        </div>
    </div>
</div>
<div class="fond-interface">
    <div id="ecran-interface">
        <?php
        include "templates/fragments/stats_joueur.php";
        ?>
    </div>
    <div class="flex justify-around" id="boutons-action">
        <?php        
            include "templates/fragments/boutons_actions.php";        
        ?>
    </div>
</div>
<?php include "templates/fragments/footer.php" ?>
</body>

</html>