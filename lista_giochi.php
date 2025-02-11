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
    <title>Lista giochi</title>
</head>
<body>
    <?php   
        mostraNavbar();
    ?>

    

    <h1>Lista Prodotti</h1>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "giochi";

    // Crea connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Query con JOIN per includere i dati degli utenti
    $sql = "SELECT giochi.id, giochi.nome_gioco, utenti.username AS nome_donatore 
            FROM giochi
            LEFT JOIN utenti ON giochi.id_donatore = utenti.id";

    $result = $conn->query($sql);

    echo "<p>Giochi trovati <b>".$result->num_rows.'<b>';

    if ($result->num_rows > 0) {
        // Stampa dati di ogni riga
        echo "<table border='1'><tr><th>ID</th><th>Nome</</th><th>Donatore</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["nome_gioco"]. "</td><td>" . $row["nome_donatore"]. "</td></tr>";
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