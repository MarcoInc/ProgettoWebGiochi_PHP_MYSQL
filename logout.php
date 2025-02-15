<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    
    //Verifica se NON si è autenticati
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == 0) {
        header('Location: /');
        //esce dalla pagina di logout
        exit;
    }

    //Svuota i vari valori da _SESSION[]
    unset($_SESSION['username']);
    unset($_SESSION['authenticated']);
    unset($_SESSION['isCuratore']);
    //e cancella la sessione
    session_destroy();
    //riporta nella homepage
    header(header: 'Location: /');
    exit;
?>