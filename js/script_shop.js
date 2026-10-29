// gestisce il catalogo prodotti in shop.php tramite AJAX

document.addEventListener("DOMContentLoaded", function() {
    loadProducts('');

    document.getElementById("search-btn")?.addEventListener("click", function() {
        const term = document.getElementById("search-input").value.trim();
        loadProducts(term);
    });

    document.getElementById("search-input")?.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            const term = this.value.trim();
            loadProducts(term);
        }
    });
});

function loadProducts(term) {
    const container = document.getElementById("products-container");
    const title     = document.getElementById("search-title");

    if (!container) return;

    container.innerHTML = `
        <div class="text-center py-5 w-100">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted">Caricamento prodotti...</p>
        </div>`;

    fetch("api/prodotti/read.php")
        .then(res => res.json())
        .then(data => {

            if (term !== '') {
                const termLower = term.toLowerCase();
                data = data.filter(
                    prod => prod.nome.toLowerCase().includes(termLower) ||
                            prod.descrizione.toLowerCase().includes(termLower) ||
                            prod.categoria.toLowerCase().includes(termLower)
                );
                if (title) title.innerHTML = `Risultati per: <span class="text-primary">"${term}"</span>`;
            } else {
                if (title) title.textContent = "Tutti i Prodotti";
            }

            if (data.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="alert alert-warning py-5 border-0 shadow-sm">
                            <h5>Nessun prodotto trovato</h5>
                            <p class="text-muted">Prova a cambiare i termini di ricerca.</p>
                            <button class="btn btn-outline-dark btn-sm" onclick="loadProducts('')">
                                Mostra tutti i prodotti
                            </button>
                        </div>
                    </div>`;
                return;
            }

            let html = '';
            data.forEach(prod => {

                // il pulsante "Aggiungi al carrello" viene mostrato solo se USER_LOGGED_IN è true
                const cartButton = (typeof USER_LOGGED_IN !== 'undefined' && USER_LOGGED_IN)
                    ? `<button class="btn btn-success btn-sm mt-2 w-100"
                               onclick="aggiungiAggiungiAlCarrello(${prod.id})">
                           Aggiungi al carrello
                       </button>`
                    : `<a href="login.php" class="btn btn-outline-secondary btn-sm mt-2 w-100">
                           Accedi per acquistare
                       </a>`;

                html += `
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 overflow-hidden">
                            <div class="row g-0 h-100">

                                <div class="col-sm-4 bg-white d-flex align-items-center justify-content-center p-2"
                                     style="min-height: 180px; cursor:pointer;"
                                     onclick="location.href='prodotto.php?id=${prod.id}'">
                                    <img src="img/${prod.immagine || 'default.jpg'}"
                                         class="img-fluid rounded"
                                         style="max-height: 160px; object-fit: contain;"
                                         alt="${prod.nome}"
                                         onerror="this.src='img/default.jpg'">
                                </div>

                                <div class="col-sm-8 d-flex flex-column justify-content-between">
                                    <div class="card-body pb-2">
                                        <span class="badge bg-secondary mb-2">${prod.categoria}</span>
                                        <h5 class="card-title fw-bold text-dark mb-1"
                                            style="cursor:pointer;"
                                            onclick="location.href='prodotto.php?id=${prod.id}'">${prod.nome}</h5>
                                        <p class="card-text text-muted small">${prod.descrizione}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 ps-3 pe-3">
                                        <h3 class="text-success fw-bold m-0">
                                            € ${parseFloat(prod.prezzo).toFixed(2)}
                                        </h3>
                                        ${cartButton}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>`;
            });

            container.innerHTML = html;
        })
        .catch(error => {
            container.innerHTML = `
                <p class="text-danger text-center">Errore nel caricamento dei prodotti.</p>`;
        });
}

// Gestore sicuro per l'aggiunta al carrello
function aggiungiAggiungiAlCarrello(idProdotto) {
    if (typeof addToCart === 'function') {
        addToCart(idProdotto);
    } else {
        fetch('api/carrello/carrello.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_prodotto: idProdotto })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || "Prodotto aggiunto!");
            if (typeof updateBadge === 'function' && data.count !== undefined) {
                updateBadge(data.count);
            }
        })
        .catch(() => alert("Errore durante l'aggiunta al carrello."));
    }
}