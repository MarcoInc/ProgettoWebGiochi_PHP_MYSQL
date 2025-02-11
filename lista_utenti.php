<?php

    
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //Questa pagina deve essere visibile solo ai Curatori e autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0 || $_SESSION['isCuratore'] == 0) {
        header('Location: /');
        exit;
    }

    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista utenti</title>
</head>
<body>
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?>

    <h1>Lista Prodotti</h1>
    <?php
        //Collegamento al DB
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "giochi";

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Query con JOIN per includere i dati degli abbonamenti agli utenti
        $sql = "SELECT utenti.id AS id, utenti.username AS username, 
                    utenti.isCuratore AS isCuratore, abbonamenti.stato AS stato, 
                    abbonamenti.data_fine_abbonamento AS data_fine_abbonamento FROM utenti
                LEFT JOIN abbonamenti ON utenti.id = abbonamenti.id_utente";
       
        $result = $conn->query($sql);

        echo "<p>Utenti registrati: <b>".$result->num_rows.'<b>';

        if ($result->num_rows > 0) {
            // Stampa dati di ogni riga
            echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Curatore</th>
                <th>Stato abbonamento</th><th>Scadenza abbonamento</th></tr>";
            while($row = $result->fetch_assoc()) {
                $tmp=$row["isCuratore"] ? "X" : " ";
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["username"]."</td><td>" .$tmp."</td>
                <td>".$row["stato"]."</td><td>".$row["data_fine_abbonamento"]."</td></tr>";
            }
            echo "</table>";
        } else {
            echo "0 risultati";
        }

        // Chiudi connessione
        $conn->close();
    ?>
</body>
</html>