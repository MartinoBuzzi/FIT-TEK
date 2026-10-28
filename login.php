<?php
// avvio della sessione utente
session_start();

include_once 'classes/Database.php';

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = "SELECT * FROM utenti WHERE username = :u";
    $stmt  = $db->prepare($query);
    $stmt->bindParam(':u', $user);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($pass, $row['password'])) {

        $_SESSION['username'] = $row['username'];
        // salviamo l'id utente in sessione — necessario per il carrello
        $_SESSION['user_id']  = $row['id'];

        if ($row['ruolo'] == 1) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin.php");
        } else {
            $_SESSION['user_logged_in'] = true;
            header("Location: index.php");
        }
        exit();

    } else {
        $error = "Credenziali errate!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FIT-TEK - Accedi</title>
    <?php include 'partials/header_login.php'; ?>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow auth-card">
                <h3 class="text-center mb-4">Accedi a FIT-TEK</h3>

                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control"
                               placeholder="Inserisci username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Inserisci password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mt-2">Accedi</button>
                </form>

                <div class="text-center mt-3 border-top pt-3">
                    <small class="text-muted">Non hai ancora un account?</small>
                    <a href="register.php" class="d-block fw-bold text-success mt-1">Registrati</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
