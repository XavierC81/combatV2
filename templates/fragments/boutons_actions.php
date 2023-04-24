<?php
/*
Template de fragment : Mise en forme des boutons d'actions du jeu
Paramètres : 
        $perso : Objet courant de la classe personnage
*/
?>
<span data-id="<?= $perso->id() ?>" class="btn" id="avancer">Avancer</span>
<span data-id="<?= $perso->id() ?>" class="btn" id="reculer">reculer</span>
<span data-id="<?= $perso->id() ?>" class="btn" id="res">For->Res</span>
<span data-id="<?= $perso->id() ?>" class="btn" id="for">Res->For</span>