<?php
session_start();
$servername = "127.0.0.1";
$username = "admin";
$password = "admin";
$dbname = "gestion_utilisateurs";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête pour vérifier les informations d'identification
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Vérification du mot de passe
    if ($user && $password == $user['password']) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirection en fonction du rôle
        if ($user['role'] == 'rapporteur') {
            echo $user['role'];
            header("Location: rapporteur.php");
        } elseif ($user['role'] == 'dev') {
            echo $user['role'];
            header("Location: dev.php");
        } elseif ($user['role'] == 'moderateur') {
            echo $user['role'];
            header("Location: moderateur.php");
        }
        exit();
    } else {
        header("Location: login.php");
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
        echo $error;
    }
}
