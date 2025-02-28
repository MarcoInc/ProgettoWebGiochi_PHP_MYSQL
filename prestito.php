<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //Verifica se si è autenticati
        //Verifica se è definita e non NULL isset($_SESSION['authenticated'] && che il suo valore sia esattamente true
    if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0) {
        //se si è autenticati riporta nella home
        header('Location: /');
        exit;
    }
    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'templates/navbar.php';
    //Importo le costanti per usare le credenziali per il db
    require_once 'config/db.php'

?>
<?php
    function presta($id_gioco, $id_utente) {
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;
        $messaggio = "";

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica connessione
        if ($conn->connect_error) {
            die("Connessione fallita: " . $conn->connect_error);
        }

        // Controlla il numero di prestiti esistenti
        $sql = "SELECT COUNT(*) AS totale FROM prestiti WHERE id_utente = '$id_utente'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc(); // serve per contare le effettive righe trovate

        // Verifica se l'utente ha già chiesto un prestito
        if ($row['totale'] > 0) {
            $messaggio = "Non puoi prendere in prestito più di un gioco, consegna prima quello che hai già preso.";
        } else {
            //data che terrà traccia del prestito
            $data = date('Y-m-d');
            $data_scadenza_prestito=date('Y-m-d', strtotime("+2 months", strtotime($data)));

            // Esegui query di aggiornamento
            $sql = "UPDATE `giochi` SET `isPrestato` = 1 WHERE `id` = $id_gioco";
            $conn->query($sql);

            $sql = "INSERT INTO prestiti (id_gioco, id_utente, data_prestito,data_scadenza_prestito) VALUES ('$id_gioco', '$id_utente','$data','$data_scadenza_prestito')";
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
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;
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

            $sql = "DELETE FROM `Giochi`.`prestiti` WHERE `id_utente` = $id_utente";
            $conn->query($sql);

            // Chiudi connessione
            $conn->close();

            // Reindirizza alla pagina dei prestiti
            header("Location: /prestito.php");
            exit; // Assicurati di terminare l'esecuzione dello script dopo il reindirizzamento
        }

        return $messaggio;
    }

    

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
        <link rel="stylesheet" href="/css/style.css"></head>
    </head>
    <body>
        <?php mostraNavbar(); ?>
        <h2>Lista giochi disponibili</h2>
        <?php
        // Collega al database
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;
        $messaggio = "";

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT id, nome_gioco, isPrestato FROM giochi WHERE isPrestato=0";
        $result = $conn->query($sql);

        echo "<p>Giochi disponibili trovati <b>" . $result->num_rows . '</b>';
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
        <h2>Lista giochi in prestito</h2>
        <?php
            // Collega al database
            $conn = new mysqli("localhost", "root", "root", "giochi");
            $sql = "SELECT giochi.id AS id_gioco, giochi.nome_gioco AS nome_gioco, giochi.isPrestato AS isPrestato, prestiti.id_utente 
                    AS id_utente, prestiti.data_prestito AS data_prestito, prestiti.data_scadenza_prestito AS data_scadenza_prestito,
                    utenti.id
                    FROM giochi
                    LEFT JOIN prestiti ON giochi.id = prestiti.id_gioco
                    LEFT JOIN utenti ON utenti.id = prestiti.id_utente
                    WHERE giochi.isPrestato=1;
    "; 
            $result = $conn->query($sql);


            echo "<p>Hai in prestito <b>" . $result->num_rows . '</b> giochi</p>';
            if ($result->num_rows > 0) {
                echo "<table border='1'><tr><th>Nome</th><th>Data prestito</th><th>Data scadenza prestito</th><th>Azione</th></tr>";
                while($row = $result->fetch_assoc()) {
                    $pulsanteRestituisci = '<form class="prestito" method="post">
                                    <input type="hidden" name="id_gioco" value="' . $row["id_gioco"] . '">
                                    <input type="hidden" name="id_utente" value="' . $_SESSION["id_utente"] . '">
                                    <input type="submit" name="ritorna" value="Ritorna gioco">
                                    </form>';
                    echo "<tr><td>" . $row["nome_gioco"]. "</td><td>" . $row["data_prestito"]. "</td>
                    <td>" . $row["data_scadenza_prestito"]. "</td><td>" . $pulsanteRestituisci . "</td></tr>";
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
