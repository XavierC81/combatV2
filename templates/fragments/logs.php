<?php
/*
Template de fragment : Mise en forme des logs du personnage courant
Paramètre :
        $logs : tableau d'objet de la classe log ayant pour personnage l'id du perso courant
*/
    if (isset($logs)) {
        foreach ($logs as $log) {
            include "templates/fragments/ligne_p_log.php";
        }
    }
    ?>