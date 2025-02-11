<?php
//Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    
    //Verifica se si è autenticati
    if($_SESSION['authenticated']==0)
        //se non si è autenticati si verrà reindirizzati nella home
        header('Location: /');
    
    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'navbar.php';
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
        <i type="submit" name="nome_gioco" value="Aggiungi">
        </form>
        <?php
            //Collegamento al DB
            $servername = "localhost";
            $username = "root";
            $password = "root";
            $dbname = "giochi";
            // Crea connessione
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Gestione registrazione
            if (isset($_POST['nome_gioco'])) {
                $nuovo_gioco = $_POST['nome'];
                $id_utente = $_SESSION['id_utente'];
                $data = date('Y-m-d');
                $data_scadenza = date('Y-m-d', strtotime('+1 year', strtotime($data)));

                
                // Controlla il numero di copie esistenti
                $sql = "SELECT COUNT(*) as totale FROM giochi WHERE nome_gioco = '$nuovo_gioco' AND id_donatore = '$id_utente'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc(); //servirà per contare le effettive righe trovate
                
                //Verifica se ci sono 3 o più record trovati del gioco da donare
                if ($row['totale'] >= 3) {
                    echo "Raggiunto il limite di 3 copie per questo gioco!";
                } else {
                    // Inserisci nuovo gioco
                    $sql = "INSERT INTO giochi (nome_gioco, data_acquisto, id_donatore) VALUES ('$nuovo_gioco', '$data', '$id_utente')";
                    $sql = "INSERT INTO abbonamenti (id_utente, data_inizio_abbonamento, data_fine_abbonamento, stato)
                            VALUES ('$id_utente', '$data','$data_scadenza','Regolare')";

                    if ($conn->query($sql) === TRUE)
                        echo "Gioco donato!";
                        echo "Adesso sei un UTENTE DONATORE. Data di scadenza del tuo abbonamento aggiornata: ".$data_scadenza;
                }
            }
            // Chiudi connessione
            $conn->close();
        ?>
    </body>
</html>