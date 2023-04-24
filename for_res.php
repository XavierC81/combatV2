<?php 
/*
Controleur : transforme un point de force en point de resistance ou inversement contre 3 point d'agilité et met à jour la BDD
Paramètres :
        GET action : for ou res
        GET id : id du personnage donné
*/

// Initialisation
include "library/init.php";

// Analyse de la demande
$perso = new personnage($_GET["id"]);

// Intéraction objet / BDD
$perso->changeResFor($_GET["action"]);
$perso->update();

// Affichage
include "templates/fragments/stats_joueur.php";