<?php
//Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();

    //Questa pagina deve essere visibile solo ai Curatori e autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0 || $_SESSION['isCuratore'] == 0) {
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
<title>Aggiungi gioco</title>
</head>
    <body>
        <?php
            //Richiama la la navbar in precedenza importata
            mostraNavbar();
        ?>
        <h2>Aggiunta gioco</h2>
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
            // Gestione registrazione
            if (isset($_POST['nome_gioco'])) {
                $nuovo_gioco = $_POST['nome'];
                $data = date('Y-m-d');

                // Controlla il numero di copie esistenti
                $sql = "SELECT COUNT(*) as totale FROM giochi WHERE nome_gioco = '$nuovo_gioco'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc(); //servirà per contare le effettive righe trovate
                
                //Verifica se ci sono 3 o più record trovati del gioco da aggiungere
                if ($row['totale'] >= 3) {
                    echo "Raggiunto il limite di 3 copie per questo gioco!";
                } else {
                    // Inserisci nuovo gioco
                    $sql = "INSERT INTO giochi (nome_gioco, data_acquisto) VALUES ('$nuovo_gioco', '$data')";
                
                    if ($conn->query($sql) === TRUE)
                        echo "Gioco aggiunto!";
                }
            }
            // Chiudi connessione
            $conn->close();
        ?>
    </body>
</html>