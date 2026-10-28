<?php

// Includiamo il file di configurazione 
include_once __DIR__ . '/../config.php';

// Classe Database che gestisce la connessione al db tramite PDO

class Database {
    
    // Variabile che conterrà la connessione
    private $conn;

    // Metodo che crea e restituisce la connessione al database
    public function getConnection() {
        
        $this->conn = null; // si inizializza a null per pulire eventuali connessioni precedenti
        
        try {
            // Creiamo la connessione tramite le costanti globali config.php
            // mysql:host --> usa MySQL e connettiti all'host localhost
            $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);            
            // codifica UTF-8 per gestire correttamente i caratteri speciali
            $this->conn->exec("set names utf8");
        
            } catch(PDOException $exception) {
            // mex di errore in caso di fallimento nella connessione 
            echo "Errore di connessione: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>