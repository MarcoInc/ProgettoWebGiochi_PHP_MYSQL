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
    require_once 'config/db.php'
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Dona gioco</title>
</head>
    <body>
        <?php
            //Richiama la la navbar in precedenza importata
            mostraNavbar();
        ?>
        <h2>Donazione gioco</h2>
        <!-- Form di tipo POST -->
        <form method="post">
            Nome gioco: <input type="text" name="nome" required><br><br>
        <input type="submit" name="nome_gioco" value="Aggiungi">
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
            
            // Gestione donazione gioco
            if (isset($_POST['nome_gioco'])) {
                $nuovo_gioco = $_POST['nome'];
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
                    $data_scadenza = date('Y-m-d', timestamp: strtotime('+1 year', strtotime($data)));
                }
                
                // Controlla il numero di copie esistenti
                $sql = "SELECT COUNT(*) AS totale FROM giochi WHERE nome_gioco = '$nuovo_gioco'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc(); //servirà per contare le effettive righe trovate
                
                //Verifica se ci sono 3 o più record trovati del gioco da donare
                if ($row['totale'] >= 3) {
                    echo "Raggiunto il limite di 3 copie per questo gioco!";
                } else {
                    // Prima inserisci il gioco
                    $sql_gioco = "INSERT INTO giochi (nome_gioco, data_acquisto, id_donatore) VALUES ('$nuovo_gioco', '$data', '$id_utente')";
                    // === controlla sia valore e tipo
                    if ($conn->query($sql_gioco) === TRUE) {
                        // Poi inserisci l'abbonamento
                        $sql_abbonamento = "INSERT INTO abbonamenti (id_utente, data_inizio_abbonamento, data_fine_abbonamento, stato)
                                            VALUES ('$id_utente', '$data','$data_scadenza','Regolare')";
                        if ($conn->query($sql_abbonamento) === TRUE) {
                            echo "Gioco donato!";
                            echo "Adesso sei un UTENTE DONATORE. Data di scadenza del tuo abbonamento aggiornata: " . $data_scadenza;
                        }
                    }
                }
            }
            // Chiudi connessione
            $conn->close();
        ?>
    </body>
</html>