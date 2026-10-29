<?php
class Carrello {
    private $conn;
    private $table_name = "carrello";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($id_utente) {
        $query = "SELECT c.id, c.id_prodotto, p.nome, p.prezzo, p.immagine, p.categoria, c.quantita, (p.prezzo * c.quantita) AS subtotale 
                  FROM " . $this->table_name . " c 
                  JOIN prodotti p ON c.id_prodotto = p.id 
                  WHERE c.id_utente = :id_utente";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_utente", $id_utente);
        $stmt->execute();
        return $stmt;
    }

    public function add($id_utente, $id_prodotto) {
        // Controlla se il prodotto è già nel carrello
        $query = "SELECT id, quantita FROM " . $this->table_name . " WHERE id_utente = :id_utente AND id_prodotto = :id_prodotto";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_utente", $id_utente);
        $stmt->bindParam(":id_prodotto", $id_prodotto);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $nuova_quantita = $row['quantita'] + 1;
            return $this->updateQuantita($row['id'], $id_utente, $nuova_quantita);
        }

        $query = "INSERT INTO " . $this->table_name . " (id_utente, id_prodotto, quantita) VALUES (:id_utente, :id_prodotto, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_utente", $id_utente);
        $stmt->bindParam(":id_prodotto", $id_prodotto);
        return $stmt->execute();
    }

    public function updateQuantita($id, $id_utente, $quantita) {
        $query = "UPDATE " . $this->table_name . " SET quantita = :quantita WHERE id = :id AND id_utente = :id_utente";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantita", $quantita);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_utente", $id_utente);
        return $stmt->execute();
    }

    public function remove($id, $id_utente) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND id_utente = :id_utente";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_utente", $id_utente);
        return $stmt->execute();
    }

    public function count($id_utente) {
        $query = "SELECT SUM(quantita) as totale FROM " . $this->table_name . " WHERE id_utente = :id_utente";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_utente", $id_utente);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['totale'] ? (int)$row['totale'] : 0;
    }
}
?>