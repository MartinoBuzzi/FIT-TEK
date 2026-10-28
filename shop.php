<?php
session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>FIT-TEK - Cerca Prodotti</title>
    <?php include 'partials/header_login.php'; ?>
</head>
<body class="d-flex flex-column h-100 bg-light">

    <?php include 'partials/navbar.php'; ?>

    <main class="container mt-5 flex-shrink-0">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="display-5 fw-bold">Catalogo Prodotti</h2>
                <p class="text-muted">Trova l'attrezzatura e gli integratori per il tuo allenamento</p>
            </div>

            <!-- barra di ricerca -->
            <div class="col-md-6 offset-md-3 mb-5">
                <div class="input-group">
                    <input type="text" id="search-input" class="form-control form-control-lg shadow-sm"
                           placeholder="Cerca un prodotto o una categoria...">
                    <button class="btn btn-success px-4 shadow-sm" id="search-btn">Cerca</button>
                </div>
            </div>

            <div class="col-12 mb-5">
                <h4 class="mb-4" id="search-title">Tutti i Prodotti</h4>
                <div class="row" id="products-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-2 text-muted">Caricamento prodotti...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

    <!-- comunica a JavaScript se l'utente è loggato -->
    <!-- script_shop.js la legge per decidere se mostrare il pulsante carrello o il link "Accedi" -->
    <script>
        const USER_LOGGED_IN = <?php echo (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) ? 'true' : 'false'; ?>;
    </script>

    <!-- script_carrello.js va caricato PRIMA di script_shop.js -->
    <!-- perché script_shop.js chiama addToCart() che è definita in script_carrello.js -->
    <script src="js/script_carrello.js"></script>
    <script src="js/script_shop.js"></script>

</body>
</html>