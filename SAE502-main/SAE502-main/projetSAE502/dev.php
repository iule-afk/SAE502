<?php
session_start();
if ($_SESSION['role'] != 'dev') {
    header("Location: login.php");
    exit();
}

include 'ticket.php';
include 'creer_user.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);

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

if (isset($_POST['creer_user'])) {
    $username = $_POST['username'];
    $passeword = $_POST['password'];
    $role = $_POST['role'];

    creerUtilisateur($conn, $username, $passeword, $role);
}

// Suppression d'un utilisateur
if (isset($_POST['delete_user'])) {
    $username = $_POST['nom_utilisateur_delete'];

    supprimerUtilisateur($conn, $username);
}

// Assignation d'un ticket
if (isset($_POST['assign_ticket'])) {
    $numeroTicket = $_POST['numero_ticket'];
    $assigne_a = $_SESSION['username'];
    $etat = 'en_cours';
    $date_assignation = date('Y-m-d H:i:s');

    $sql = "UPDATE tickets SET assigne_a = ?, etat = ?, date_assignation = ? WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $assigne_a, $etat, $date_assignation, $numeroTicket);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

// Mise à jour de l'état du ticket
if (isset($_POST['update_status'])) {
    $numeroTicket = $_POST['numero_ticket'];
    $etat = $_POST['etat'];

    $sql = "UPDATE tickets SET etat = ? WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $etat, $numeroTicket);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

// Suppression d'un ticket
if (isset($_POST['delete_ticket'])) {
    $numeroTicket = $_POST['numero_ticket'];

    $sql = "DELETE FROM tickets WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numeroTicket);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

// Récupération des tickets
$sql = "SELECT date_creation, numero, texte, rapporteur, role, urgence, nom_client, nom_entreprise, titre_ticket, assigne_a, etat FROM tickets";
$result = $conn->query($sql);

// Récupération des tickets assignés à l'utilisateur connecté
$sql_assigned = "SELECT date_creation, numero, texte, rapporteur, role, urgence, nom_client, nom_entreprise, titre_ticket, assigne_a, etat FROM tickets WHERE assigne_a = ?";
$stmt_assigned = $conn->prepare($sql_assigned);
$stmt_assigned->bind_param("s", $_SESSION['username']);
$stmt_assigned->execute();
$result_assigned = $stmt_assigned->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Dev</title>
</head>
<body>
    <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Ceci est la page réservée aux développeurs.</p>
    <a href="logout.php">Se déconnecter</a>

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

<h1>Afficher les tickets non assigné</h1>
    <button onclick="toggleTickets()">Afficher les tickets</button>
    <script>
        function toggleTickets() {
            var table = document.getElementById('tickets-table');
            if (table.style.display === 'none' || table.style.display === '') {
                table.style.display = 'table';
            } else {
                table.style.display = 'none';
            }
        }
        function assignTicket(numeroTicket) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Ticket assigné avec succès.");
                        location.reload();
                    } else {
                        alert("Erreur lors de l'assignation du ticket.");
                    }
                }
            };
            xhr.send("assign_ticket=1&numero_ticket=" + encodeURIComponent(numeroTicket));
        }
        function deleteTicket(numeroTicket) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Ticket supprimé avec succès.");
                        location.reload();
                    } else {
                        alert("Erreur lors de la suppression du ticket.");
                    }
                }
            };
            xhr.send("delete_ticket=1&numero_ticket=" + encodeURIComponent(numeroTicket));
        }
    </script>
        <table id="tickets-table">
            <tr>
                <th>date création ticket</th>
                <th>Numéro</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Rapporteur</th>
                <th>Rôle</th>
                <th>Urgence</th>
                <th>Nom Client</th>
                <th>Nom Entreprise</th>
                <th>Action</th>
                <th>Supprimer</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $assigne_a = $row['assigne_a'] ?? '';
                    $etat = $row['etat'] ?? '';

                    // Vérifie si le ticket est déjà assigné
                    if (empty($assigne_a)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['date_creation']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['numero']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['titre_ticket']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['texte']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['rapporteur']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['urgence']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nom_client']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nom_entreprise']) . "</td>";
                        echo "<td><button onclick='assignTicket(\"" . htmlspecialchars($row['numero']) . "\")'>S'assigner</button></td>";
                        echo "<td><button onclick='deleteTicket(\"" . htmlspecialchars($row['numero']) . "\")'>Supprimer</button></td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='12'>Aucun ticket trouvé</td></tr>";
            }
            ?>
        </table>

<h1>mes tickets assigné</h1>
    <button onclick="toggleAssignedTickets()">mes tickets assigné</button>
    <script>
        function toggleAssignedTickets() {
            var table = document.getElementById('assigned-tickets-table');
            if (table.style.display === 'none' || table.style.display === '') {
                table.style.display = 'table';
            } else {
                table.style.display = 'none';
            }
        }
        function updateStatus(numeroTicket, etat) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("État du ticket mis à jour avec succès.");
                        location.reload();
                    } else {
                        alert("Erreur lors de la mise à jour de l'état du ticket.");
                    }
                }
            };
            xhr.send("update_status=1&numero_ticket=" + encodeURIComponent(numeroTicket) + "&etat=" + encodeURIComponent(etat));
        }
        function deleteTicket(numeroTicket) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Ticket supprimé avec succès.");
                        location.reload();
                    } else {
                        alert("Erreur lors de la suppression du ticket.");
                    }
                }
            };
            xhr.send("delete_ticket=1&numero_ticket=" + encodeURIComponent(numeroTicket));
        }
    </script>
        <table id="assigned-tickets-table">
            <tr>
                <th>date création ticket</th>
                <th>Numéro</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Rapporteur</th>
                <th>Rôle</th>
                <th>Urgence</th>
                <th>Nom Client</th>
                <th>Nom Entreprise</th>
                <th>Assigné à</th>
                <th>État</th>
                <th>Supprimer</th>
            </tr>
            <?php
            if ($result_assigned->num_rows > 0) {
                while($row = $result_assigned->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['date_creation']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['numero']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['titre_ticket']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['texte']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['rapporteur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['urgence']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_client']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_entreprise']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['assigne_a']) . "</td>";
                    echo "<td>";
                    echo "<select onchange='updateStatus(\"" . htmlspecialchars($row['numero']) . "\", this.value)'>";
                    echo "<option value='en_cours' " . ($row['etat'] == 'en_cours' ? 'selected' : '') . ">En cours</option>";
                    echo "<option value='en_attente' " . ($row['etat'] == 'en_attente' ? 'selected' : '') . ">En attente</option>";
                    echo "<option value='termine' " . ($row['etat'] == 'termine' ? 'selected' : '') . ">Terminé</option>";
                    echo "<td><button onclick='deleteTicket(\"" . htmlspecialchars($row['numero']) . "\")'>Supprimer</button></td>";
                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12'>Aucun ticket assigné trouvé</td></tr>";
            }
            ?>
        </table>

</body>
</html>