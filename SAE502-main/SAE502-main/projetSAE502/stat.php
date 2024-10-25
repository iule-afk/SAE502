<?php
// Connexion à la base de données
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "gestion_utilisateurs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requêtes SQL pour récupérer les données
// 1. Nombre total de tickets
$totalTicketsQuery = "SELECT COUNT(*) as total FROM tickets";
$totalTicketsResult = $conn->query($totalTicketsQuery);
$totalTickets = $totalTicketsResult->fetch_assoc()['total'];

// 2. Répartition des tickets par urgence
$urgenceQuery = "SELECT urgence, COUNT(*) as count FROM tickets GROUP BY urgence";
$urgenceResult = $conn->query($urgenceQuery);
$urgenceLabels = [];
$urgenceData = [];
while ($row = $urgenceResult->fetch_assoc()) {
    $urgenceLabels[] = $row['urgence'];
    $urgenceData[] = $row['count'];
}

// 3. Répartition des tickets par état avec remplacement de null par 'non_assigné'
$etatQuery = "SELECT IFNULL(etat, 'non_assigné') as etat, COUNT(*) as count FROM tickets GROUP BY etat";
$etatResult = $conn->query($etatQuery);
$etatLabels = [];
$etatData = [];
while ($row = $etatResult->fetch_assoc()) {
    $etatLabels[] = $row['etat'];
    $etatData[] = $row['count'];
}

// 4. Répartition des tickets par rapporteur
$rapporteurQuery = "SELECT rapporteur, COUNT(*) as count FROM tickets GROUP BY rapporteur";
$rapporteurResult = $conn->query($rapporteurQuery);
$rapporteurLabels = [];
$rapporteurData = [];
while ($row = $rapporteurResult->fetch_assoc()) {
    $rapporteurLabels[] = $row['rapporteur'];
    $rapporteurData[] = $row['count'];
}

// 5. Répartition des tickets par entreprise
$entrepriseQuery = "SELECT nom_entreprise, COUNT(*) as count FROM tickets GROUP BY nom_entreprise";
$entrepriseResult = $conn->query($entrepriseQuery);
$entrepriseLabels = [];
$entrepriseData = [];
while ($row = $entrepriseResult->fetch_assoc()) {
    $entrepriseLabels[] = $row['nom_entreprise'];
    $entrepriseData[] = $row['count'];
}

// 6. Nombre total d'utilisateurs
$totaldevQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'dev'";
$totaldevResult = $conn->query($totaldevQuery);
$totaldev = $totaldevResult->fetch_assoc()['total'];

// 7. Nombre de modérateurs
$totalModerateursQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'moderateur'";
$totalModerateursResult = $conn->query($totalModerateursQuery);
$totalModerateurs = $totalModerateursResult->fetch_assoc()['total'];

// 8. Nombre de rapporteurs
$totalRapporteursQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'rapporteur'";
$totalRapporteursResult = $conn->query($totalRapporteursQuery);
$totalRapporteurs = $totalRapporteursResult->fetch_assoc()['total'];

// 9. Nombre de tickets créés par jour
$ticketsPerDayQuery = "SELECT DATE(date_creation) as date, COUNT(*) as count FROM tickets GROUP BY DATE(date_creation) ORDER BY DATE(date_creation)";
$ticketsPerDayResult = $conn->query($ticketsPerDayQuery);
$ticketsPerDayLabels = [];
$ticketsPerDayData = [];
while ($row = $ticketsPerDayResult->fetch_assoc()) {
    $ticketsPerDayLabels[] = $row['date'];
    $ticketsPerDayData[] = $row['count'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Tickets</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 300px;
            height: 300px;
            margin: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            display: inline-block;
            vertical-align: top;
        }
        .total-users {
            font-size: 24px;
            margin: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: fit-content;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<h2>Statistiques des Tickets</h2>

<!-- Affichage des statistiques des utilisateurs -->
<div class="total-users">
    <p><strong>Nombre total de dev :</strong> <?php echo $totaldev; ?></p>
    <p><strong>Nombre de modérateurs :</strong> <?php echo $totalModerateurs; ?></p>
    <p><strong>Nombre de rapporteurs :</strong> <?php echo $totalRapporteurs; ?></p>
</div>

<!-- Graphique 1 : Répartition des tickets par urgence -->
<div class="chart-container">
    <canvas id="urgenceChart"></canvas>
    <h3>
        
        Légende : Répartition des tickets par urgence

    </h3>
</div>

<!-- Graphique 2 : Répartition des tickets par état -->
<div class="chart-container">
    <canvas id="etatChart"></canvas>
    <h3>
        
        Légende : Répartition des tickets par état

    </h3>
</div>

<!-- Graphique 3 : Répartition des tickets par rapporteur -->
<div class="chart-container">
    <canvas id="rapporteurChart"></canvas>
    <h3>
        
        Légende : Répartition des tickets par rapporteur

    </h3>
</div>

<!-- Graphique 4 : Répartition des tickets par entreprise -->
<div class="chart-container">
    <canvas id="entrepriseChart"></canvas>
    <h3>Légende : Répartition des tickets par entreprise

    </h3>
</div>
</div>
<!-- Graphique 5 : Nombre de tickets créés par jour -->
<div class="chart-container">
    <canvas id="ticketsPerDayChart"></canvas>
    <h3>
        
        Légende : Nombre de tickets créés par jour

    </h3>
</div>

<script>
    // Graphique 1 : Répartition des tickets par urgence
    var ctxUrgence = document.getElementById('urgenceChart').getContext('2d');
    var urgenceChart = new Chart(ctxUrgence, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($urgenceLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($urgenceData); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Graphique 2 : Répartition des tickets par état
    var ctxEtat = document.getElementById('etatChart').getContext('2d');
    var etatChart = new Chart(ctxEtat, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($etatLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($etatData); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Graphique 3 : Répartition des tickets par rapporteur
    var ctxRapporteur = document.getElementById('rapporteurChart').getContext('2d');
    var rapporteurChart = new Chart(ctxRapporteur, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($rapporteurLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($rapporteurData); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Graphique 4 : Répartition des tickets par entreprise
    var ctxEntreprise = document.getElementById('entrepriseChart').getContext('2d');
    var entrepriseChart = new Chart(ctxEntreprise, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($entrepriseLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($entrepriseData); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });

        // Graphique 5 : Nombre de tickets créés par jour
    var ctxTicketsPerDay = document.getElementById('ticketsPerDayChart').getContext('2d');
    var ticketsPerDayChart = new Chart(ctxTicketsPerDay, {
        type: 'bar', // Utilisation d'un graphique à barres
        data: {
            labels: <?php echo json_encode($ticketsPerDayLabels); ?>,
            datasets: [{
                label: 'Nombre de tickets créés',
                data: <?php echo json_encode($ticketsPerDayData); ?>,
                backgroundColor: '#36A2EB'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
