<?php
//Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();

    //Verifica se si è autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0) {
        header('Location: /');
        exit;
    }
    
    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'templates/navbar.php';
    //Importo le costanti per usare le credenziali per il db
    require_once 'config/db.php';

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Dona 5€</title>
</head>
    <body>
        <?php
            //Richiama la la navbar in precedenza importata
            mostraNavbar();
        ?>
        <h2>Dona 5€</h2>
                <!-- Form di tipo POST -->
        <form method="post">
        <input type="submit" name="dona" value="dona">
        </form>
        <?php
             //Collegamento al DB
            //Uso le costanti usati nel file in config/db.php
            $servername = DB_HOST;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = DB_NAME;
            // Crea connessione
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Gestione registrazione
            if (isset($_POST['dona'])) {
                $id_utente = $_SESSION['id_utente'];
                $data = date('Y-m-d');
                
                // Verifica se esiste già un abbonamento
                $sql = "SELECT data_fine_abbonamento FROM abbonamenti WHERE id_utente = '$id_utente' ORDER BY data_fine_abbonamento DESC LIMIT 1";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $data_scadenza_esistente = $row['data_fine_abbonamento'];
                    
                    // Calcola la nuova data di scadenza
                    $data_scadenza = date('Y-m-d', strtotime('+1 year', 
                        strtotime(max($data, $data_scadenza_esistente))));
                } else {
                    // Primo abbonamento
                    $data_scadenza = date('Y-m-d', strtotime('+1 year', strtotime($data)));
                }
                
                // Aggiorna l'abbonamento
                $sql = "INSERT INTO abbonamenti (id_utente, data_inizio_abbonamento, data_fine_abbonamento, stato)
                        VALUES ('$id_utente', '$data', '$data_scadenza', 'Regolare')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "Ti sei abbonato!";
                    $_SESSION['donazione']=true;
                    echo "Adesso sei un UTENTE REGOLARE. Data di scadenza del tuo abbonamento aggiornata: " . $data_scadenza;
                    require_once 'level_up.php';
                    header('Location: level_up.php');
                    $_SESSION['donazione']=false;
                }
            }
            // Chiudi connessione
            $conn->close();
        ?>
    </body>
</html>