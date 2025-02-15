<?php
function presta($id_gioco, $id_utente) {
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "giochi";
    $messaggio = "";

    // Crea connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Controlla il numero di prestiti esistenti
    $sql = "SELECT COUNT(*) as totale FROM prestiti WHERE id_utente = '$id_utente'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc(); // serve per contare le effettive righe trovate

    // Verifica se l'utente ha già chiesto un prestito
    if ($row['totale'] > 0) {
        $messaggio = "Non puoi prendere in prestito più di un gioco, consegna prima quello che hai già preso.";
    } else {
        // Esegui query di aggiornamento
        $sql = "UPDATE `giochi` SET `isPrestato` = 1 WHERE `id` = $id_gioco";
        $conn->query($sql);

        $sql = "INSERT INTO prestiti (id_gioco, id_utente) VALUES ('$id_gioco', '$id_utente')";
        $conn->query($sql);

        // Chiudi connessione
        $conn->close();

        // Reindirizza alla pagina dei prestiti
        header("Location: /prestito.php");
        exit; // Assicurati di terminare l'esecuzione dello script dopo il reindirizzamento
    }

    return $messaggio;
}

function ritorna($id_gioco, $id_utente){
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "giochi";
    $messaggio = "";

    // Crea connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Controlla il numero di prestiti esistenti
    $sql = "SELECT COUNT(*) as totale FROM prestiti WHERE id_utente = '$id_utente'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc(); // serve per contare le effettive righe trovate

    // Verifica se l'utente ha già chiesto un prestito
    if ($row['totale'] == 0) {
        $messaggio = "Non ha giochi prestati";
    } else {
        // Esegui query di aggiornamento
        $sql = "UPDATE `giochi` SET `isPrestato` = 0 WHERE `id` = $id_gioco";
        $conn->query($sql);

        $sql = "DELETE FROM `Giochi`.`prestiti` WHERE `id_utente` = '$id_utente'";
        $conn->query($sql);

        // Chiudi connessione
        $conn->close();

        // Reindirizza alla pagina dei prestiti
        header("Location: /prestito.php");
        exit; // Assicurati di terminare l'esecuzione dello script dopo il reindirizzamento
    }

    return $messaggio;
}

// Inizia la sessione e verifica l'autenticazione
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0) {
    header('Location: /');
    exit;
}

// Importa il file navbar.php
require_once 'navbar.php';

// Controlla se il pulsante è stato cliccato e chiama la funzione presta
$messaggio = "";
if (isset($_POST['prestito']) && isset($_POST['id_utente']) && isset($_POST['id_gioco']) ) {
    $messaggio = presta($_POST['id_gioco'], $_POST['id_utente']);
}
if (isset($_POST['ritorna']) && isset($_POST['id_utente']) && isset($_POST['id_gioco']) ) {
    $messaggio = ritorna($_POST['id_gioco'], $_POST['id_utente']);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista giochi disponibili</title>
</head>
<body>
    <?php mostraNavbar(); ?>
    <h1>Lista giochi disponibili</h1>
    <?php
    // Collega al database
    $conn = new mysqli("localhost", "root", "root", "giochi");
    $sql = "SELECT id, nome_gioco, isPrestato FROM giochi WHERE isPrestato=0";
    $result = $conn->query($sql);

    echo "<p>Giochi disponibili trovati <b>" . $result->num_rows . '<b>';
    if ($result->num_rows > 0) {
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
    $conn->close();
    ?>

    <!-- Lista noleggiati -->
    <h1>Lista giochi in prestito</h1>
    <?php
        // Collega al database
        $conn = new mysqli("localhost", "root", "root", "giochi");
        $sql = "SELECT id, nome_gioco, isPrestato FROM giochi WHERE isPrestato=1";
        $result = $conn->query($sql);

        echo "<p>Giochi che hai noleggiato: <b>" . $result->num_rows . '<b>';
        if ($result->num_rows > 0) {
            echo "<table border='1'><tr><th>Nome</th><th>Azione</th></tr>";
            while($row = $result->fetch_assoc()) {
                $pulsanteRestituisci = '<form method="post">
                                <input type="hidden" name="id_gioco" value="' . $row["id"] . '">
                                <input type="hidden" name="id_utente" value="' . $_SESSION["id_utente"] . '">
                                <input type="submit" name="ritorna" value="Ritorna gioco">
                                </form>';
                echo "<tr><td>" . $row["nome_gioco"]. "</td><td>" . $pulsanteRestituisci . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Non hai giochi in prestito</p>";
        }
        $conn->close();
    ?>

    <?php
    // Mostra il messaggio se necessario
    if (!empty($messaggio)) {
        echo "<p>$messaggio</p>";
    }
    ?>
</body>
</html>
