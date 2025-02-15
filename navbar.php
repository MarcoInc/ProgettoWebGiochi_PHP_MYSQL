<?php
//creo una funzione che richiameremo ad inizio di ogni pagina
function mostraNavbar() {
    //controlla se in _SESSION è definito 'username'
    if (isset($_SESSION['username'])) {
        echo "Benvenuto ".$_SESSION["username"]."!"; //messaggio di benvenuto che mostra l'username
        //verifica se in _SESSION è definito 'isCuratore' -> se si è Curatore
        if($_SESSION['isCuratore'])
            echo ' (CURATORE)'; //Mostra Curatore dopo il messaggio di benvenuto
    } else {
        echo "Nessun accesso effettuato";
    }
    
    ?>
    
    <div>
        <a href="/">Homepage</a>
        <a href="/lista_giochi.php">Lista giochi</a>
        <?php
        //Se non si è loggati mostra i pulsanti di login e registrazione
        if (!isset($_SESSION['username'])) {
            echo '<a href="/registrazione.php">Registrati</a>
                <a href="/login.php">Login</a>';
        } else { //se siamo invece loggati
            //Verifica se si è Curatore
            if($_SESSION['isCuratore']){
                echo '<a href="/lista_utenti.php">Lista utenti</a>'; //Mostra link per aggiungere gioco
                echo '<a href="/aggiungi_gioco.php">Aggiungi gioco</a>'; //Mostra link per aggiungere gioco
            }
            //in ogni caso se si è loggati vengono mostrati i link per donare giochi, donazione e logout
            echo '<a href="/prestito.php">Noleggia giochi</a>
                <a href="/dona_gioco.php">Dona gioco</a>
                <a href="/donazione.php">Donaci 5€</a>
                <a href="/logout.php">Logout</a>
                ';
        }
        ?>
    </div>
    <?php
}
?>