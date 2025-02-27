<?php

    
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //Questa pagina deve essere visibile solo ai Curatori e autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0 || $_SESSION['isCuratore'] == 0) {
        header('Location: /');
        exit;
    }

    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'templates/navbar.php';
    //Importo le costanti per usare le credenziali per il db
    require_once 'config/db.php'
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista utenti</title>    
    <link rel="stylesheet" href="/css/style.css"></head>
</head>
<body>
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?>

    <h2>Lista Prodotti</h2>
    <?php
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Query con JOIN per includere i dati degli abbonamenti agli utenti
            //Seleziona l'ID dell'utente, il nome utente, se è curatore, lo stato dell'abbonamento e la data di fine abbonamento
        $sql = "SELECT DISTINCT 
                u.id AS id,
                u.username AS username,
                u.isCuratore AS isCuratore,
                u.level AS level,
                COALESCE(a.stato, '') AS stato,
                MAX(COALESCE(a.data_fine_abbonamento, '')) OVER (PARTITION BY u.id) AS data_fine_abbonamento
            FROM utenti u
            LEFT JOIN abbonamenti a ON u.id = a.id_utente
            GROUP BY u.id, u.username, u.isCuratore, a.stato;";

        $result = $conn->query($sql);

        echo "<p>Utenti registrati: <b>".$result->num_rows.'<b>';

        if ($result->num_rows > 0) {
            // Stampa dati di ogni riga
            echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Livello</th><th>Curatore</th>
                <th>Stato abbonamento</th><th>Scadenza abbonamento</th></tr>";
            while($row = $result->fetch_assoc()) {
                $tmp=$row["isCuratore"] ? "X" : " ";
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["username"]."</td><td>" . $row["level"]."</td>
                <td>" .$tmp."</td><td>".$row["stato"]."</td><td>".$row["data_fine_abbonamento"]."</td></tr>";
            }
            echo "</table>";
        }

        // Chiudi connessione
        $conn->close();
    ?>
</body>
</html>