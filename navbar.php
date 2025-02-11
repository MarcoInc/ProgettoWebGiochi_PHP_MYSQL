<?php
function mostraNavbar() {
    
    if (isset($_SESSION['username'])) {
        echo "Benvenuto ".$_SESSION["username"]."!";
        if($_SESSION['isCuratore'])
            echo ' (CURATORE)';
    } else {
        echo "Nessun accesso effettuato";
    }
    
    ?>
    
    <div>
        <a href="/">Homepage</a>
        <a href="/lista_giochi.php">Lista giochi</a>
        <?php
        if (!isset($_SESSION['username'])) {
            echo '<a href="/registrazione.php">Registrati</a>
                <a href="/login.php">Login</a>';
        } else {
            if($_SESSION['isCuratore'])
                echo '<a href="/aggiungi_gioco.php">Aggiungi gioco</a>';
            echo '<a href="/dona_gioco.php">Dona gioco</a>
                <a href="/donazione.php">Donaci 5€</a>
                <a href="/logout.php">Logout</a>
                ';
        }
        ?>
    </div>
    <?php
}
?>