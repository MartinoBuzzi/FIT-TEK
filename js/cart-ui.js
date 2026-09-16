import { auth } from "./firebase-init.js?v=3";
import { leggiCarrello, aggiornaQuantita, svuotaCarrello } from "./cart.js?v=3";
import { creaOrdine } from "./orders.js?v=3";

let bootstrapOffcanvas = null;

export function apriCarrello() {
  const el = document.getElementById("cartOffcanvas");
  if (!el) return;
  bootstrapOffcanvas = new bootstrap.Offcanvas(el);
  bootstrapOffcanvas.show();
  caricaCarrello();
}

async function caricaCarrello() {
  const body = document.getElementById("cart-body");
  const footer = document.getElementById("cart-footer");
  const user = auth.currentUser;

  if (!user) return;

  body.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-success" role="status"></div>
      <p class="mt-2 text-muted small">Caricamento...</p>
    </div>`;
  footer.innerHTML = "";

  const articoli = await leggiCarrello(user.uid);
  aggiornaBadge(articoli.reduce((tot, a) => tot + a.quantita, 0));

  if (articoli.length === 0) {
    body.innerHTML = `
      <div class="text-center py-5 text-muted">
        <p class="mt-3">Il tuo carrello è vuoto.</p>
        <a href="shop.html" class="btn btn-outline-success btn-sm">Vai allo shop</a>
      </div>`;
    return;
  }

  body.innerHTML = '<ul class="list-group list-group-flush">' + articoli.map((item) => `
    <li class="list-group-item px-0 py-3">
      <div class="d-flex gap-3 align-items-center">
        <img src="img/${item.immagine || 'default.jpg'}"
             onerror="this.src='img/default.jpg'"
             class="rounded" style="width:60px; height:60px; object-fit:contain;">
        <div class="flex-grow-1">
          <div class="fw-bold small">${item.nome}</div>
          <div class="text-muted small">€ ${item.prezzo.toFixed(2)} cad.</div>
          <div class="d-flex align-items-center gap-2 mt-1">
            <button class="btn btn-outline-secondary btn-sm px-2 py-0 qty-minus" data-id="${item.id}" data-qty="${item.quantita - 1}">−</button>
            <span class="small fw-bold">${item.quantita}</span>
            <button class="btn btn-outline-secondary btn-sm px-2 py-0 qty-plus" data-id="${item.id}" data-qty="${item.quantita + 1}">+</button>
          </div>
        </div>
        <div class="text-end">
          <div class="text-success fw-bold small">€ ${item.subtotale.toFixed(2)}</div>
          <button class="btn btn-link btn-sm text-danger p-0 mt-1 remove-item" data-id="${item.id}">Rimuovi</button>
        </div>
      </div>
    </li>`).join("") + "</ul>";

  const totale = articoli.reduce((tot, a) => tot + a.subtotale, 0);

  footer.innerHTML = `
    <div class="border-top pt-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="fw-bold">Totale</span>
        <span class="fs-5 fw-bold text-success">€ ${totale.toFixed(2)}</span>
      </div>
      <button class="btn btn-success w-100" id="checkoutBtn">Completa ordine</button>
      <p id="checkoutMsg" class="small text-center mt-2 mb-0"></p>
    </div>`;

  body.querySelectorAll(".qty-minus, .qty-plus").forEach((btn) => {
    btn.addEventListener("click", async () => {
      await aggiornaQuantita(user.uid, btn.dataset.id, Number(btn.dataset.qty));
      caricaCarrello();
    });
  });

  body.querySelectorAll(".remove-item").forEach((btn) => {
    btn.addEventListener("click", async () => {
      await aggiornaQuantita(user.uid, btn.dataset.id, 0);
      caricaCarrello();
    });
  });

  document.getElementById("checkoutBtn").addEventListener("click", async () => {
    const msg = document.getElementById("checkoutMsg");
    try {
      await creaOrdine(user.uid, user.email, articoli, totale);
      await svuotaCarrello(user.uid);
      caricaCarrello();
      msg.textContent = "Ordine confermato! (demo, nessun pagamento reale)";
      msg.className = "small text-center mt-2 mb-0 text-success";
    } catch (errore) {
      msg.textContent = "Errore durante la conferma dell'ordine.";
      msg.className = "small text-center mt-2 mb-0 text-danger";
    }
  });
}

function aggiornaBadge(count) {
  const badge = document.getElementById("cart-badge");
  if (!badge) return;
  badge.textContent = count;
  badge.style.display = count > 0 ? "inline-block" : "none";
}

window.apriCarrello = apriCarrello;
