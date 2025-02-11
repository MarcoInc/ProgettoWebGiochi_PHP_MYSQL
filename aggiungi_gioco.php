<?php
//Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();

    //Questa pagina deve essere visibile solo ai Curatori
    if($_SESSION['isCuratore']==0)
        //se si è utenti normali si verrà reindirizzati nella home
        header('Location: /');
        
    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'navbar.php';
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
            $servername = "localhost";
            $username = "root";
            $password = "root";
            $dbname = "giochi";
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