<?php

// Controleur : charger la nouvelle salle
// Paramètres :
//      GET action : avancer ou reculer
//      GET id : id du personnage donné

// Initialisation
include "library/init.php";

// Analyse de la demande
$perso = new personnage($_GET["id"]);
if ($perso->get("salle") != 10 && $perso->get("pv") > 0) {
    $perso->mouvementSalle($_GET["action"]);
}

// Intéraction objet / BDD
if ($perso->get("salle") != 0 && $perso->get("salle") != 10) {
    $personnages = $perso->getPersonnages();
} else {
    $personnages[] = $perso;
}



// Affichage
if ($perso->get("salle") == 10) {
    include "templates/fragments/salle_fin.php";
} else {
    include "templates/fragments/salle.php";
}
