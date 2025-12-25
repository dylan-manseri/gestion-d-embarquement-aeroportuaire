<?php
/**
 * ==============================================================
 *  Fichier : privateAdmin.php
 *  Auteur  : Dylan Manseri
 *  Projet  : Site Web Aéroport
 *  Date    : 30-11-2025
 *
 *  Description :
 *      Page dédié aux administrateur, pour ajouter supprimer ou modifier les informations des scanner de la base de donnée.
 *      Permet aussi de voir les informations d'un vol.
 *
 * ==============================================================
 */
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit;
}
$infos = false;
include_once "includes/classes/DatabaseManager.class.php";
if(isset($_POST['delScanner'])){
    $db = new DatabaseManager();
    $db->connection();
    $operation = $db->deleteScanner($_POST['scanner']);
}
else if((isset($_POST['modScanner'])) && (isset($_POST['modMarque'])) && isset($_POST['modEtat'])){
    $db = new DatabaseManager();
    $db->connection();
    $operation = $db->modifyScanner($_POST['modScanner'], $_POST['modMarque'], $_POST['modEtat']);
}
else if(isset($_POST['id_scanner']) && isset($_POST['marque']) && isset($_POST['etat'])){
    $db = new DatabaseManager();
    $db->connection();
    $operation = $db->addScanner($_POST['id_scanner'], $_POST['marque'], $_POST['etat']);
}

$title = "Page administrateur";

include "includes/pageParts/header.php";
include "includes/functions/functionsCsv.php";
$db = new DatabaseManager();
$db->connection();
$admin = $db->searchAdmin($_SESSION["id_user"]);
?>


<h1>Page administrateur</h1>

<section>
    <h2>Bienvenue, <?= $admin['prenom'].' '.$admin['nom'] ?></h2>
    <p>Que voulez-vous faire ?</p>
    <button type="button" onclick="window.location.href='privateAdmin.php?action=scan'">
        Gestion des scanner
    </button>
    <button type="button" id="btnFly">
        Suivre un vol
    </button>
    <form method="get" id="inputVol" style="display:none;">
        <label>
            <input type="text" name="numVol" placeholder="Numéro de vol">
        </label>
        <input type="submit" value="Valider">
    </form>
    <?php if (isset($operation)): ?>
        <?php if ($operation): ?>
            <p style="color: green;">Opération réussite !</p>
        <?php else: ?>
            <p style="color: red;">L'opération a échoué...</p>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php if(isset($_GET['action'])):?>
    <section>
        <h2>Panneau de configuration</h2>
        <?php if($_GET['action'] == 'scan'):?>
            <button type="button" id="btnAddScan">Ajouter un scanner</button>
            <button type="button" id="btnDelScan">Supprimer un scanner</button>
            <button type="button" id="btnModScan">Modifier un scanner</button>

            <div id="createScan" style="display:none;">
                <h3>Creation d'un scanner</h3>
                <form method="post">
                    <label>
                        Identifiant du scanner
                        <input type="text" name="id_scanner" placeholder="SCAN" required>
                    </label>
                    <label>
                        Marque
                        <input type="text" name="marque" placeholder="mugiwara-entreprise" required>
                    </label>
                    <label>
                        Etat
                        <input type="text" name="etat" placeholder="1" required>
                    </label>
                    <input type="submit" value="Valider">
                </form>
            </div>
            <div id="deleteScan" style="display:none;">
                <h3>Choisissez le scanner à supprimer :</h3>
                <form action="privateAdmin.php" method="POST">
                    <?php echo constSelectScan('delScanner'); ?>
                    <label>
                        <input type="submit" name="deleteScan">
                    </label>
                </form>

            </div>
            <div id="modifyScan" style="display:none;">
                <h3>Modification d'un scanner</h3>
                <form action="privateAdmin.php" method="post">
                    <?php echo constSelectScan('modScanner'); ?>
                    <label>
                        Marque
                        <input type="text" name="modMarque" placeholder="mugiwara-entreprise">
                    </label>
                    <label>
                        Etat
                        <input type="text" name="modEtat" placeholder="1">
                    </label>
                    <input type="submit" value="Valider">
                </form>
                <p>Laissez un vide si vous ne voulez pas modifier !</p>
            </div>
        <?php endif;?>
    </section>
<?php endif;?>

<?php if (isset($_GET['numVol'])){
    $db1 = new DatabaseManager();
    $db1->connection();
    $infos = $db1->existFlight($_GET['numVol']);
    if($infos){
        $business = $infos['business_class'] ? 'Business' : 'Economique';
    }
}
    ?>
    <?php if ($infos): ?>
        <section>
            <h2>Information concernant le vol <?= $business . ' ' . $infos['numero_vol'] ?>, <?=$infos['pays_depart']?>-><?=$infos['pays_arrivee']?></h2>

            <p>Depart prévu le <?= $infos['date_depart'] ?> à <?= $infos['horaire_depart'] ?></p>
            <p>Arrivée prévue le <?= $infos['date_arrivee'] ?> à <?= $infos['horaire_arrivee'] ?></p>
            <p>Nombre de passagers : <?= $infos['nombre_passager'] ?></p>
        </section>
    <?php endif ?>


<script>
    document.getElementById("btnFly").addEventListener("click", function(e){
        e.preventDefault();
        document.getElementById("inputVol").style.display = "block";
    });
    document.getElementById("btnAddScan").addEventListener("click", function(e){
        e.preventDefault();
        document.getElementById("deleteScan").style.display = "none";
        document.getElementById("modifyScan").style.display = "none";
        document.getElementById("createScan").style.display = "block";
    });
    document.getElementById("btnDelScan").addEventListener("click", function(e){
        e.preventDefault();
        document.getElementById("modifyScan").style.display = "none";
        document.getElementById("createScan").style.display = "none";
        document.getElementById("deleteScan").style.display = "block";
    });
    document.getElementById("btnModScan").addEventListener("click", function(e){
        e.preventDefault();
        document.getElementById("createScan").style.display = "none";
        document.getElementById("deleteScan").style.display = "none";
        document.getElementById("modifyScan").style.display = "block";
    });
</script>
</main>
</body>
</html>