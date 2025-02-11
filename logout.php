<?php
    //Inizia la sessione -> crea o richiama l'array chiave valore _SESSION[....]
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['authenticated']);
    unset($_SESSION['isCuratore']);
    session_destroy();
    header('Location: index.php');
    exit;
?>