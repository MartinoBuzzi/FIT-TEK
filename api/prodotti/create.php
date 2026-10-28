<?php

session_start();

require 'cors.php';

// controllo per verificare utente sia un admin (solo loro possono aggiungere prod.)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401); // = unauthorized + blocco dell'esecuzione
    // --> richiesta non completata perchè mancano le credenziali di autentificazione valide
    echo json_encode(array("message" => "Operazione negata. Non hai i permessi di amministratore."));
    exit();
}

include_once '../../classes/Database.php';
include_once '../../classes/Prodotto.php';

$database = new Database();
$db = $database->getConnection();
$prodotto = new Prodotto($db);

// lettura del JSON inviato da javascript 
$data = json_decode(file_get_contents("php://input")); //--> php://input contiene il body della richiesta HTTP come arriva

// verifica che i campi obbligatori siano presenti
if(!empty($data->nome) && !empty($data->prezzo) && !empty($data->categoria) && !empty($data->descrizione)){
    
    // assegnazione dei valori alle proprietà della classe Prodotto
    $prodotto->nome = $data->nome;
    $prodotto->prezzo = $data->prezzo;
    $prodotto->categoria = $data->categoria;
    $prodotto->descrizione = $data->descrizione;
    
    // se manca nel JSON, usiamo 'default.jpg'
    // OPERATORE TERNARIO --> condizione ? valore_se_vera : valore_se_falsa 
    $prodotto->immagine = !empty($data->immagine) ? $data->immagine : "default.jpg";

    if($prodotto->create()){
        http_response_code(201); // = creata con successo
        echo json_encode(array("message" => "Prodotto inserito con successo."));
    } else {
        http_response_code(503); // = server web teporaneamente impossibilitato a elaborare la richiesta
        echo json_encode(array("message" => "Impossibile inserire il prodotto."));
    }
} else {
    http_response_code(400); // = Bad Request = dati mancanti o non validi
    echo json_encode(array("message" => "Dati incompleti."));
}

?>