<?php

    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    require_once 'config/db.php';

    
    //Verifica se NON si è autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0 || $_SESSION['donazione']==false){
        header('Location: /');
        //esce dalla pagina di logout
        exit;
    }else
        levelUp($_SESSION['id_utente']);    
    header(header: 'Location: /');
    exit;
?>
<?php
    require_once 'config/db.php';

    function levelUp($id_utente){
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

        // Controlla il numero di prestiti esistenti
        $sql = "UPDATE utenti SET level=level+1 WHERE id = $id_utente";
        $conn->query($sql);

        $sql = "SELECT level FROM utenti WHERE id = $id_utente";
        $result=$conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $_SESSION['level']=$row['level'];
        }

        $conn->close();


    }
?>
