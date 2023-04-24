<?php

// Classe log : manipuler l'objet log

class log extends _model
{
    protected $champs = ["personnage" => "link", "evenement" => "string", "datetime" => "date", "adversaire" => "link"];
    protected $table = "log";
    protected $links = ["personnage" => "personnage"];
    protected $champsNomComplet = ["evenement"];

    public function getlogs()
    {
        // Rôle : récupère les logs dont la personnage est l'id de la session
        // Retour : tableau d'objet logs
        // Paramètre : néant
        $champs = $this->getChampsSelect();
        $sql = "SELECT `id`, $champs FROM `$this->table` WHERE `personnage` = :personnage OR `adversaire` = :personnage";
        $param = [":personnage" => $_SESSION["id"]];
        $req = $this->executeRequest($sql, $param);

        if ($req == false) {
            return false;
        }
        $tab = $req->fetchAll(PDO::FETCH_ASSOC);

        if (empty($tab)) {
            $tab = [];
        }
        return $this->tabToTabObj($tab);
    }

    public function logMort($perso) {
        // Rôle : Créer une nouvelle ligne dans la BDD pour la mort d'un personnage
        // Retour : néant
        // Paramètre : 
        //      $perso : objet courant de la classe personnage
        $message = "<span class='perte'>&#9760;</span> " . $perso->html("pseudo") . " est mort";
        $this->logInsert($perso, null, $message);
    }

    public function newGame($perso) {
        // Rôle : Créer une nouvelle ligne dans la BDD pour une nouvelle partie
        // Retour : néant
        // Paramètre : néant
        $message = $perso->get("pseudo") . " commence une nouvelle partie";
        $this->logInsert($perso, null, $message);
    }


    public function logEsquive($attaquant, $adversaire)
    {
        // Rôle : Créer un log pour l'esquive d'une attaque
        // retour : néant
        // Paramètres :
        //      $attaquant : objet perso de l'attaquant
        //      $adversaire : objet perso de l'adversaire
        $message = $adversaire->get("pseudo") . " esquive l'attaque de " . $attaquant->get("pseudo") . " et perd 1 point d'agilité";
        $this->logInsert($attaquant, $adversaire, $message);
    }

    public function logMouvement($perso, $salle)
    {
        // Rôle : Créer un log pour le mouvement entre les salles
        // Retour : néant
        // Paramètres :
        //      $perso : objet du personnage donné
        //      $salle : nom de la salle donné
        $message = "<span class='mouvement'>&#10159;</span> " . $perso->html("pseudo") . " entre dans $salle";
        $this->logInsert($perso, null, $message);
    }

    public function logLanceCombat($attaquant, $adversaire)
    {
        // Rôle : créer une ligne de log pour l'attaque
        // Retour : néant
        // Paramètres : 
        //      $attaquant : objet de la classe personnage qui lance l'attaque
        //      $adversaire : objet de la classe personnage qui est attaqué
        $message = "<span class='attaque'>&#9876;</span> " . $attaquant->get("pseudo") . " attaque " . $adversaire->get("pseudo");
        $this->logInsert($attaquant, $adversaire, $message);
    }

    public function logGainPV($perso, $pv)
    {
        // Créer une ligne de log pour le gain de pv
        // Retour : néant
        // Paramètres :
        //      $perso : objet de la classe personnage donné
        //      $salle : numéro de la salle soit le nombre de PV récupérer
        $message = "<span class='gain'>&#10084;</span> " . $perso->get("pseudo") . " gagne $pv points de vie";
        $this->logInsert($perso, null, $message);
    }

    public function logGainAgi($perso) {
        // Rôle : Créer une ligne de log pour le gain d'agilité
        // Retour : néant
        // Paramètres :
        //      $perso : objet de la classe personnage donné
        $message = "<span class='gain'>&#10138;</span> " . $perso->get("pseudo"). " gagne 1 point d'agilité";
        $this->logInsert($perso, null, $message);
    }

