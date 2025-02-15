<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //Verifica se NON si è autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0) {
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
    <title>Lista giochi disponibili</title>
</head>
<body>
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?>

    <h1>Lista giochi disponibili</h1>
    <?php
        //Collegamento al DB
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "giochi";

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Query con JOIN per includere i dati degli utenti
        $sql = "SELECT id, nome_gioco, isPrestato FROM giochi WHERE isPrestato=0";

        $result = $conn->query($sql);

        echo "<p>Giochi disponibili trovati <b>" . $result->num_rows . '<b>';

        if ($result->num_rows > 0) {
            // Stampa dati di ogni riga
            echo "<table border='1'><tr><th>Nome</th><th>Azione</th></tr>";
            while($row = $result->fetch_assoc()) {
                $pulsantePresta = '<form method="post">
                                   <input type="hidden" name="id_gioco" value="' . $row["id"] . '">
                                   <input type="hidden" name="id_utente" value="' . $_SESSION["id_utente"] . '">

                                   <input type="submit" name="prestito" value="Prendi in prestito">
                                   </form>';
                echo "<tr><td>" . $row["nome_gioco"]. "</td><td>" . $pulsantePresta . "</td></tr>";
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

<?php
function presta($id_gioco, $id_utente) {
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "giochi";

    // Crea connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Esegui query di aggiornamento
    $sql = "UPDATE `giochi` SET `isPrestato` = 1 WHERE `id` = $id_gioco";
    $conn->query($sql);

    $sql = "INSERT INTO prestiti (id_gioco, id_utente) VALUES ('$id_gioco', '$id_utente')";
    $conn->query($sql);

    // Chiudi connessione
    $conn->close();
    header("Location: /prestito.php");
}

// Controlla se il pulsante è stato cliccato e chiama la funzione presta
if (isset($_POST['prestito']) && isset($_POST['id_utente']) && isset($_POST['id_gioco']) ) {
    presta($_POST['id_gioco'],$_POST['id_utente'],);
}
?>
