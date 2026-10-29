<?php

// endpoint unico per tutte le operazioni del carrello
require_once __DIR__ . '/../prodotti/cors.php';
session_start();

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Carrello.php';

// solo gli utenti loggati possono usare il carrello (non gli admin)
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["message" => "Devi essere loggato per usare il carrello."]);
    exit();
}

$id_utente = $_SESSION['user_id'];

$db       = (new Database())->getConnection();
$carrello = new Carrello($db);
$method   = $_SERVER['REQUEST_METHOD'];

// per POST, PUT e DELETE leggiamo il body JSON
$data = null;
if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    $data = json_decode(file_get_contents("php://input"));
}

switch ($method) {

    // READ — restituisce tutti i prodotti nel carrello + conteggio badge
    case 'GET':
        $stmt  = $carrello->read($id_utente);
        $items = [];
        $totale = 0.0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = [
                "id"          => $row['id'],
                "id_prodotto" => $row['id_prodotto'],
                "nome"        => $row['nome'],
                "prezzo"      => $row['prezzo'],
                "immagine"    => $row['immagine'],
                "categoria"   => $row['categoria'],
                "quantita"    => $row['quantita'],
                "subtotale"   => $row['subtotale']
            ];
            $totale += $row['subtotale'];
        }

        http_response_code(200);
        echo json_encode([
            "items"   => $items,
            "totale"  => round($totale, 2),
            "count"   => $carrello->count($id_utente)
        ]);
        break;

    // ADD — aggiunge un prodotto (o incrementa la quantità)
    case 'POST':
        if (empty($data->id_prodotto)) {
            http_response_code(400);
            echo json_encode(["message" => "ID prodotto mancante."]);
            exit();
        }

        if ($carrello->add($id_utente, $data->id_prodotto)) {
            http_response_code(201);
            echo json_encode([
                "message" => "Prodotto aggiunto al carrello.",
                "count"   => $carrello->count($id_utente)
            ]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Errore durante l'aggiunta al carrello."]);
        }
        break;

    // UPDATE — modifica la quantità di un articolo
    case 'PUT':
        if (empty($data->id) || !isset($data->quantita)) {
            http_response_code(400);
            echo json_encode(["message" => "Dati mancanti."]);
            exit();
        }

        // se la quantità è 0 o negativa rimuoviamo direttamente la riga
        if ((int)$data->quantita <= 0) {
            $carrello->remove($data->id, $id_utente);
            http_response_code(200);
            echo json_encode([
                "message" => "Prodotto rimosso dal carrello.",
                "count"   => $carrello->count($id_utente)
            ]);
            exit();
        }

        if ($carrello->updateQuantita($data->id, $id_utente, $data->quantita)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Quantità aggiornata.",
                "count"   => $carrello->count($id_utente)
            ]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Errore durante l'aggiornamento."]);
        }
        break;

    // REMOVE — elimina un articolo dal carrello
    case 'DELETE':
        if (empty($data->id)) {
            http_response_code(400);
            echo json_encode(["message" => "ID mancante."]);
            exit();
        }

        if ($carrello->remove($data->id, $id_utente)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Prodotto rimosso.",
                "count"   => $carrello->count($id_utente)
            ]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Errore durante la rimozione."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Metodo non supportato."]);
        break;
}

?>