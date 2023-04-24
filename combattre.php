<?php

// Controleur : calculer le combat et afficher les stats mises à jour
// Paramètres :
//      GET idadversaire : id de l'adversaire

// Initialisation
include "library/init.php";




// Analyse de la demande
$idAdversaire = $_GET["idadversaire"];
$perso = new personnage($_SESSION["id"]);



// Intéraction objet / BDD
$perso->combattre($idAdversaire);




// Affichage
include "templates/fragments/stats_joueur.php";
