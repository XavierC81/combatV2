<?php
/*
Template de fragment : Mise en forme des statistiques courante du joueur
Paramètre : 
        $perso : perso courant
*/
?>
    <h1><?= $perso->htmlNomSalle($perso->get("salle")) ?></h1>
    <div class="flex justify-around">
        <div class="large-20">
            <p>PV</p>
            <p id="pv" data-pv="<?= $perso->get("pv") ?>"><?= $perso->html("pv") ?></p>
        </div>
        <div class="large-20">
            <p>Force</p>
            <p><?= $perso->html("force") ?></p>
        </div>
        <div class="large-20">
            <p>Agilité</p>
            <p id="agi-perso"><?= $perso->html("agilite") ?></p>
        </div>
        <div class="large-20">
            <p>Résistance</p>
            <p><?= $perso->html("resistance") ?></p>
        </div>
    </div>
    