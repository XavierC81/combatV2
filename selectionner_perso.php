<?php 

// Controleur : préparer et afficher le formulaire de connexion ou de création de personnage
// Paramètres :
//      GET action : creer ou connecter

// Initialisation
include "library/init.php";

// Analyse de la demande
if ($_GET["action"] == "creer") {
    $titre = "Créer";
}
if ($_GET["action"] == "connecter") {
    $titre = "Connecter";
}

// Intéraction objet / BDD


// Affichage
include "templates/pages/form_perso.php";