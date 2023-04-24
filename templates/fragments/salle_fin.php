<?php
/*
Template de fragment : Mise en forme de la salle de fin
Paramètres :
        $perso : objet courant de la classe personnage
*/
?>

<div class="flex align-center justify-center" id="ecran-jeu" style="background-image: url(<?= $perso->urlFonds() ?>);">
    <h1 class="finir large-100">Vous avez gagné !</h1>
    <?php include "templates/fragments/bouton_redemarrer.php" ?>
</div>