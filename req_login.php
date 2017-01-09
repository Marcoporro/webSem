<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 04/12/2016
 * Time: 23:05
 */

    $email=stripslashes($_POST['email']);
    $password=stripslashes($_POST['password']);

    $dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

    session_start();

    if (!isset($_SESSION['prenom'])) {

        header("Location: main.php");
    }

    $sql = $dbh->query("SELECT COUNT(*) id FROM users WHERE email='$email' AND password='$password'");
    if ($sql->fetchColumn() == 1) {
        // ensuite on requête à nouveau la base pour l'utilisateur qui vient d'être inscrit, et
        $sql = $dbh->query("SELECT u.id, u.email, u.nom, u.prenom, u.couleur, u.profilepic FROM USERS u WHERE u.email='$email' AND u.password='$password'");
        // on récupère la ligne qui nous intéresse avec $sql->fetch(),
        // et on enregistre les données dans la session avec $_SESSION["..."]=...
        $result = $sql->fetch(PDO::FETCH_ASSOC);

        $_SESSION["email"] = $result["email"];
        $_SESSION["password"] = $result["password"];
        $_SESSION["nom"] = $result["nom"];
        $_SESSION["prenom"] = $result["prenom"];
        $_SESSION["tel"] = $result["tel"];
        $_SESSION["website"] = $result["website"];
        $_SESSION["sexe"] = $result["sexe"];
        $_SESSION["birthdate"] = $result["birthdate"];
        $_SESSION["ville"] = $result["ville"];
        $_SESSION["taille"] = $result["taille"];
        $_SESSION["couleur"] = $result["couleur"];
        $_SESSION["profilepic"] = $result["profilepic"];

        header("Location: main.php?rowCount=" . $sql->rowCount());
    } else
    {
        header("Location: main.php");
    }

?>