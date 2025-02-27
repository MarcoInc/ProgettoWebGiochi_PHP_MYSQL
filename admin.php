<?php
    function setCuratoreTrue($id_utente) {
         //Importo le costanti per usare le credenziali per il db
        require_once 'config/db.php';
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica connessione
        if ($conn->connect_error) {
            die("Connessione fallita: " . $conn->connect_error);
        }
        //data che terrà traccia del prestito
    
        // Esegui query di aggiornamento
        $sql = "UPDATE `utenti` SET `isCuratore` = 1 WHERE `id` = $id_utente";
        $conn->query($sql);


        $sql = "SELECT `username` FROM utenti WHERE `id` = $id_utente";
        $result = $conn->query(query: $sql);
        $row = $result->fetch_assoc();
        // Chiudi connessione
        $conn->close();
        
        // Reindirizza alla pagina dei prestiti
        header("Location: /admin.php");
        return "Utente ".$row['username']." adesso è un CURATORE";
        exit; // Assicurati di terminare l'esecuzione dello script dopo il reindirizzamento
    }
    function setCuratoreFalse($id_utente) {
        //Importo le costanti per usare le credenziali per il db
        require_once 'config/db.php';
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica connessione
        if ($conn->connect_error) {
            die("Connessione fallita: " . $conn->connect_error);
        }   
        // Toglie Curatore dall'utente designato
        $sql = "UPDATE `utenti` SET `isCuratore` = 0 WHERE `id` = $id_utente";
        $conn->query($sql);

        //seleziona l'username
        $sql = "SELECT `username` FROM utenti WHERE `id` = $id_utente";
        $result = $conn->query(query: $sql);
        $row = $result->fetch_assoc();
        // Chiudi connessione
        $conn->close();
        
        // Reindirizza alla pagina dei prestiti
        header("Location: /admin.php");
        return "Utente ".$row['username']." non è più un CURATORE";
        exit; // Assicurati di terminare l'esecuzione dello script dopo il reindirizzamento
    }
   function eliminaUtente($id_utente) {
    //Importo le costanti per usare le credenziali per il db
        require_once 'config/db.php';
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;

        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica connessione
        if ($conn->connect_error) {
            die("Connessione fallita: " . $conn->connect_error);
        }
        //data che terrà traccia del prestito

        // Esegui query di aggiornamento
        $sql = "DELETE FROM `Giochi`.`utenti` WHERE `id` = $id_utente";
        $conn->query($sql);

        $conn->close();
        
        // Reindirizza alla pagina dei prestiti
        header("Location: /admin.php");
        exit; // Assicurati di terminare l'esecuzione dello script dopo il reindirizzamento
    }

    $messaggio="";
     // Controlla se il pulsante è stato cliccato e chiama la funzione presta
    if (isset($_POST['curatoreTRUE']) && isset($_POST['id_utente'])) {
        $messaggio = setCuratoreTrue($_POST['id_utente']);
    }
    if (isset($_POST['curatoreFALSE']) && isset($_POST['id_utente'])) {
        $messaggio = setCuratoreFalse($_POST['id_utente']);
    }
    if (isset($_POST['eliminaUtente']) && isset($_POST['id_utente'])) {
        $messaggio = eliminaUtente($_POST['id_utente']);
    }
    
?>


