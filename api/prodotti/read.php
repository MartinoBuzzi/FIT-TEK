<?php

require 'cors.php';

// ENDPOINT PUBBLICO: non si mette la protezione di sessione (visualizzaz. prodotti shop)

include_once '../../classes/Database.php';
include_once '../../classes/Prodotto.php';

//connessione tramite la classe Database
$database = new Database();
$db = $database->getConnection();

// creo un'istanza di Prodotto passandogli la connessione (dependency injection)
$prodotto = new Prodotto($db);

// invoco il metodo read() della classe che esegue la SELECT
$stmt = $prodotto->read();

// array vuoto che conterrà i prod. da restituire
$prodotti_arr = array();

// scorriamo ogni riga del risultato --> finché ci sono righe da leggere le si salva in row
// stmt->fetch(PDO::FETCH_ASSOC) restituisce un array associativo 
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row); // converte le chiavi di un array associativo in variabili locali (es. $row['nome'] diventa $nome)

    // array di ogni prod. con i campi della tab del DB
    $item = array(
        "id" => $id,
        "nome" => $nome,
        "descrizione" => $descrizione,
        "prezzo" => $prezzo,
        "categoria" => $categoria,
        "immagine" => $immagine
    );

    // metodo per aggiungere l'array $item al fondo dell'array principale
    array_push($prodotti_arr, $item);
}

http_response_code(200); // = tutto ok

// conversione dell'array PHP in JSON --> inviato come risposta
echo json_encode($prodotti_arr);

?>