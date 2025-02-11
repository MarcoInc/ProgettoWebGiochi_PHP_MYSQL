<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();

    //Verifica se si è autenticati
        //Verifica se è definita e non NULL isset($_SESSION['authenticated'] && che il suo valore sia esattamente true
    if(isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        //se si è autenticati riporta nella home
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
    <title>Registrazione</title>
</head>
<body>
    
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?>
    <h2>Registrazione Utente</h2>
    <!-- Form di tipo POST -->
    <form method="post">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" name="registra" value="Registra">
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
        if (isset($_POST['registra'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Controlla se username esiste
            $sql = "SELECT id FROM utenti WHERE username = '$username'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) { //controlla se ne esiste almeno 1 uguale
                echo "Username già esistente!";
            } else {
                // Inserisci nuovo utente
                $sql = "INSERT INTO utenti (username, password) VALUES ('$username', '$password')";
                if ($conn->query($sql) === TRUE) 
                    echo "Registrazione avvenuta con successo!";
            }
        }

        // Chiudi connessione
        $conn->close();
    ?>
</body>
</html>