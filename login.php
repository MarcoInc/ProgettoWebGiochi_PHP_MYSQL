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
    require_once 'templates/navbar.php';
    //Importo le costanti per usare le credenziali per il db
    require_once 'config/db.php'

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <?php   
        //Richiama la la navbar in precedenza importata
        mostraNavbar();
    ?> 
    <h2>Accedi</h2>
    <!-- Form di tipo POST -->
    <form method="post">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" name="login" value="Accedi">
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

        // Controlla connessione
        if ($conn->connect_error) {
            die("Connessione fallita: " . $conn->connect_error);
        }

        // Gestione login
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Verifica credenziali
            $sql = "SELECT * FROM utenti WHERE username = '$username' AND password = '$password'";
            $result = $conn->query($sql);

                //se ne ha trovato uno
            if ($result->num_rows > 0) {
                //prende la riga trovata
                $row = $result->fetch_assoc();
                echo "Login effettuato con successo! Benvenuto ".$row["username"]."!";

                $_SESSION['authenticated']=true;
                $_SESSION['username']=$username;
                $_SESSION['id_utente']=$row['id'];
                $_SESSION['isCuratore']=(bool)$row['isCuratore'];
                $_SESSION['isAdmin']=(bool)$row['isAdmin'];

                //ti riporta nalla homepage che sta in /
                header('Location: /');
            } else {
                echo "Username o password non validi!";
            }
        }

        // Chiudi connessione
        $conn->close();
    ?>
</body>
</html>