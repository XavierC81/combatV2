<?php

// Classe personnage : manipuler l'objet personnage

class personnage extends _model
{
    protected $champs = ["pseudo" => "string", "password" => "string", "pv" => "number", "force_base" => "number", "agilite_base" => "number", "resistance_base" => "number", "portrait" => "string", "salle" => "number", "force" => "number", "agilite" => "number", "resistance" => "number"];
    protected $table = "personnage";
    protected $champsNomComplet = ["pseudo"];
    protected $nomSalle = ["0" => "Entrée", "1" => "Salle 1", "2" => "Salle 2", "3" => "Salle 3", "4" => "Salle 4", "5" => "Salle 5", "6" => "Salle 6", "7" => "Salle 7", "8" => "Salle 8", "9" => "Salle 9", "10" => "Sortie"];
    protected $urlFonds = ["0" => "img/fonds/entree.jpg", "1" => "img/fonds/salle1.jpg", "2" => "img/fonds/salle2.jpg", "3" => "img/fonds/salle3.jpg", "4" => "img/fonds/salle4.jpg", "5" => "img/fonds/salle5.jpg", "6" => "img/fonds/salle6.jpg", "7" => "img/fonds/salle7.jpg", "8" => "img/fonds/salle8.jpg", "9" => "img/fonds/salle9.jpg", "10" => "img/fonds/sortie.jpg"];


    // Fonctions publiques


    public function resetStat()
    {
        // Rôle : Réinitialise les stats du joueur
        // Retour : néant
        // Paramètre : néant
        $this->setCarac();
        $log = new log();
        $log->newGame($this);
    }

    public function logMort()
    {
        // Rôle ; créer une ligne de log pour la mort du personnage
        // Retour : néant
        // Paramètre : néant
        $log = new log();
        $log->logMort($this);
    }

    public function combattre($idAdv)
    {
        // Rôle : calcule le déroulement du combat et met à jour les stats et enregistre les logs dans la BDD
        // Retour : néant
        // Paramètres :
        //      $idAdv : id du personnage attaqué
        $adversaire = new personnage($idAdv);
        if ($this->valeurs["pv"] <= 0 || $this->id  == $adversaire->id()) {
            exit;
        }
        $this->logLanceCombat($this, $adversaire);
        $adversaire->subitCombat($this->id);
    }

    public function changeResFor($stat)
    {
        // Rôle : transforme un point de force en point de resistance ou inversement contre 3 point d'agilité
        // Retour : néant
        // Paramètres : 
        //      $stat : res ou for selon les point de stat à changer
        $log = new log();
        if ($stat == "res" && $this->valeurs["agilite"] >= 3 && $this->valeurs["resistance"] < 12) {
            $this->valeurs["force"] -= 1;
            $this->valeurs["resistance"] += 1;
            $this->valeurs["agilite"] -= 3;
            $log->logResFor($stat, $this);
        } else if ($stat == "for" && $this->valeurs["agilite"] >= 3 && $this->valeurs["force"] < 12) {
            $this->valeurs["force"] += 1;
            $this->valeurs["resistance"] -= 1;
            $this->valeurs["agilite"] -= 3;
            $log->logResFor($stat, $this);
        }
    }


    public function mouvementSalle($action)
    {
        // Rôle : calcule la nouvelle salle du personnage après un mouvement et le met à jour dans la BDD
        // Retour néant
        // Paramètres :
        //      $action : avancer ou reculer
        $perteAgi = false;
        if ($action == "avancer") {
            $n = 1;
            if ($this->valeurs["agilite"] >= $this->valeurs["salle"] + 1) {
                $this->valeurs["agilite"] = $this->valeurs["agilite"] - ($this->valeurs["salle"] + 1);
                $perteAgi = true;
            } else {
                $n = 0;
            }
        } else {
            $n = -1;
            $this->valeurs["pv"] = $this->valeurs["pv"] + ($this->valeurs["salle"] - 1);
            if ($this->valeurs["pv"] > 100) {
                $this->valeurs["pv"] = 100;
            }
        }
        if ($this->valeurs["salle"] == 0 && $n == -1) {
            $this->valeurs["salle"] = 0;
        } else if ($this->valeurs["salle"] == 10 && $n == 1) {
            $this->valeurs["salle"] = 10;
        } else {
            $this->valeurs["salle"] = $this->valeurs["salle"] + $n;
            $this->update();
            $log = new log();
            $log->logMouvement($this, $this->htmlNomSalle());
            if ($action == "reculer" && $this->valeurs["pv"] < 100) {
                $log->logGainPV($this, $this->valeurs["salle"]);
            }
            if ($perteAgi) {
                $log->logPerteAgi($this, $this->valeurs["salle"]);
            }
        }
    }

