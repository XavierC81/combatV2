<?php 
// Controleur : Préparer et afficher la page d'accueil du jeu "combat"
// Paramètre : néant

// Initialisation
include "library/init.php";

// Analayse de la demande
$perso = new personnage($_GET["id"]);
if ($perso->get("pv") <= 0) {    
    $perso->logMort();
}
$perso->resetStat();
$perso->update();


// Intéraction objet / BDD
$logs = $perso->getLogs();
if ($perso->get("salle") != 0 && $perso->get("salle") != 10) {
    $personnages = $perso->getPersonnages();
} else {
    $personnages[] = $perso;
}



// Affichage
header("location: lancer_jeu.php?id=" . $perso->id());