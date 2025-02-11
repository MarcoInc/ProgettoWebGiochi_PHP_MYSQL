<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    //importo il file navbar.php e tutte le sue funzioni e contenuto
    require_once 'navbar.php';

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
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "giochi";

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

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "Login effettuato con successo! Benvenuto ".$row["username"]."!";
            $_SESSION['authenticated']=true;
            $_SESSION['username']=$username;
            $_SESSION['id_utente']=$row['id'];
            $_SESSION['isCuratore']=(bool)$row['isCuratore'];
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