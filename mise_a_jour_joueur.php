<?php

// Controleur : préparer et afficher le formulaire de connexion ou de création de personnage
// Paramètres :
//      GET id : id du perso donné

// Initialisation
include "library/init.php";

// Connection
$perso = new personnage($_GET["id"]);



// Analyse de la demande


// Intéraction objet / BDD
$logs = $perso->getLogs();
if ($perso->get("salle") != 0 && $perso->get("salle") != 10) {
    $personnages = $perso->getPersonnages();
} else {
    $personnages[] = $perso;
}



// Affichage
foreach ($personnages as $joueur) {
    include "templates/fragments/div_joueur.php";
}