<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //Questa pagina deve essere visibile solo all'admin e autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0 || $_SESSION['isAdmin'] == 0) {
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
</head>
<body>
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?>

    <h1>Stato utenti</h1>
    <?php
        //Collegamento al DB
        //Uso le costanti usati nel file in config/db.php
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;

        $utenteAttuale=$_SESSION['username'];


        // Crea connessione
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Query con JOIN per includere i dati degli abbonamenti agli utenti
            //Seleziona l'ID dell'utente, il nome utente, se è curatore, lo stato dell'abbonamento e la data di fine abbonamento
        $sql = "SELECT DISTINCT 
            u.id AS id,
            u.username AS username,
            u.isCuratore AS isCuratore,
            COALESCE(a.stato, '') AS stato,
            MAX(COALESCE(a.data_fine_abbonamento, '')) OVER (PARTITION BY u.id) AS data_fine_abbonamento
        FROM utenti u
        LEFT JOIN abbonamenti a ON u.id = a.id_utente
        WHERE u.isCuratore = TRUE
        AND u.isAdmin = FALSE
        AND u.username != '$utenteAttuale'
        GROUP BY u.id, u.username, u.isCuratore, a.stato;"; //si vedono solo chi non è curatore ad esclusione dell'utente attuale e admim

        $result = $conn->query($sql);

        echo "<p>Utenti Curatori: <b>".$result->num_rows.'</b>';

        if ($result->num_rows > 0) {
            // Stampa dati di ogni riga
            echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Curatore</th>
                <th>Stato abbonamento</th><th>Scadenza abbonamento</th><th>Azioni</th></tr>";
            
            while($row = $result->fetch_assoc()) {
                //pulsante che toglie il curatore
                $pulsanteCuratore = '<form method="post">
                                        <input type="hidden" name="id_utente" value="' . $row["id"] . '">
                                        <input type="submit" name="curatoreFALSE" value="Togli CURATORE">
                                    </form>';
                $pulsanteEliminaUtente = '<form method="post">
                <input type="hidden" name="id_utente" value="' . $row["id"] . '">
                <input type="submit" name="eliminaUtente" value="Elimina utente">
                </form>';
                $tmp=$row["isCuratore"] ? "X" : " ";
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["username"]."</td><td>" .$tmp."</td>
                <td>".$row["stato"]."</td><td>".$row["data_fine_abbonamento"]."</td><td>".$pulsanteCuratore.$pulsanteEliminaUtente."</td></tr>";
            }
            echo "</table>";
        }

        $sql = "SELECT DISTINCT 
            u.id AS id,
            u.username AS username,
            u.isCuratore AS isCuratore,
            COALESCE(a.stato, '') AS stato,
            MAX(COALESCE(a.data_fine_abbonamento, '')) OVER (PARTITION BY u.id) AS data_fine_abbonamento
        FROM utenti u
        LEFT JOIN abbonamenti a ON u.id = a.id_utente
        WHERE u.isCuratore = FALSE
        AND u.isAdmin = FALSE
        AND u.username != '$utenteAttuale'
        GROUP BY u.id, u.username, u.isCuratore, a.stato;"; //si vedono solo chi non è curatore ad esclusione dell'utente attuale e admim

        $result = $conn->query($sql);

        echo "<p>Utenti normali: <b>".$result->num_rows.'</b>';

        if ($result->num_rows > 0) {
            // Stampa dati di ogni riga
            echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Curatore</th>
                <th>Stato abbonamento</th><th>Scadenza abbonamento</th><th>Azioni</th></tr>";
            
            while($row = $result->fetch_assoc()) {
                //pulsante che rende curatore
                $pulsanteCuratore = '<form method="post">
                                        <input type="hidden" name="id_utente" value="' . $row["id"] . '">
                                        <input type="submit" name="curatoreTRUE" value="Promuovi a CURATORE">
                                    </form>';
                $pulsanteEliminaUtente = '<form method="post">
                <input type="hidden" name="id_utente" value="' . $row["id"] . '">
                <input type="submit" name="eliminaUtente" value="Elimina utente">
                </form>';
                $tmp=$row["isCuratore"] ? "X" : " ";
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["username"]."</td><td>" .$tmp."</td>
                <td>".$row["stato"]."</td><td>".$row["data_fine_abbonamento"]."</td><td>".$pulsanteCuratore.$pulsanteEliminaUtente."</td></tr>";
            }
            echo "</table>";
        }

        // Chiudi connessione
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