<?php
/*
Template de fragment : Mise en forme de la salle courante et des joueurs s'y trouvant
Paramètres :
        $personnages : tableau d'objets de la classe personnages dont la salle est l'id de la salle courante
*/
?>

<div class="flex" id="ecran-jeu" style="background-image: url(<?= $perso->urlFonds() ?>);">
    <div id="liste-joueur" class="large-100 flex">
        <?php
        foreach ($personnages as $joueur) {
            include "templates/fragments/div_joueur.php";
        }
        ?>
    </div>
</div>