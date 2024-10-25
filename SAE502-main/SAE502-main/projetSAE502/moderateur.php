<?php
session_start();
if ($_SESSION['role'] != 'moderateur') {
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

$sql_pri_en_charge ="SELECT date_creation, numero, texte, rapporteur, role, urgence, nom_client, nom_entreprise, titre_ticket, assigne_a, etat FROM tickets WHERE assigne_a IS NOT NULL AND assigne_a != ''";
$result_pri_en_charge = $conn->query($sql_pri_en_charge);

// Fonction pour filtrer les tickets
function filtrerTickets($conn, $criteria) {
    $sql = "SELECT date_creation, numero, texte, rapporteur, role, urgence, nom_client, nom_entreprise, titre_ticket, assigne_a, etat FROM tickets WHERE 1=1";

    if (!empty($criteria['numero'])) {
        $sql .= " AND numero = ?";
    }
    if (!empty($criteria['entreprise'])) {
        $sql .= " AND nom_entreprise LIKE ?";
    }
    if (!empty($criteria['rapporteur'])) {
        $sql .= " AND rapporteur LIKE ?";
    }
    if (!empty($criteria['assigne_a'])) {
        $sql .= " AND assigne_a LIKE ?";
    }
    if (!empty($criteria['etat'])) {
        $sql .= " AND etat = ?";
    }
    if (!empty($criteria['titre'])) {
        $sql .= " AND titre_ticket LIKE ?";
    }
    if (!empty($criteria['urgence'])) {
        $sql .= " AND urgence = ?";
    }
    if (!empty($criteria['nom_client'])) {
        $sql .= " AND nom_client LIKE ?";
    }

    $stmt = $conn->prepare($sql);

    $params = [];
    $types = '';

    if (!empty($criteria['numero'])) {
        $params[] = $criteria['numero'];
        $types .= 's';
    }
    if (!empty($criteria['entreprise'])) {
        $params[] = '%' . $criteria['entreprise'] . '%';
        $types .= 's';
    }
    if (!empty($criteria['rapporteur'])) {
        $params[] = '%' . $criteria['rapporteur'] . '%';
        $types .= 's';
    }
    if (!empty($criteria['assigne_a'])) {
        $params[] = '%' . $criteria['assigne_a'] . '%';
        $types .= 's';
    }
    if (!empty($criteria['etat'])) {
        $params[] = $criteria['etat'];
        $types .= 's';
    }
    if (!empty($criteria['titre'])) {
        $params[] = '%' . $criteria['titre'] . '%';
        $types .= 's';
    }
    if (!empty($criteria['urgence'])) {
        $params[] = $criteria['urgence'];
        $types .= 's';
    }
    if (!empty($criteria['nom_client'])) {
        $params[] = '%' . $criteria['nom_client'] . '%';
        $types .= 's';
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result();
}

// Traitement du formulaire de filtrage
$criteria = [];
if (isset($_POST['filter'])) {
    $criteria = [
        'numero' => $_POST['numero'] ?? '',
        'entreprise' => $_POST['entreprise'] ?? '',
        'rapporteur' => $_POST['rapporteur'] ?? '',
        'assigne_a' => $_POST['assigne_a'] ?? '',
        'etat' => $_POST['etat'] ?? '',
        'titre' => $_POST['titre'] ?? '',
        'urgence' => $_POST['urgence'] ?? '',
        'nom_client' => $_POST['nom_client'] ?? ''
    ];
    $result_filtered = filtrerTickets($conn, $criteria);
} else {
    $result_filtered = filtrerTickets($conn, $criteria);
}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Modérateur</title>
</head>
<body>
    <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Ceci est la page réservée aux modérateurs.</p>
    <a href="logout.php">Se déconnecter</a>
    <a href="stat.php">voir les stats</a>
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

    <h2>Créer un nouvel utilisateur</h2>
    <form method="post">
        <label for="username">Nom d'utilisateur :</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Mot de passe :</label><br>
        <input type="password" id="password" name="password" required><br>
        <label for="role">Rôle :</label><br>
        <select id="role" name="role">
            <option value="dev">Développeur</option>
            <option value="rapporteur">Rapporteur</option>
            <option value="admin">Administrateur</option>
        </select><br>
        <input type="submit" name="creer_user" value="Créer un utilisateur">
    </form>

    <h2>Supprimer un utilisateur</h2>
    <form method="post">
        <label for="nom_utilisateur_delete">Nom d'utilisateur :</label><br>
        <input type="text" id="nom_utilisateur_delete" name="nom_utilisateur_delete" required><br>
        <input type="submit" name="delete_user" value="Supprimer l'utilisateur">
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
    <h1>Filtrer les Tickets</h1>
    <form method="post">
        <label for="numero">Numéro du ticket :</label>
        <input type="text" id="numero" name="numero"><br>

        <label for="entreprise">Nom de l'entreprise :</label>
        <input type="text" id="entreprise" name="entreprise"><br>

        <label for="rapporteur">Rapporteur :</label>
        <input type="text" id="rapporteur" name="rapporteur"><br>

        <label for="assigne_a">Assigné à :</label>
        <input type="text" id="assigne_a" name="assigne_a"><br>

        <label for="etat">État :</label>
        <select id="etat" name="etat">
            <option value="">Tous</option>
            <option value="en_cours">En cours</option>
            <option value="en_attente">En attente</option>
            <option value="termine">Terminé</option>
        </select><br>

        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre"><br>

        <label for="urgence">Urgence :</label>
        <select id="urgence" name="urgence">
            <option value="">Tous</option>
            <option value="faible">Faible</option>
            <option value="moyen">Moyen</option>
            <option value="eleve">Élevé</option>
        </select><br>

        <label for="nom_client">Nom du client :</label>
        <input type="text" id="nom_client" name="nom_client"><br>

        <input type="submit" name="filter" value="Filtrer">
    </form>

    <h2>Résultats du filtrage</h2>
    <table>
        <tr>
            <th>Date de création</th>
            <th>Numéro</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Rapporteur</th>
            <th>Rôle</th>
            <th>Urgence</th>
            <th>Nom Client</th>
            <th>Nom Entreprise</th>
            <th>Action</th>
            <th>Assigné à</th>
            <th>État</th>
            <th>Supprimer</th>
        </tr>
        <?php
        if ($result_filtered->num_rows > 0) {
            while($row = $result_filtered->fetch_assoc()) {
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
                echo "<td>" . htmlspecialchars($row['assigne_a']) . "</td>";
                echo "<td>";
                echo "<select onchange='updateStatus(\"" . htmlspecialchars($row['numero']) . "\", this.value)'>";
                echo "<option value='en_cours' " . ($row['etat'] == 'en_cours' ? 'selected' : '') . ">En cours</option>";
                echo "<option value='en_attente' " . ($row['etat'] == 'en_attente' ? 'selected' : '') . ">En attente</option>";
                echo "<option value='termine' " . ($row['etat'] == 'termine' ? 'selected' : '') . ">Terminé</option>";
                echo "</select>";
                echo "</td>";
                echo "<td><button onclick='deleteTicket(\"" . htmlspecialchars($row['numero']) . "\")'>Supprimer</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='12'>Aucun ticket trouvé</td></tr>";
        }
        ?>
    </table>

    <script>
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


<h1>Afficher les tickets</h1>
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
    <button onclick="toggleAssignedTickets()">Afficher les tickets assignés</button>
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
<h1>Tickets assignés</h1>
    <button onclick="total_ticket_assigner()">Afficher les tickets assignés</button>
    <script>
        function total_ticket_assigner() {
            var table = document.getElementById('total_assigned-tickets-table');
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
        <table id="total_assigned-tickets-table" style="display: none;">
            <tr>
                <th>Date de création</th>
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
            if ($result_pri_en_charge->num_rows > 0) {
                while($row = $result_pri_en_charge->fetch_assoc()) {
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
                    echo "</select>";
                    echo "</td>";
                    echo "<td><button onclick='deleteTicket(\"" . htmlspecialchars($row['numero']) . "\")'>Supprimer</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12'>Aucun ticket assigné trouvé</td></tr>";
            }
            ?>
        </table>

</body>
</html>