    public function logGainAgi()
    {
        $log = new log();
        $log->logGainAgi($this);
    }

    public function creation($tab)
    {
        // Rôle : Créer un nouveau personnage dans la BDD en cryptant le password
        // Retour : néant
        // Paramètres :
        //      $tab : Tableau de valeurs pour créer le nouveau personnage
        $this->loadFromArray($tab);
        $this->set("password", password_hash($tab["password"], PASSWORD_DEFAULT));
        $this->setCarac();
        $this->insert();
        $_SESSION["id"] = $this->id;
        $_SESSION["salle"] = "img/fonds/entree.jpg";
    }

    public function connexion($pseudo, $password)
    {
        // Rôle : Vérifier les codes de connexions et établir la connexion si ok, la fermer sinon
        // Retour : true ou false
        // Paramètres : 
        //      $pseudo : pseudo à vérifier
        //      $password : password à vérifier
        if (!$this->loadByPseudo($pseudo)) {
            $this->deconnecter();
            return false;
        }
        if (!password_verify($password, $this->get("password"))) {
            $this->deconnecter();
            return false;
        }
        $_SESSION["id"] = $this->id;
        $_SESSION["salle"] = $this->urlFonds($this->valeurs["salle"]);
        return true;
    }


    public function deconnecter()
    {
        // Rôle : fermer la connexion courante
        // Retour : néant
        // Paramètre : néant
        $_SESSION["id"] = 0;
    }


    public function isConnected($id)
    {
        // Rôle : savoir si quelqu'un est connecté ou pas
        // Retour : true si connexion active, false sinon
        // Paramètres : néant
        if ($_SESSION["id"] != $id) {
            return false;
        } else {
            return true;
        }
    }

    public function htmlNomSalle()
    {
        // Rôle : Retourne sous forme HTML le nom de la salle courante
        // Retour : Valeur html de la salle
        // Paramètres :
        //      $num : numéro de la salle courante
        foreach ($this->nomSalle as $numSalle => $salle) {
            if ($numSalle == $this->valeurs["salle"]) {
                return $salle;
            }
        }
    }

    public function urlFonds()
    {
        // Rôle : récupère le chemin de l'image de fond à afficher
        // Retour : le chemin de l'image
        // Paramètre : le numéro de la salle
        return $this->urlFonds[$this->valeurs["salle"]];
    }

    public function getLogs()
    {
        // Rôle : récupère les logs dont la personnage est l'id de la session
        // Retour : tableau d'objet logs
        // Paramètre : néant
        $log = new log();
        return $log->getLogs();
    }

