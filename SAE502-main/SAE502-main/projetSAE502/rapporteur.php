<?php
session_start();
if ($_SESSION['role'] != 'rapporteur') {
    header("Location: login.php");
    exit();
}
include 'ticket.php';

// Enregistrement du ticket dans la base de données
if (isset($_POST['submit'])) {
    $numeroTicket = genererNumeroTicket();
    $texteTicket = $_POST['incident'];
    $rapporteur = $_SESSION['username']; // récupère le nom de l'utilisateur à partir de la session
    $urgence = $_POST['urgence']; // récupère le niveau d'urgence du ticket à partir des données du formulaire
    $nom_client = $_POST['nom_client'];
    $nom_entreprise = $_POST['nom_entreprise'];
    $titre_ticket = $_POST['titre_ticket'];
    $role = $_SESSION['role'];

    $sql = "INSERT INTO tickets (numero, texte, rapporteur, role, urgence, nom_client, nom_entreprise, titre_ticket) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $numeroTicket, $texteTicket, $rapporteur, $role, $urgence, $nom_client, $nom_entreprise, $titre_ticket);

    if ($stmt->execute()) {
        echo "<p>Le ticket a été enregistré avec succès. Numéro de ticket : " . $numeroTicket . "</p>";
    } else {
        echo "<p>Une erreur est survenue lors de l'enregistrement du ticket.</p>";
    }
}

// Fermeture de la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Rapporteur</title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Ceci est la page réservée aux rapporteurs.</p>

    <h2>Créer un nouveau ticket</h2>
    <form method="post">
        <h4 for="incident"> titre du ticket : <textarea id="titre_ticket" name="titre_ticket"rows="1" cols="30"></textarea></h4>
        <label >Description de l'incident : </label><br>
        <textarea id="incident" name="incident" rows="30" cols="100"></textarea>
        <h3>nom client : <textarea id="nom_client" name="nom_client"rows="1" cols="30"></textarea></h3><h3> nom entreprise : <textarea id="nom_entreprise" name="nom_entreprise" rows="1" cols="30"></textarea></h3>
        <label for="urgence">Niveau d'urgence du ticket :</label><br>
        <select id="urgence" name="urgence">
            <option value="faible">Faible</option>
            <option value="moyen">Moyen</option>
            <option value="eleve">Élevé</option>
        </select><br>
        <input type="submit" name="submit" value="Créer un ticket">
    </form>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>

    <a href="logout.php">Se déconnecter</a>
</body>
</html>