<?php 

// Classe model : classe générique pour héritage

class _model {
    protected $champs = [];
    protected $table = "";
    protected $valeurs = [];
    protected $links = [];
    protected $champsNomComplet = [];
    protected $id = 0;

    function __construct($id = null) {
        // Rôle : charger l'objet d'id donné si id
        // Retour : néant
        // Paramètre :
        //      $id : id donné
        if (isset($id)) {
            $this->loadById($id);
        }
    }
    
        
    

    // Fonctions publiques

    public function set($nomChamp, $val) {
        // Rôle : Mettre à jour ou charger l'attribut valeurs correspondant
        // Retour : true ou false
        // Paramètre : 
        //      $nomChamp : nom du champ où mettre la nouvelle valeur
        //      $val : valeur à attribuer
        if (! isset($this->champs[$nomChamp])) {
            return false;
        }
        $this->valeurs[$nomChamp] = $val;
        return true;
    }

    public function loadById($id) {
        // Rôle : Charge l'objet courant avec la ligne de la table dont la clé primaire est égal à l'id donné
        // Retour : true ou false
        // Paramètres :
        //      $id : id donné
        $champs = $this->getChampsSelect();
        $sql = "SELECT `id`, $champs FROM `$this->table` WHERE `id` = :id";

        $req = $this->executeRequest($sql, [":id"=>$id]);

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



    public function loadFromArray($tab) {
        // Rôle : charge l'objet courant avec les valeurs de $tab
        // Retour : néant
        // Paramètres :
        //      $tab : tableau de valeurs à charger dans l'objet
        foreach ($this->champs as $champ=>$type) {
            if (isset($tab[$champ])) {
                $this->set($champ, $tab[$champ]);
            }
        }
    }


    public function update() {
        // Rôle : Modifie une ligne correspondant à l'objet courant dans la BDD
        // Retour : true si réussi, false sinon
        // Paramètres : néant
        $champs = $this->makeChampsInsertUpdate();
        $param = $this->makeParamArray();
        $param[":id"] = $this->id;
        $sql = "UPDATE `$this->table` SET $champs WHERE `id` = :id";
        $req = $this->executeRequest($sql, $param);

        if ($req == false) {
            $this->id = 0;
            return false;
        }
        return true;
    }


    public function insert() {
        // Rôle : Créer une ligne correspondant à l'objet courant dans la BDD
        // Retour : true si réussi, false sinon
        // Paramètres : néant
        $champs = $this->makeChampsInsertUpdate();
        $param = $this->makeParamArray();
        $sql = "INSERT INTO `$this->table` SET $champs";
        $req = $this->executeRequest($sql, $param);

        if ($req == false) {
            $this->id = 0;
            return false;
        }
        global $bdd;
        $this->id = $bdd->lastInsertId();
        return true;
    }


    public function id() {
        // Rôle : Récupérer l'id de l'objet courant
        // Retour : valeur de l'id
        // Paramètres : néant
        if (empty($this->id)) {
            return 0;
        }
        return $this->id;
    }


    public function get($nomChamp) {
        // Rôle : récupère la valeur du champ, l'objet cible pour les liens, un objet DateTime pour les dates. Par défaut 0 pour le nombre, "" pour les chaines, la date du jour pour les dates
        // Retour : élément du champ de nom indiqué
        // Paramètres :
        //      $nomChamp : nom du champ donné
        foreach ($this->champs as $champ=>$type) {
            if ($nomChamp == $champ){
                if ($type == "link") {
                    $result = $this->getLink($nomChamp);
                } else if ($type == "string") {
                    $result = $this->getString($nomChamp);
                } else if ($type == "number") {
                    $result = $this->getNumber($nomChamp);
                } else {
                    $result = $this->getDate($nomChamp);
                }
            }
        }
        return $result;
    }


    public function html($nomChamp) {
        // Rôle : Retourner la valeur d'un champ en version html
        // Retour : valeur à retourner
        // Paramètres :
        //      $nomChamp : nom du champ à retourner la valeur
        foreach ($this->champs as $nom => $type) {
            if ($nom == $nomChamp) {
                if ($type == "link") {
                    $html = $this->htmlLink($nomChamp);
                } else if ($type == "date") {
                    $html = $this->htmlDate($nomChamp);
                } else if ($type == "number") {
                    $html = $this->htmlNumber($nomChamp);
                } else {
                    $html = $this->htmlString($nomChamp);
                }
            }
        }
        return $html;
    }


    public function nomComplet() {
        // Rôle : renvoie le nom complet (nom prenom) d'une personne
        // Retour : le nom complet
        // Paramètres : néant
        $nomComplet = [];
        foreach ($this->champsNomComplet as $champ) {
            $nomComplet[] = $this->valeurs[$champ];
        }
        return implode(" ", $nomComplet);
    }


    // Fonctions protégé



    protected function tabToTabObj($tab)
    {
        // Rôle : Transforme un tableau de tableaux en tableau d'objets indexé par leur id
        // Retour : tableau d'objets
        // Paramètres :
        //      $tab : tableau simple de tableaux
        $nomClasse = get_class($this);
        $result = [];
        foreach ($tab as $detail) {
            $obj = new $nomClasse;
            $obj->loadFromArray($detail);
            $obj->id = $detail["id"];
            $result[$detail["id"]] = $obj;
        }
        return $result;
    }


    protected function htmlDate($nomChamp) {
        // Rôle : Retourne la valeur html du nom du champ
        // Retour : la valeur du champ (date), date du jour si non existant
        // Paramètres :
        //      $nomChamp : nom du champ à retourner la valeur
        if (empty($this->valeurs[$nomChamp])) {
            $obj = new DateTime();
            $html = htmlentities($obj->format("d/m/Y"));
        } else {
            $obj = new DateTime($this->valeurs[$nomChamp]);
            $html = htmlentities($obj->format("d/m/Y"));
        }
        return $html;
    }


    protected function htmlNumber($nomChamp) {
        // Rôle : Retourne la valeur html du nom du champ
        // Retour : la valeur du champ (number), 0 si non existant
        // Paramètres :
        //      $nomChamp : nom du champ à retourner la valeur
        if (empty($this->valeurs[$nomChamp])) {
            $html = 0;
        } else {
            $html = htmlentities($this->valeurs[$nomChamp]);
        }
        return $html;
    }


    protected function htmlString($nomChamp) {
        // Rôle : Retourne la valeur html du nom du champ
        // Retour : la valeur du champ (string), vide si non existant
        // Paramètres :
        //      $nomChamp : nom du champ à retourner la valeur
        if (empty($this->valeurs[$nomChamp])) {
            $html = "";
        } else {
            $html =nl2br(htmlentities($this->valeurs[$nomChamp]));
        }
        return $html;
    }


    protected function htmlLink($nomChamp) {
        // Rôle : Retourne la valeur html du nom du lien
        // Retour : le nom complet du lien
        // Paramètres :
        //      $nomChamp : nom du champ à retourner la valeur
        if (empty($this->valeurs[$nomChamp])) {
            $html = "";
        } else {
            $target = $this->links["$nomChamp"];
            $obj = new $target();
            $obj->loadById($this->valeurs[$nomChamp]);
            $html = htmlentities($obj->nomComplet());
        }
        return $html;
    }


    private function getDate($nomChamp) {
        // Rôle : retourne la valeur d'un attribut 
        // Retour : objet date, date du jour si non existant
        // Paramètre :
        //      $nomChamp : nom du champ à retourner la valeur
        if (empty($this->valeurs[$nomChamp])) {
            $obj = new DateTime();
        } else {
            $obj = new DateTime();
            $obj = $this->valeurs[$nomChamp];
        }
        return $obj;
    }


    protected function getNumber($nomChamp) {
        // Rôle : retourne la valeur d'un attribut
        // Retour : valeur de l'attribut (number), valeur à 0 si non existant
        // Paramètre :
        //      $nomChamp : nom du champ à retourner la valeur
        if (empty($this->valeurs[$nomChamp])) {
            $obj = 0;
        } else {
            $obj = $this->valeurs[$nomChamp];
        }
        return $obj;
    }


    protected function getString($nomChamp) {
            // Rôle : retourne la valeur d'un attribut
            // Retour : valeur de l'attribut (string), valeur vide si non existant
            // Paramètre :
            //      $nomChamp : nom du champ à retourner la valeur
            if (empty($this->valeurs[$nomChamp])) {
                $obj = "";
            } else {
                $obj = $this->valeurs[$nomChamp];
            }
            return $obj;
        
    }

    protected function getLink($nomChamp) {
        // Rôle : Verifie si un objet est déjà chargé, si non le charge
        // Retour : l'objet donné ou un objet vide si inexistant
        // Paramètre :
        //      $nomChamp : nom du champ qui est un lien
        if (empty($this->valeurs[$nomChamp])) {
            $target = $this->links["$nomChamp"];
            $obj = new $target();
        } else {
                $target = $this->links[$nomChamp];
                $obj = new $target($this->valeurs[$nomChamp]);
                }
        
        return $obj; 
    }


    protected function makeParamArray() {
        // Rôle : Fabrique le tableau des paramètres à injecter dans l'execution de la requête
        // Retour : tableau des paramètres
        // Paramètres : néant
        $param = [];
        foreach ($this->champs as $champ=>$type) {
            if (isset($this->valeurs[$champ])) {
                $param[":$champ"] = $this->valeurs[$champ];
            } else {
                $param[":$champ"] = null;
            }
        }
        return $param;
    }


    protected function makeChampsInsertUpdate() {
        // Rôle : Fabrique le morceau de requête SQL pour les champs et leurs variables
        // Retour : String lisible par SQL pour les champs et leurs valeurs (sous forme de variable)
        // Paramètres : néant
        $champs = [];
        foreach ($this->champs as $champ=>$type) {
            $champs[] = "`$champ` = :$champ";
        }
        return implode(", ", $champs);
    }



    protected function executeRequest($sql, $param = []) {
        // Rôle : execute une requête SQL dans la BDD
        // Retour : la requête si réussi, false sinon
        // Paramètres :
        //      $sql : la requête SQL
        //      $param : tableau des paramètres à injecter dans l'execution de la requête

        global $bdd;
        $req = $bdd->prepare($sql);

        if (! $req->execute($param)) {
            echo "Echec de la requête $sql avec les paramètres : ";
            print_r($param);
            return false;
        }
        return $req;
    }



    protected function getChampsSelect() {
        // Rôle : Construit la liste des champs à chercher au format d'un SELECT d'une requete SQL
        // Retour : string lisible par SQL pour les champs où chercher
        // Paramètres : néant
        $champs = [];
        foreach ($this->champs as $champ=>$type) {
            $champs[] = "`$champ`";
        }
        return implode(", ", $champs);
    }


    
}