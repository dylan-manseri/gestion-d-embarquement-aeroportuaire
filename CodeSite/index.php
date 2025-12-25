<?php
/**
 * ==============================================================
 *  Fichier : index.php
 *  Auteur  : Dylan Manseri
 *  Projet  : Site Web Aéroport
 *  Date    : 30-11-2025
 *
 *  Description :
 *      Script PHP gérant la connexion à la base de données,
 *      la validation des identifiants et la création de session.
 *
 * ==============================================================
 */
if(isset($_SESSION["id_user"])){
    $query = http_build_query($_GET);
    header("Location: private.php?$query");
    exit;
}
include "includes/functions/functionsCsv.php";
if(isset($_POST["id_user"]) && isset($_POST["password"])){
    $user = checkUser($_POST["id_user"], $_POST["password"]);
    if($user !== false){
        var_dump($user);
        session_destroy();
        session_start();
        $_SESSION["id_user"] = $user["id_user"];
        $_SESSION["role"] = $user["role"];
        if($_SESSION['role'] == 'admin'){
            header("Location: privateAdmin.php");
        }
        else{
            header("Location: private.php");
        }
        exit;
    }
    else{
        header("Location: index.php?error=true");
        exit;
    }
}
$title = "Je me connecte";
$style = "styleIndex";
include "includes/pageParts/header.php";

?>
<h1>Connexion</h1>
<?php if(isset($_GET['error']) && $_GET['error']){?>
    <p style="color: red; text-align: center;"><b>Identifiant ou mot de passe incorrect, merci de réssayer.</b></p>
<?php } ?>
<form action="index.php" method="post">
    <label>
        <input type="text" name="id_user" placeholder="Entrer votre nom d'utilisateur">
    </label>
    <label>
        <input type="password" name="password" placeholder="Entrer votre mot de passe">
    </label>
    <input style="width:70px" type ="submit" value="Valider">
</form>

</main>
</body>
</html>
