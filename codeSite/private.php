<?php
/**
 * ==============================================================
 *  Fichier : private.php
 *  Auteur  : Dylan Manseri
 *  Projet  : Site Web Aéroport
 *  Date    : 30-11-2025
 *
 *  Description :
 *      Page dédié aux passagers, pour voir les informations lié à leur reservation
 *
 * ==============================================================
 */
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'user'){
    header("Location: index.php");
    exit;
}
$title = "Page utilisateur";
include "includes/classes/DatabaseManager.class.php";
include "includes/pageParts/header.php";
$db = new DatabaseManager();
$db->connection();
$user = $db->searchUser($_SESSION["id_user"]);
$pass = $db->searchPassenger($user["id_user"]);
$res = $db->searchReservation($pass['id_passager']);
$business = $res['business'] ? 'Business' : 'Commercial';
?>
<style>
    h1 {
        font-size: 32px;
        margin-bottom: 20px;
        font-weight: 700;
        text-align:center;
    }

    h2 {
        font-size: 22px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    section {
        margin: 40px auto;   /* centre horizontalement */
        max-width: 650px;
        text-align=center;
        background: white;
        padding: 25px 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        line-height: 1.55;
    }

    p {
        font-size: 16px;
        margin: 8px 0;
    }

    strong {
        font-weight: 600;
    }
</style>
<h1>Bonjour, <?= $pass['prenom'].' '.$pass['nom'] ?></h1>

<section>
    <h2>Information concernant le vol <?= $business.' '.$res['numero_vol'] ?></h2>
    <p>Votre numéro de réservation : <?= $res['num_res'] ?></p>
    <p>Votre place à bord : <?= $res['siege'] ?></p>
    <p>Votre nombre de baggage : <?= $res['nb_bag'] ?></p>
</section>

</main>
</body>
</html>