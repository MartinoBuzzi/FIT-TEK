<?php

// avviamo la sessione solo se non è già attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <!-- logo cliccabile che rimanda alla home -->
        <a class="navbar-brand fw-bold" href="index.php">FIT-TEK</a>

        <!-- pulsante hamburger per mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- link principali — spinti a sinistra da me-auto -->
            <ul class="navbar-nav me-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
            </ul>

            <!-- area destra: carrello + auth -->
            <ul class="navbar-nav align-items-center gap-2">

                <?php if(isset($_SESSION['admin_logged_in'])): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Pannello Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm"
                           href="logout.php"
                           onclick="return confirm('Sei sicuro di voler uscire?')">Logout</a>
                    </li>

                <?php elseif(isset($_SESSION['user_logged_in'])): ?>

                    <!-- icona carrello con badge — visibile solo agli utenti loggati -->
                    <li class="nav-item">
                        <button class="btn btn-link nav-link text-white position-relative px-1"
                                onclick="openCart()"
                                title="Il tuo carrello">
                            <!-- icona carrello Bootstrap Icons -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                 fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </svg>
                            <!-- badge con il numero di articoli — nascosto se 0 -->
                            <span id="cart-badge"
                                  class="position-absolute top-0 start-100 translate-middle
                                         badge rounded-pill bg-success"
                                  style="font-size:.65rem; display:none;">
                                0
                            </span>
                        </button>
                    </li>

                    <li class="nav-item">
                        <span class="text-white small">
                            Ciao, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm"
                           href="logout.php"
                           onclick="return confirm('Sei sicuro di voler uscire?')">Logout</a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="btn btn-custom-login btn-sm" href="login.php">Accedi</a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- offcanvas carrello — si apre dal lato destro -->
<!-- visibile solo agli utenti loggati -->
<?php if(isset($_SESSION['user_logged_in'])): ?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas"
     style="width: 420px; max-width: 95vw;">

    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">
            Il tuo carrello
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <!-- corpo: popolato da script_carrello.js -->
    <div class="offcanvas-body" id="cart-body">
    </div>

    <!-- footer: totale + checkout — popolato da script_carrello.js -->
    <div class="p-3" id="cart-footer">
    </div>

</div>
<?php endif; ?>
