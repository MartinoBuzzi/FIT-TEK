// gestione completa del carrello: offcanvas, badge navbar, operazioni CRUD

const CART_URL = "api/carrello/carrello.php";

// ── BADGE ────────────────────────────────────────────────────────────────────

// aggiorna il numero sul badge della navbar con il count restituito dall'API
function updateBadge(count) {
    const badge = document.getElementById("cart-badge");
    if (!badge) return;

    badge.textContent = count;
    // nasconde il badge se il carrello è vuoto
    badge.style.display = count > 0 ? "inline-block" : "none";
}

// chiamata leggera solo per aggiornare il badge (usata al caricamento della pagina)
function refreshBadge() {
    fetch(CART_URL)
        .then(res => res.json())
        .then(data => updateBadge(data.count))
        .catch(() => {}); // se fallisce non mostriamo nulla, il badge rimane com'è
}

// ── OFFCANVAS ────────────────────────────────────────────────────────────────

// apre l'offcanvas e carica il contenuto del carrello
function openCart() {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById("cartOffcanvas"));
    offcanvas.show();
    loadCart();
}

// carica il carrello dall'API e costruisce il DOM dell'offcanvas
function loadCart() {
    const body = document.getElementById("cart-body");
    const footer = document.getElementById("cart-footer");

    // spinner di caricamento
    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted small">Caricamento...</p>
        </div>`;
    footer.innerHTML = "";

    fetch(CART_URL)
        .then(res => res.json())
        .then(data => {
            updateBadge(data.count);

            // carrello vuoto
            if (data.items.length === 0) {
                body.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-cart-x fs-1"></i>
                        <p class="mt-3">Il tuo carrello è vuoto.</p>
                        <a href="shop.php" class="btn btn-outline-success btn-sm">Vai allo shop</a>
                    </div>`;
                footer.innerHTML = "";
                return;
            }

            // costruisce la lista degli articoli
            let html = '<ul class="list-group list-group-flush">';
            data.items.forEach(item => {
                html += `
                    <li class="list-group-item px-0 py-3">
                        <div class="d-flex gap-3 align-items-center">

                            <!-- immagine prodotto -->
                            <img src="img/${item.immagine || 'default.jpg'}"
                                 onerror="this.src='img/default.jpg'"
                                 class="rounded"
                                 style="width:60px; height:60px; object-fit:contain;">

                            <!-- dettagli -->
                            <div class="flex-grow-1">
                                <div class="fw-bold small">${item.nome}</div>
                                <div class="text-muted small">€ ${parseFloat(item.prezzo).toFixed(2)} cad.</div>

                                <!-- controllo quantità + / - -->
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-0"
                                            onclick="changeQuantity(${item.id}, ${item.quantita - 1})">−</button>
                                    <span class="small fw-bold">${item.quantita}</span>
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-0"
                                            onclick="changeQuantity(${item.id}, ${item.quantita + 1})">+</button>
                                </div>
                            </div>

                            <!-- subtotale + pulsante rimozione -->
                            <div class="text-end">
                                <div class="text-success fw-bold small">
                                    € ${parseFloat(item.subtotale).toFixed(2)}
                                </div>
                                <button class="btn btn-link btn-sm text-danger p-0 mt-1"
                                        onclick="removeFromCart(${item.id})">
                                    Rimuovi
                                </button>
                            </div>

                        </div>
                    </li>`;
            });
            html += '</ul>';
            body.innerHTML = html;

            // footer con totale e pulsante checkout
            footer.innerHTML = `
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Totale</span>
                        <span class="fs-5 fw-bold text-success">€ ${parseFloat(data.totale).toFixed(2)}</span>
                    </div>
                    <button class="btn btn-success w-100" onclick="alert('Checkout non ancora disponibile.')">
                        Procedi al checkout
                    </button>
                </div>`;
        })
        .catch(() => {
            body.innerHTML = `<p class="text-danger text-center">Errore nel caricamento del carrello.</p>`;
        });
}

// ── OPERAZIONI ───────────────────────────────────────────────────────────────

// aggiunge un prodotto al carrello — chiamata dalle card dello shop
function addToCart(id_prodotto) {
    fetch(CART_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_prodotto: id_prodotto })
    })
    .then(res => res.json())
    .then(data => {
        updateBadge(data.count);
        // feedback visivo rapido senza interrompere la navigazione
        showToast("Prodotto aggiunto al carrello!");
    })
    .catch(() => showToast("Errore durante l'aggiunta.", true));
}

// modifica la quantità di un articolo nel carrello
// se quantita arriva a 0, l'API rimuove direttamente la riga
function changeQuantity(id, quantita) {
    fetch(CART_URL, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, quantita: quantita })
    })
    .then(res => res.json())
    .then(data => {
        updateBadge(data.count);
        loadCart(); // ricarica l'offcanvas con i dati aggiornati
    });
}

// rimuove completamente un articolo dal carrello
function removeFromCart(id) {
    fetch(CART_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        updateBadge(data.count);
        loadCart();
    });
}

// ── TOAST ────────────────────────────────────────────────────────────────────

// feedback visivo leggero che appare in basso a destra e scompare da solo
function showToast(message, isError = false) {
    // rimuove un eventuale toast precedente ancora visibile
    const existing = document.getElementById("cart-toast");
    if (existing) existing.remove();

    const toast = document.createElement("div");
    toast.id = "cart-toast";
    toast.style.cssText = `
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
        background: ${isError ? '#dc3545' : '#198754'};
        color: white; padding: .75rem 1.25rem;
        border-radius: .5rem; font-size: .9rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        animation: fadeInUp .25s ease;`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // scompare dopo 2.5 secondi
    setTimeout(() => toast.remove(), 2500);
}

// ── INIT ─────────────────────────────────────────────────────────────────────

// al caricamento della pagina aggiorna subito il badge
document.addEventListener("DOMContentLoaded", refreshBadge);
