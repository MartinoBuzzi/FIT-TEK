<?php
session_start();
include_once 'classes/Database.php';
include_once 'classes/Prodotto.php';

// legge l'id dalla URL: prodotto.php?id=5
// se manca o non è un numero reindirizza allo shop
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: shop.php");
    exit();
}

$db       = (new Database())->getConnection();
$prodotto = new Prodotto($db);

// aggiunge un metodo getById al volo senza modificare la classe
// usando PDO direttamente — la classe Prodotto non ha un readOne()
$stmt = $db->prepare("SELECT * FROM prodotti WHERE id = :id");
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();
$p = $stmt->fetch(PDO::FETCH_ASSOC);

// prodotto non trovato → torna allo shop
if (!$p) {
    header("Location: shop.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>FIT-TEK — <?php echo htmlspecialchars($p['nome']); ?></title>
    <?php include 'partials/header_login.php'; ?>
    <style>
        /* ── layout split ────────────────────────────────────────────────── */
        .product-hero {
            min-height: calc(100vh - 56px);
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        @media (max-width: 768px) {
            .product-hero { grid-template-columns: 1fr; min-height: auto; }
            .product-image-panel { min-height: 50vw; }
        }

        /* pannello sinistro: immagine su sfondo scuro */
        .product-image-panel {
            background: var(--color-neutral-text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image-panel img {
            max-width: 70%;
            max-height: 70vh;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,.5));
            transition: transform .4s ease;
        }

        .product-image-panel img:hover {
            transform: scale(1.04);
        }

        /* cerchio accent decorativo in background — usa il colore brand del sito */
        .product-image-panel::before {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(32,201,151,.15) 0%, transparent 70%);
            z-index: 0;
        }

        /* pannello destro: dettagli */
        .product-info-panel {
            background: var(--color-neutral-surface);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 4rem;
        }

        @media (max-width: 992px) {
            .product-info-panel { padding: 2rem 1.5rem; }
        }

        /* categoria pill — usa l'accent del brand */
        .category-pill {
            display: inline-block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--color-brand-accent);
            border: 1.5px solid var(--color-brand-accent);
            border-radius: 2rem;
            padding: .2rem .85rem;
            margin-bottom: 1.2rem;
        }

        /* nome prodotto */
        .product-name {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--color-neutral-text-primary);
            margin-bottom: 1.5rem;
        }

        /* linea separatrice accent */
        .accent-line {
            width: 48px;
            height: 4px;
            background: var(--color-brand-accent);
            border-radius: 2px;
            margin-bottom: 1.5rem;
        }

        /* descrizione */
        .product-description {
            font-size: 1rem;
            color: var(--color-neutral-text-secondary);
            line-height: 1.75;
            margin-bottom: 2rem;
        }

        /* prezzo — usa il success semantico come nel resto del sito */
        .product-price {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--color-semantic-success);
            margin-bottom: 2rem;
            letter-spacing: -.02em;
        }

        /* area azioni */
        .product-actions {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        /* pulsante aggiungi al carrello — usa brand-primary come il resto dei btn principali */
        .btn-add-cart {
            background: var(--color-semantic-success);
            color: var(--color-neutral-surface);
            border: none;
            border-radius: .5rem;
            padding: .9rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
        }

        .btn-add-cart:hover { opacity: .88; transform: translateY(-1px); }
        .btn-add-cart:active { transform: translateY(0); }

        /* pulsante torna allo shop */
        .btn-back {
            background: transparent;
            color: var(--color-neutral-text-muted);
            border: 1.5px solid var(--color-neutral-border);
            border-radius: .5rem;
            padding: .75rem 1.5rem;
            font-size: .9rem;
            text-decoration: none;
            text-align: center;
            transition: border-color .2s, color .2s;
        }

        .btn-back:hover {
            border-color: var(--color-neutral-text-secondary);
            color: var(--color-neutral-text-primary);
        }

        /* controllo quantità */
        .qty-control {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .5rem;
        }

        .qty-btn {
            width: 36px; height: 36px;
            border: 1.5px solid var(--color-neutral-border);
            border-radius: .4rem;
            background: var(--color-neutral-surface);
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: border-color .15s, color .15s;
        }

        .qty-btn:hover {
            border-color: var(--color-brand-accent);
            color: var(--color-brand-accent);
        }

        .qty-value {
            min-width: 36px;
            text-align: center;
            font-weight: 700;
            font-size: 1rem;
            color: var(--color-neutral-text-primary);
        }

        /* box "non loggato" */
        .login-nudge {
            background: var(--color-neutral-background);
            border-radius: .5rem;
            padding: 1rem;
            text-align: center;
            font-size: .9rem;
            color: var(--color-neutral-text-secondary);
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; ?>

    <div class="product-hero">

        <!-- SINISTRA: immagine su sfondo scuro -->
        <div class="product-image-panel">
            <img src="img/<?php echo htmlspecialchars($p['immagine'] ?: 'default.jpg'); ?>"
                 onerror="this.src='img/default.jpg'"
                 alt="<?php echo htmlspecialchars($p['nome']); ?>">
        </div>

        <!-- DESTRA: dettagli prodotto -->
        <div class="product-info-panel">

            <span class="category-pill"><?php echo htmlspecialchars($p['categoria']); ?></span>

            <h1 class="product-name"><?php echo htmlspecialchars($p['nome']); ?></h1>

            <div class="accent-line"></div>

            <p class="product-description"><?php echo htmlspecialchars($p['descrizione']); ?></p>

            <div class="product-price">
                € <?php echo number_format((float)$p['prezzo'], 2, ',', '.'); ?>
            </div>

            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>

                <!-- controllo quantità + pulsante aggiungi -->
                <div class="product-actions">
                    <div class="qty-control">
                        <button class="qty-btn" onclick="changeQty(-1)">−</button>
                        <span class="qty-value" id="qty">1</span>
                        <button class="qty-btn" onclick="changeQty(1)">+</button>
                        <span class="text-muted small ms-2">quantità</span>
                    </div>
                    <button class="btn-add-cart" onclick="addToCartQty(<?php echo (int)$p['id']; ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        Aggiungi al carrello
                    </button>
                    <a href="shop.php" class="btn-back">← Torna allo shop</a>
                </div>

            <?php elseif (isset($_SESSION['admin_logged_in'])): ?>

                <!-- admin: niente carrello, link utile -->
                <div class="login-nudge">
                    <p class="mb-2">Sei loggato come amministratore.</p>
                    <a href="admin.php" class="btn btn-outline-success btn-sm">Vai al pannello admin</a>
                </div>
                <a href="shop.php" class="btn-back mt-3">← Torna allo shop</a>

            <?php else: ?>

                <!-- visitatore non loggato -->
                <div class="login-nudge">
                    <p class="mb-2">Accedi per aggiungere questo prodotto al carrello.</p>
                    <a href="login.php" class="btn btn-success btn-sm">Accedi</a>
                    <a href="register.php" class="btn btn-outline-secondary btn-sm ms-2">Registrati</a>
                </div>
                <a href="shop.php" class="btn-back mt-3">← Torna allo shop</a>

            <?php endif; ?>

        </div>
    </div>

    <?php include 'partials/footer.php'; ?>

    <!-- variabile sessione per script_carrello.js -->
    <script>
        const USER_LOGGED_IN = <?php echo (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) ? 'true' : 'false'; ?>;

        let qty = 1;

        function changeQty(delta) {
            qty = Math.max(1, qty + delta);
            document.getElementById("qty").textContent = qty;
        }

        // aggiunge il prodotto N volte in base alla quantità selezionata
        // chiama addToCart() di script_carrello.js in loop
        async function addToCartQty(id_prodotto) {
            for (let i = 0; i < qty; i++) {
                await fetch("api/carrello/carrello.php", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_prodotto: id_prodotto })
                }).then(r => r.json()).then(d => updateBadge(d.count));
            }
            showToast(`${qty} × aggiunto al carrello!`);
            qty = 1;
            document.getElementById("qty").textContent = 1;
        }
    </script>

    <script src="js/script_carrello.js"></script>

</body>
</html>