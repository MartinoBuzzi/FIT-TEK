<?php
    session_start();

    session_unset(); // elimina le variabili di sessione sul server --> SVUOTA l'array 
    
    session_destroy(); // ELIMINA la sessione e il suo ID

    header("Location: index.php"); //rimanda l'utente nella home 
?>