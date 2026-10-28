<?php

session_start();

require 'cors.php';

// controllo per verificare l'utente sia un admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401); // = unauthorized
    echo json_encode(array("message" => "Operazione negata. Non hai i permessi di amministratore."));
    exit(); // interrompe l'esecuzione dello script
}

include_once '../../classes/Database.php';
include_once '../../classes/Prodotto.php';

$database = new Database();
$db = $database->getConnection();
$prodotto = new Prodotto($db);

// lettura del JSON inviato da javascript
$data = json_decode(file_get_contents("php://input"));

// verifica che id e prezzo siano presenti
// --> prezzo perchè si aggiorna solo quello
if(!empty($data->id) && !empty($data->prezzo)){
    
    // assegniamo i valori alle proprietà della classe Prodotti
    $prodotto->id = $data->id;
    $prodotto->prezzo = $data->prezzo;

    if($prodotto->update()){
        http_response_code(200); // = tutto ok
        echo json_encode(array("message" => "Prezzo aggiornato con successo."));
    } else {
        http_response_code(503); // = server temporaneamente non in grado di elaborare la richiesta
        echo json_encode(array("message" => "Impossibile aggiornare il prezzo."));
    }
} else {
    http_response_code(400); // = bad request (richiesta incompleta o con errori di sintassi)
    echo json_encode(array("message" => "Dati incompleti per l'aggiornamento."));
}

?>