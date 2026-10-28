<?php

session_start();

//inlusione della classe Database per la connessione al DB
include_once 'classes/Database.php';

$database = new Database();
$db = $database->getConnection();

//creazione della variabile per il messaggio di successo/errore
$message = "";

// controllo per verificare il form sia stato inviato con metodo POST
// --> stessa logica di login.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // query per inserire il nuovo utente nel DB
    $query = "INSERT INTO utenti (username, email, password) VALUES (:user, :email, :pass)";
    $stmt = $db->prepare($query);
    
    // bindParam collega ogni parametro al valore corrispondente
    $stmt->bindParam(':user', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':pass', $password);

    try {
    
        if($stmt->execute()) {
            // se la registrazione è riuscita si mostra il link per accedere
            $message = "<div class='alert alert-success'>Registrazione completata! <a href='login.php'>Accedi ora</a></div>";
        } 
    } catch (PDOException $e) {
            //altrimenti si da un mex di errore generico (motivi di sicurezza)
            // appare solo se la query fallisce lato-server
            $message = "<div class='alert alert-danger'>Errore durante la registrazione.</div>";
        }
    
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>FIT-TEK - Registrazione</title>
    <?php include 'partials/header_login.php'; ?>
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow auth-card p-4">
                    
                    <h2 class="text-center mb-4">Crea Account</h2>
                    
                    <!-- si mostra il mex definito prima-->
                    <?php echo $message; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-2">Registrati</button>
                    </form>
                    <div class="text-center mt-3">
                        <small>Hai già un account? <a class="text-success" href="login.php">Accedi qui</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</p><br>

    <?php include 'partials/footer.php'; ?>
</body>
</html>