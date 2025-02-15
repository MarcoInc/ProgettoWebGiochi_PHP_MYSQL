<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'navbar.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>HOME PAGE</title>
    <link rel="stylesheet" href="/css/style.css"></head>
<body>    
    <h2 class="titolo">Homepage di Dungeon & Games</h2>
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?> 
    <?php
        //Collegamento al DB
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "giochi";

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Esegui query
        $sql = "SELECT * FROM giochi";
        $result = $conn->query(query: $sql);
        //Stampa quante righe sono state trovate
        echo "<p>Giochi inseriti <b>".$result->num_rows.'</b>';

        $sql = "SELECT * FROM utenti";
        $result = $conn->query($sql);
        echo "<p>Utenti registrati <b>".$result->num_rows.'</b>';

        // Chiudi connessione
        $conn->close();
    ?>
</body>
</html>
