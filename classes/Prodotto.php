<?php

// rappr. un prodotto del catalogo shop
// + gestisce tutte le operazioni CRUD sulla tab. prodotti del DB

class Prodotto {
    // Connessione al database
    // privata --> non deve essere accessibile dall'esterno
    private $conn;

    // Nome della tab nel DB centralizzato in questa variabile => se cambia cambiamo solo qui
    private $table_name = "prodotti";

    // Proprietà che rispecchiano le colonne della tab nel DB
    // pubbliche --> devono poter essere impostate dagli endpoint prima di chiamare i metodi
    public $id;
    public $nome;
    public $descrizione;
    public $prezzo;
    public $categoria;
    public $immagine; 

    // Costruttore che riceve la connessione al DB come parametro + la salva nella variabile d'istanza
    // pattern "dependency injection": connessione iniettata dall'esterno
    public function __construct($db) {
        $this->conn = $db;
    }

    // ---------------- OPERAZIONI CRUD ------------------ 

    // READ 
    function read() {
        // Ordiniamo per ID decrescente --> più recenti in alto) 
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        //prepare() è il primo passo dei prepared statement --> manda la query al db senza ancora eseguirla
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt; // Restituisce il PDOStatement con i risultati
    }

    // CREATE
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome=:nome, descrizione=:descrizione, prezzo=:prezzo, categoria=:categoria, immagine=:immagine";
        
        $stmt = $this->conn->prepare($query);

        // SANIFICAZIONE dei dati prima di inserirli nel DB --> MOTIVI DI SICUREZZA (xss)
        // htmlspecialchars CONVERTE caratteri speciali HTML in entità 
        // strip_tags RIMUOVE eventuali tag HTML/PHP dai valori
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->prezzo = htmlspecialchars(strip_tags($this->prezzo));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->immagine = htmlspecialchars(strip_tags($this->immagine)); 

        // bindParam collega ogni parametro nella query alla variabile d'istanza
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":descrizione", $this->descrizione);
        $stmt->bindParam(":prezzo", $this->prezzo);
        $stmt->bindParam(":categoria", $this->categoria);
        $stmt->bindParam(":immagine", $this->immagine); 

        return $stmt->execute(); // Restituisce true se l'inserimento è andato a buon fine
    }

    // UPDATE del prezzo
    function update() {
        $query = "UPDATE " . $this->table_name . " SET prezzo = :prezzo WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanificazione dati
        $this->prezzo = htmlspecialchars(strip_tags($this->prezzo));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':prezzo', $this->prezzo);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // DELETE in base all'ID
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}

?>