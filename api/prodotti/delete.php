<?php

session_start();

require 'cors.php';

// controllo per verificare utente sia un admin (solo loro possono eliminare prod.)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401); // = unauthorized + blocco dell'esecuzione
    echo json_encode(array("message" => "Operazione negata. Non hai i permessi di amministratore."));
    exit(); 
}

include_once '../../classes/Database.php';
include_once '../../classes/Prodotto.php';

$database = new Database();
$db = $database->getConnection();
$prodotto = new Prodotto($db);

// lettura del JSON inviato da javascript
$data = json_decode(file_get_contents("php://input"));

// verifica che l'ID del prodotto ci sia
if(!empty($data->id)){
    $prodotto->id = $data->id;

    if($prodotto->delete()){
        http_response_code(200); // tutto ok
        echo json_encode(array("message" => "Prodotto eliminato con successo."));
    } else {
        http_response_code(503); // = service unavailable
        echo json_encode(array("message" => "Impossibile eliminare il prodotto."));
    }
} else {
    http_response_code(400); // = Bad Request
    echo json_encode(array("message" => "ID prodotto non fornito."));
}
?>