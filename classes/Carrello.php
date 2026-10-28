<?php

// gestisce tutte le operazioni sul carrello dell'utente nel DB

class Carrello {

    private $conn;
    private $table_name = "carrello";

    // dependency injection: connessione ricevuta dall'esterno come in Prodotto.php
    public function __construct($db) {
        $this->conn = $db;
    }

    // READ — legge tutti i prodotti nel carrello di un utente
    // JOIN con prodotti per ottenere nome, prezzo e immagine direttamente
    public function read($id_utente) {
        $query = "SELECT c.id, c.id_prodotto, c.quantita,
                         p.nome, p.prezzo, p.immagine, p.categoria,
                         (p.prezzo * c.quantita) AS subtotale
                  FROM " . $this->table_name . " c
                  JOIN prodotti p ON c.id_prodotto = p.id
                  WHERE c.id_utente = :id_utente
                  ORDER BY c.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utente', $id_utente);
        $stmt->execute();
        return $stmt;
    }

    // ADD — aggiunge un prodotto al carrello
    // se è già presente incrementa la quantità invece di duplicare la riga
    // ON DUPLICATE KEY UPDATE funziona grazie al UNIQUE KEY (id_utente, id_prodotto) nella tabella
    public function add($id_utente, $id_prodotto) {
        $query = "INSERT INTO " . $this->table_name . " (id_utente, id_prodotto, quantita)
                  VALUES (:id_utente, :id_prodotto, 1)
                  ON DUPLICATE KEY UPDATE quantita = quantita + 1";

        $stmt = $this->conn->prepare($query);

        $id_utente  = htmlspecialchars(strip_tags($id_utente));
        $id_prodotto = htmlspecialchars(strip_tags($id_prodotto));

        $stmt->bindParam(':id_utente',  $id_utente);
        $stmt->bindParam(':id_prodotto', $id_prodotto);

        return $stmt->execute();
    }

    // UPDATE — aggiorna la quantità di un prodotto nel carrello
    // se la quantità arriva a 0 la riga viene eliminata direttamente in JavaScript
    public function updateQuantita($id, $id_utente, $quantita) {
        $query = "UPDATE " . $this->table_name . "
                  SET quantita = :quantita
                  WHERE id = :id AND id_utente = :id_utente";

        $stmt = $this->conn->prepare($query);

        $id         = htmlspecialchars(strip_tags($id));
        $id_utente  = htmlspecialchars(strip_tags($id_utente));
        $quantita   = htmlspecialchars(strip_tags($quantita));

        $stmt->bindParam(':quantita',  $quantita);
        $stmt->bindParam(':id',        $id);
        $stmt->bindParam(':id_utente', $id_utente);

        return $stmt->execute();
    }

    // REMOVE — rimuove un singolo prodotto dal carrello
    // il controllo su id_utente impedisce che un utente elimini righe di un altro
    public function remove($id, $id_utente) {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = :id AND id_utente = :id_utente";

        $stmt = $this->conn->prepare($query);

        $id        = htmlspecialchars(strip_tags($id));
        $id_utente = htmlspecialchars(strip_tags($id_utente));

        $stmt->bindParam(':id',        $id);
        $stmt->bindParam(':id_utente', $id_utente);

        return $stmt->execute();
    }

    // COUNT — restituisce il numero totale di articoli nel carrello (somma delle quantità)
    // usato per aggiornare il badge nella navbar
    public function count($id_utente) {
        $query = "SELECT COALESCE(SUM(quantita), 0) AS totale
                  FROM " . $this->table_name . "
                  WHERE id_utente = :id_utente";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utente', $id_utente);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['totale'];
    }
}

?>
