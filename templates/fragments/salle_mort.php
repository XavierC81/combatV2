<?php
/*
Template de fragment : Mise en forme de la salle de fin
Paramètres :
        $perso : objet courant de la classe personnage
*/
?>

<div class="flex align-center justify-center" id="ecran-jeu" style="background-image: url(img/fonds/mort.jpg);">
    <h1 class="mort large-100">Vous êtes mort</h1>
    <?php include "templates/fragments/bouton_redemarrer.php" ?>
</div>