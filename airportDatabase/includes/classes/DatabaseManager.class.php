<?php
/**
 * Gestionnaire d’accès à la base de données.
 *
 * Cette classe centralise la connexion PDO et fournit les méthodes
 * nécessaires pour exécuter des requêtes SQL (sélection, insertion,
 * mise à jour, suppression). Elle charge ses paramètres de connexion
 * depuis un fichier de configuration externe.
 *
 * @author Dylan Manseri
 * @version 1.0
 */
class DatabaseManager
{
    /**
     * Nom d'hôte du serveur SQL.
     * @var string
     */
    private string $hostname;

    /**
     * Nom d'utilisateur pour la connexion PDO.
     * @var string
     */
    private string $usernameDB;

    /**
     * Mot de passe associé à l'utilisateur de la base.
     * @var string
     */
    private string $password;

    /**
     * Nom de la base de données à utiliser.
     * @var string
     */
    private string $dbname;

    /**
     * Port TCP du service SQL.
     * @var int
     */
    private int $port;

    /**
     * Instance PDO utilisée pour exécuter les requêtes SQL.
     * @var PDO
     */
    private PDO $dbh;

    /**
     * Initialise le gestionnaire de base de données.
     * Le constructeur charge automatiquement les paramètres de connexion
     * (hôte, port, nom de base, utilisateur, mot de passe) depuis le fichier
     * "secret/config.conf.php", puis initialise la connexion PDO.
     */
    public function __construct()
    {
        $config = require "secret/config.conf.php";
        $this->hostname = $config['hostname'];
        $this->port = $config['port'];
        $this->dbname = $config['dbname'];
        $this->password = $config['password'];
        $this->usernameDB = $config['username'];
    }

    /**
     * Etablit la connection entre le serveur web et la base de donnée
     * @return PDO|null
     */
    public function connection(): ?PDO
    {
        try{
            $dsn =  'pgsql:host='. $this->hostname .
                    ';port='.$this->port .
                    ';dbname='.$this->dbname;
            $this->dbh = new PDO($dsn, $this->usernameDB, $this->password);
        }catch(PDOException $e){
            echo 'Erreur de connexion: ' . $e->getMessage(); // CHANGEZ CETTE LIGNE
            throw $e; // Ajoutez ceci pour voir la stack trace complète
        }
        return null;
    }

