<?php

session_start();

// Controllo di sicurezza: se l'utente non è loggato come admin, viene rimandato al login
// --> solo gli admin possono accedere a questa pagina
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <title>Pannello Admin - FIT-TEK</title>
    <?php include 'partials/header_login.php'; ?>
</head>

<body class="d-flex flex-column h-100 bg-light">

    <?php include 'partials/navbar.php'; ?>

    <main class="flex-shrink-0">   
        <!-- Contenitore dove script_admin.js inserirà dinamicamente la tab dei prodotti/form di creazione -->
        <div id="admin-container" class="container mt-4">
            <!-- Spinner di caricamento mostrato mentre JavaScript carica i prodotti via AJAX -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <!-- visually-hidden = classe di Bootstrap che rende il testo leggibile dagli screen reader => ACCESSIBILITà -->
                    <span class="visually-hidden">Caricamento inventario...</span>
                </div>
                <p class="mt-2 text-muted">Caricamento in corso dell'inventario dello shop...</p>
            </div>
        </div>
    </main>

    <!-- includiamo script_admin.js che gestisce le operazioni CRUD -->
    <?php include 'partials/footer.php'; ?>

    <script src="js/script_admin.js"></script>
</body>
</html>