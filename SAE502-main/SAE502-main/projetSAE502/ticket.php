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

// Génération du numéro de ticket

// Récupération du dernier numéro de ticket utilisé
$sql = "SELECT MAX(numero) AS dernier_numero FROM tickets";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$dernierNumero = intval(substr($row['dernier_numero'], 4));

// Génération du numéro de ticket
function genererNumeroTicket() {
    global $dernierNumero;
    $prefixe = "INC-";
    $dernierNumero++;
    $numero = str_pad($dernierNumero, 6, '0', STR_PAD_LEFT);
    return $prefixe . $numero;
}
?>