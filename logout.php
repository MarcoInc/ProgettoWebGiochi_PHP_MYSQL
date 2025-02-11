<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    
    //Verifica se NON si è autenticati
    if($_SESSION['authenticated']==0)
        //se non si è autenticati si verrà reindirizzati nella home
        header('Location: /');

    //Svuota i vari valori da _SESSION[]
    unset($_SESSION['username']);
    unset($_SESSION['authenticated']);
    unset($_SESSION['isCuratore']);
    //e cancella la sessione
    session_destroy();
    //riporta nella homepage
    header('Location: index.php');
    exit;
?>