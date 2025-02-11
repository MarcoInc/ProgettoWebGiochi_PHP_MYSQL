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
            $servername = "localhost";
            $username = "root";
            $password = "root";
            $dbname = "giochi";
            // Crea connessione
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Gestione registrazione
            if (isset($_POST['dona'])) {
                $id_utente = $_SESSION['id_utente'];
                $data = date('Y-m-d');
                $data_scadenza = date('Y-m-d', strtotime('+1 year', strtotime($data)));
                
                $sql = "INSERT INTO abbonamenti (id_utente, data_inizio_abbonamento, data_fine_abbonamento, stato)
                            VALUES ('$id_utente', '$data','$data_scadenza','Regolare')";

                if ($conn->query($sql) === TRUE){
                    echo "Ti sei abbonato!";
                    echo "Adesso sei un UTENTE REGOLARE. Data di scadenza del tuo abbonamento aggiornata: ".$data_scadenza;
                }
            }
            // Chiudi connessione
            $conn->close();
        ?>
    </body>
</html>