    public function logPerteAgi($perso, $perteAgi) {
        // Rôle : Créer une ligne de log pour la perte de point d'agilité
        // Retour : néant
        // Paramètres :
        //      $perso : objet de la classe personnage donné
        //      $perteAgi : nombre de points d'agilité perdu
        $message = "<span class='perte'>&#10136;</span> " . $perso->get("pseudo"). " perd $perteAgi points d'agilité";
        $this->logInsert($perso, null, $message);
    }


    public function logRiposte($attaquant, $adversaire)
    {
        // Rôle : Créer une ligne de log pour la riposte
        // Retour : néant
        //      $attaquant : objet perso de l'attaquant
        //      $adversaire : objet perso de l'adversaire
        $message = $adversaire->get("pseudo") . " riposte";
        $this->logInsert($attaquant, $adversaire, $message);
        $this->logLanceCombat($adversaire, $attaquant);
    }

    public function logResFor($stat, $perso) {
        // Rôle : Créer une ligne de log pour un changement de force ou de resistance
        // Retour : néant
        // Paramètres :
        //      $stat : res ou for
        //      $perso : objet courant de la classe personnage
        if ($stat == "res") {
            $bonus = "resistance";
            $malus = "force";
        } else {
            $bonus = "resistance";
            $malus = "force";
        }
        $message = "<span class='gain'>&#10138;</span> " . $perso->get("pseudo"). " gagne 1 point de $bonus <br><span class='perte'>&#10136;</span> " . $perso->get("pseudo"). " perd 1 point de $malus <br><span class='perte'>&#10136;</span> ". $perso->get("pseudo"). " perd 3 points d'agilité";
        $this->logInsert($perso, null, $message);
    }

    public function logTue($attaquant, $defenseur) {
        // Rôle : Créer une ligne de log si l'attaquant tue le défenseur
        // Retour : néant
        // Paramètres :
        //      $attaquant : objet donné de la classe personnage
        //      $defenseur : objet donné de la classe personnage
        $message = "<span class='perte'>&#9760;</span> " . $attaquant->html("pseudo") . " a tué " . $defenseur->html("pseudo");
        $this->logInsert($attaquant, $defenseur, $message);
    }

    public function logDefaite($attaquant, $adversaire, $pv)
    {
        // Rôle : Créer un ligne de log pour la defaite
        // Retour : néant
        // //      $attaquant : objet perso de l'attaquant
        //      $adversaire : objet perso de l'adversaire
        //      $pv : nombre de pv perdu
        $message = "<span class='perte'>&#10084;</span> " . $attaquant->get("pseudo") . " perd $pv points de vie";
        $this->logInsert($attaquant, $adversaire, $message);
    }

    public function logDefense($perso, $attaquant) {
        // Rôle : Créer une ligne de log pour la défense
        // Retour : néant
        // Paramètre :
        //      $perso : objet de la classe personnage qui se défend
        //      $attaquant : objet de la classe personnage qui attaque
        $message = "<span class='mouvement'>&coprod;</span> " . $perso->html("pseudo") . " se défend contre l'attaque de " . $attaquant->html("pseudo");
        $this->logInsert($attaquant, $perso, $message);
    }

    // Fonctions protégé

    protected function logInsert($attaquant, $adversaire = null, $message)
    {
        // Rôle : Créer la ligne de log demandé
        // Retour : néant
        // Paramètres :
        //      $attaquant : objet perso de l'attaquant
        //      $adversaire : objet perso de l'adversaire
        //      $message : message à enregistrer
        $this->set("evenement", $message);
        $this->set("personnage", $attaquant->id());
        $date = date("Y-m-d H:i:s");
        if (!is_null($adversaire)) {
            $this->set("adversaire", $adversaire->id());
        }
        $this->set("datetime", $date);
        $this->insert();
    }
}
