<?php
// Configurazione Database per Altervista
define('DB_HOST', 'localhost');
define('DB_NAME', 'my_fittek'); // Nome DB standard su Altervista (my_ + il tuo username)
define('DB_USER', 'fittek');    // Il tuo username Altervista
define('DB_PASS', '');          // Su Altervista la password di default per il DB è vuota

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errore di connessione al database: " . $e->getMessage());
}
?>