    /**
     * Recherche un login user dans la base de donnée
     * @param $login : l'user
     * @return mixed
     */
    public function selectLogin($login){
        $stmt = $this->dbh->prepare("SELECT * FROM Utilisateur WHERE id_user = :id_user");
        $stmt->bindValue(':id_user', $login, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    /**
     * Verifit le mot de passe liée au login
     * @param $user : l'user
     * @param $password : le mot de passe entré haché
     * @return mixed*
     */
    public function selectPassword($user, $password){
        $stmt = $this->dbh->prepare("SELECT * FROM Utilisateur WHERE id_user = :user");
        $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user!==false && password_verify($password, $user['mdp_hache'])){
            return $user;
        }
        return false;
    }

    /**
     * Recherche un login admin
     * @param $fKey : clé étrangère correspondant à l'id de l'utilisateur
     * @return mixed
     */
    public function searchAdmin($fKey){
        $stmt = $this->dbh->prepare("SELECT * FROM Administrateur WHERE id_user = :admin");
        $stmt->bindValue(':admin', $fKey, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    /**
     * Recherche un compte utilisateur selon un login
     * @param $fKey : clé étrangère correspondant à l'id de l'utilisateur
     * @return mixed
     */
    public function searchUser($fKey){
        $stmt = $this->dbh->prepare("SELECT * FROM Utilisateur WHERE id_user = :user");
        $stmt->bindValue(':user', $fKey, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    /**
     * Recherche un passager selon son nom user
     * @param $fKey : clé étrangère correspondant à l'id de l'user
     * @return mixed
     */
    public function searchPassenger($fKey){
        $stmt = $this->dbh->prepare("SELECT * FROM Passager WHERE id_user = :user");
        $stmt->bindValue(':user', $fKey, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    /**
     * Recherche une réservation selon un id passager
     * @param $fKey : clé étrangère correspondant l'identifiant du passager
     * @return mixed
     */
    public function searchReservation($fKey){
        $stmt = $this->dbh->prepare("SELECT * FROM Reservation WHERE id_passager = :pass");
        $stmt->bindValue(':pass', $fKey, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getInstance(): PDO
    {
        return $this->dbh;
    }

    /**
     * Supprime un scanner donnée de la base de donnée
     * @param $scanner : le scanner
     * @return bool
     */
    public function deleteScanner($scanner): bool
    {
        $stmt = $this->dbh->prepare("DELETE FROM Scanner WHERE id_scanner = :id_scanner");
        $stmt->bindValue(':id_scanner', $scanner, PDO::PARAM_STR);
        $ok = $stmt->execute();
        return $ok;
    }

    /**
     * Ajoute un scanner dans la base de donnée selon ses informations
     * @param $id_scanner : l'identifiant du scanner
     * @param $marque : sa marque
     * @param $etat : son etat
     * @return bool
     */
    public function addScanner($id_scanner, $marque, $etat)
    {
        $stmt = $this->dbh->prepare("INSERT INTO Scanner VALUES 
                        (:id_scanner, :marque, :etat)");
        $stmt->bindValue(':id_scanner', $id_scanner, PDO::PARAM_STR);
        $stmt->bindValue(':marque', $marque, PDO::PARAM_STR);
        $stmt->bindValue(':etat', $etat, PDO::PARAM_STR);
        $ok = $stmt->execute();
        return $ok;
    }

    /**
     * Modifie un scanner dans la base de donnée
     * @param $scanner : le scanner
     * @param $modMarque : la nouvelle marque
     * @param $modEtat : le nouveau modèle
     * @return bool
     */
    public function modifyScanner($scanner, $modMarque, $modEtat): bool
    {
        if(empty($modMarque) && empty($modEtat)){
            return false;
        }
        if(empty($modEtat)){
            $stmt = $this->dbh->prepare("UPDATE Scanner SET marque = :marque WHERE id_scanner = :id_scanner");
            $stmt->bindValue(':id_scanner', $scanner, PDO::PARAM_STR);
            $stmt->bindValue(':marque', $modMarque, PDO::PARAM_STR);
            $ok = $stmt->execute();
            return $ok;
        }
        if(empty($modMarque)){
            $stmt = $this->dbh->prepare("UPDATE Scanner SET etat = :etat WHERE id_scanner = :id_scanner");
            $stmt->bindValue(':id_scanner', $scanner, PDO::PARAM_STR);
            $stmt->bindValue(':etat', $modEtat, PDO::PARAM_STR);
            $ok = $stmt->execute();
            return $ok;
        }
        else{
            $stmt = $this->dbh->prepare("UPDATE Scanner SET etat = :etat, marque = :marque WHERE id_scanner = :id_scanner");
            $stmt->bindValue(':id_scanner', $scanner, PDO::PARAM_STR);
            $stmt->bindValue(':etat', $modEtat, PDO::PARAM_STR);
            $stmt->bindValue(':marque', $modMarque, PDO::PARAM_STR);
            $ok = $stmt->execute();
            return $ok;
        }
    }

    /**
     * Verifie l'existence d'un vol dans la base de donnée
     * @param $numVol : numero du vol
     * @return array|false
     */
    public function existFlight($numVol): array|false
    {
        $stmt = $this->dbh->prepare("SELECT * FROM Vol WHERE numero_vol = :numVol");
        $stmt->bindValue(':numVol', $numVol, PDO::PARAM_INT);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);
        return $exist;
    }
}