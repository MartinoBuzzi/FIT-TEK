<?php

// permette al frontend di fare richieste a questa API
header("Access-Control-Allow-Origin: *");

// Specifica che la risposta sarà in formato JSON
header("Content-Type: application/json; charset=UTF-8");

//specifica i metodi accettati
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

header("Access-Control-Allow-Headers: Content-Type");

?>