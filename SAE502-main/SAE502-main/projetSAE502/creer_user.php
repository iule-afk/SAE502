<?php
$servername = "127.0.0.1";
$username = "admin";
$password = "admin";
$dbname = "gestion_utilisateurs";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

function creerUtilisateur($conn, $username, $password, $role) {
    // Hachage du mot de passe
    //$passworddeff = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        echo "<p>L'utilisateur a été créé avec succès.</p>";
    } else {
        echo "<p>Une erreur est survenue lors de la création de l'utilisateur.</p>";
    }
    $stmt->close();
    
}
function supprimerUtilisateur($conn, $username) {
    $sql = "DELETE FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        echo "<p>L'utilisateur a été supprimé avec succès.</p>";
    } else {
        echo "<p>Une erreur est survenue lors de la suppression de l'utilisateur.</p>";
    }
    $stmt->close();
}


?>