    public function getPersonnages()
    {
        // Rôle : récupère les personnages dont la salle est la salle de l'objet courant
        // Retour : tableau d'objet personnages
        // Paramètre : néant
        $champs = $this->getChampsSelect();
        $sql = "SELECT `id`, $champs FROM `$this->table` WHERE `salle` = :salle AND `pv` > 0";
        $param = [":salle" => $this->valeurs["salle"]];
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


    // Fonctions protégé


    protected function logLanceCombat($attaquant, $adversaire)
    {
        // Rôle : créer deux ligne de logs, une pour l'attanquant et une pour le defenseur
        // Retour : néant
        // Paramètres : néant
        $log = new log();
        $log->logLanceCombat($attaquant, $adversaire);
    }

    protected function logEsquive($perso)
    {
        // Rôle : Créer un log pour l'esquive d'une attaque
        // rettour : néant
        // Paramètres :
        //      $perso : objet perso de l'adversaire
        $log = new log();
        $log->logEsquive($perso, $this);
    }

    protected function subitAttaque($id, $riposte = "")
    {
        // Rôle : Calcule de déroulement d'un combat
        // Retour : néant
        // Paramètre : 
        //      $id : id de l'attaquant
        $perso = new personnage($id);
        $pertePV = 0;
        $statut = "defaite";
        $esquive = false;
        $log = new log();
        // Si l'agilité est supérieur ou égal à la force de l'adversaire +3, on esquive
        if ($this->valeurs["agilite"] >= $perso->get("force") + 3) {
            $this->valeurs["agilite"]--;
            $this->logEsquive($perso);
            if ($perso->get("force") >= 10 && $perso->get("force") < 15) {
                $perso->set("force", ($perso->get("force") - 1));
                $perso->set("resistance", ($perso->get("resistance") + 1));
            }
            $esquive = true;
            // Si la force du joueur attaqué est supérieur à celle de l'attaquant, on lance une riposte
        } else if ($this->valeurs["force"] > $perso->get("force")) {
            $log->logRiposte($perso, $this);
            $perso->subitAttaque($this->id, "riposte");
            // Si la resistance du joueur attaqué est supérieur ou égal à la force de l'attaquant, on déclare une victoire
        } else if ($this->valeurs["resistance"] >= $perso->get("force")) {
            $statut = "victoire";
            $log->logDefense($this, $perso);
        }
        if ($this->valeurs["resistance"] < $perso->get("force") && $esquive == false) {
            $this->valeurs["pv"] -= $perso->get("force") - $this->valeurs["resistance"];
            $pertePV = $perso->get("force") - $this->valeurs["resistance"];
            $log->logDefaite($this, $perso, $pertePV);
        }
        if ($riposte == "riposte" && $esquive == false) {
            if ($statut == "defaite") {
                if ($perso->valeurs["pv"] < 100) {
                    $perso->valeurs["pv"]++;
                }
            } else {
                $perso->valeurs["pv"] -= 2;
                $pertePVAtt = 2;
                $log->logDefaite($perso, $this, $pertePVAtt);
            }
        }

        $this->update();
        $perso->update();
    }


    protected function subitCombat($idAttaquant, $riposte = false)
    {
        // Rôle : Calcule de déroulement d'un combat
        // Retour : néant
        // Paramètre : 
        //      $idAttaquant : id de l'attaquant
        $attaquant = new personnage($idAttaquant);
        $log = new log();
        $esquive = false;

        // Si agi défenseur = force attaquant +3, esquive du défenseur
        if ($this->valeurs["agilite"] >= $attaquant->get("force") + 3) {
            $esquive = true;
            $this->esquive($attaquant);
            // Si force attaquant >= 10, force attaquant - 1, res attanquant +1
            if ($attaquant->get("force") >= 10) {
                $attaquant->changeResFor("res");
            }
        }

        // Si force défenseur > force attaquant, lance riposte
        else if ($this->valeurs["force"] > $attaquant->get("force")) {
            $log->logRiposte($attaquant, $this);
            $attaquant->subitCombat($this->id, true);
        }

        // Si res def >= for attaquant, se défend victoire défenseur
        else if ($this->valeurs["resistance"] >= $attaquant->get("force")) {
            $log->logDefense($this, $attaquant);
            $victoire = "defenseur";
        }

        // Si res defenseur < force attaquant, victoire attaquant
        else if ($this->valeurs["resistance"] < $attaquant->get("force")) {
            $victoire = "attaquant";
        }

        if (isset($victoire)) {
            // Si victoire attaquant
            if ($victoire == "attaquant") {
                $attaquant->victoireAttaquant($this, $riposte);
            }
            // Si victoire defenseur 
            if ($victoire == "defenseur") {
                $this->victoireDefenseur($attaquant, $riposte);
            }
        }

        // Calcule pv att et pv def et demande log

    }

    protected function victoireDefenseur($attaquant, $riposte)
    {
        // Rôle : Calcule les stats de la victoire de l'attaquant
        // Retour : néant
        // Paramètres :
        //      $defenseur : objet de la classe personnage donné
        //      $riposte : true ou false

        $pvAttaquant = $attaquant->get("pv");
        $log = new log();

        // Si riposte, pv attaquant - 2
        if ($riposte) {
            $pvAttaquant -= 2;
        } else {
            // Sinon pv attaquant -1
            $pvAttaquant--;
        }
        $log->logDefaite($attaquant, $this, $attaquant->get("pv") - $pvAttaquant);
        $attaquant->set("pv", $pvAttaquant);
        if ($attaquant->get("pv") <= 0) {
            $log->logTue($this, $attaquant);
        }
        $attaquant->update();
    }

    protected function victoireAttaquant($defenseur, $riposte)
    {
        // Rôle : Calcule les stats de la victoire de l'attaquant
        // Retour : néant
        // Paramètres :
        //      $defenseur : objet de la classe personnage donné
        //      $riposte : true ou false
        $pvAttaquant = 0;
        $log = new log();

        // Si riposte, pv attaquant +1
        if ($riposte) {

            $pvAttaquant++;
        }

        // si agi att < 15, agi +1
        if ($this->valeurs["agilite"] < 15) {
            $this->valeurs["agilite"]++;
            $this->logGainAgi();
        } else {
            // Sinon pv +1

            $pvAttaquant++;
        }

        // Si pv def <= force attaquant - res def, pv attaquant + pv def
        if ($defenseur->get("pv") <= $this->valeurs["force"] - $defenseur->get("resistance")) {
            $pvAttaquant += $defenseur->get("pv");
            $mort = "oui";
        }
        if ($this->valeurs["pv"] < 100) {
            $log->logGainPV($this, $pvAttaquant);
        }

        $this->valeurs["pv"] += $pvAttaquant;
        if ($this->valeurs["pv"] > 100) {
            $this->valeurs["pv"] = 100;
        }

        $pvDefenseur = $defenseur->get("pv") - $this->valeurs["force"] - $defenseur->get("resistance");

        $defenseur->set("pv", $pvDefenseur);
        $log->logDefaite($defenseur, $this, $this->valeurs["force"] - $defenseur->get("resistance"));
        if (isset($mort)) {
            $log->logTue($this, $defenseur);
        }
        $this->update();
        $defenseur->update();
    }

    protected function esquive($attaquant)
    {
        // Rôle : Créer un log d'esquive et retire un point d'agilité
        // Retour : néant
        // Paramètres :
        //      $attaquant : objet personnage donné
        $this->logEsquive($attaquant);
        $this->set("agilite", $this->valeurs["agilite"] - 1);
        $this->update();
    }



    protected function setCarac()
    {
        // Rôle: définit la valeur des attributs non pris en compte dans le formulaire
        // Retour : néant
        // Paramètre : néant

        $this->valeurs["force"] = $this->valeurs["force_base"];
        $this->valeurs["agilite"] = $this->valeurs["agilite_base"];
        $this->valeurs["resistance"] = $this->valeurs["resistance_base"];
        $this->valeurs["pv"] = 100;
        $this->valeurs["salle"] = 0;
    }


    protected function loadByPseudo($pseudo)
    {
        // Rôle : Charge dans l'objet courant la ligne correspondant au pseudo dans la BDD
        // Retour : true ou false
        // Paramètres :
        //      $pseudo : pseudo à rechercher
        $champs = $this->getChampsSelect();
        $sql = "SELECT `id`, $champs FROM `$this->table` WHERE `pseudo` = :pseudo";

        $req = $this->executeRequest($sql, [":pseudo" => $pseudo]);
        if ($req == false) {
            $this->id = 0;
            return false;
        }
        $tab = $req->fetchAll(PDO::FETCH_ASSOC);
        if (empty($tab)) {
            return false;
        }
        $tab = $tab[0];

        $this->loadFromArray($tab);
        $this->id = $tab["id"];
        return true;
    }
}
