<?php
/* 
Initialisation générale des prohrammes (URL)

Ficher à inclure en début de toutes les URL
*/

// Afficher les erreurs PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Démarrage session
include_once "library/model.php";
include_once "data/personnage.php";
include_once "data/log.php";
session_start();

// Chargement des fonctions


// Ouverture de la BDD
global $bdd;
$bdd = new PDO("mysql:host=localhost;dbname=");

// Affichage des erreurs remontant de la BDD
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);