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


// Affichage
if ($perso->get("pv") <= 0) {
    include "templates/fragments/salle_mort.php";
}
