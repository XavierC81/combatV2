<?php 
/*
Template de fragment : Mise en forme d'une div joueur
Paramètres :
        $joueur : objet courant de la classe joueur
*/
?>

<div class="joueur" data-id="<?= $joueur->id() ?>" onclick="combat(<?= $joueur->id() ?>)">
    <p class="nom"><?= $joueur->html("pseudo") ?></p>
    <img src="<?= $joueur->get("portrait")?>" alt="Avatar de <?= $joueur->html("pseudo") ?>">
</div>