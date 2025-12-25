<?php
/**
 * Fichier de fonctions utilitaire à la connexion de l'utilisateur ou l'affichage.
 *
 * @author Dylan Manseri
 * @version 1.0
 */
declare(strict_types=1);
include_once "includes/classes/DatabaseManager.class.php";

/**
 * Fais la recherche du compte dans la base de donnée pour validé la connexion
 * @param string $login
 * @param string $password
 * @return array|bool
 */
function checkUser(string $login, string $password): array|bool {
    $db = new DatabaseManager();
    $db->connection();
    $user = $db->selectLogin($login);
    $user = $db->selectPassword($user['id_user'], $password);
    return $user;
}

/**
 * Construit un select listant les différents scanners
 * @param $name
 * @return string
 */
function constSelectScan($name): string
{
    $db = new DatabaseManager();
    $db->connection();
    $dbh = $db->getInstance();
    $stmt = $dbh->query("SELECT * FROM Scanner");
    $scanners = $stmt->fetchAll();
    $select = '<select name="'.$name.'">';
    foreach($scanners as $scanner){
        $select .= '<option value="'.$scanner['id_scanner'].'">'.$scanner['id_scanner'].'- '.$scanner['marque'].'</option>';
    }
    $select .= '</select>';
    return $select;
}

?>