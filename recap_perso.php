<?php

// Controleur : préparer et afficher le formulaire de connexion ou de création de personnage
// Paramètres :
//      GET action : creer ou connecter
//      POST : champ du formulaire de creation ou connexion de personnage

// Initialisation
include "library/init.php";

// Connection
$perso = new personnage();
if ($_GET["action"] == "creer") {
    $perso->creation($_POST);
} else if ($_GET["action"] == "connecter") {
    $perso->connexion($_POST["pseudo"], $_POST["password"]);
}



// Analyse de la demande


// Intéraction objet / BDD


// Affichage
include "templates/pages/resume_perso.php";
