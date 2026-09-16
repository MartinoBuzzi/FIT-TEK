import { osservaAutenticazione, esci } from "./auth.js?v=3";
import { contaArticoliCarrello } from "./cart.js?v=3";
import { isUtenteAdmin } from "./admin.js?v=3";

let ultimaEsecuzione = 0;

function nascondiTutte(aree) {
  aree.forEach((el) => { if (el) el.style.display = "none"; });
}

document.addEventListener("click", async (event) => {
  const link = event.target.closest(".logoutLink");
  if (!link) return;
  event.preventDefault();
  const confermato = confirm("Sei sicuro di voler uscire?");
  if (!confermato) return;
  await esci();
  window.location.href = "index.html";
});

osservaAutenticazione(async (user) => {
  const idEsecuzione = ++ultimaEsecuzione;

  const guestArea = document.getElementById("navGuestArea");
  const userArea = document.getElementById("navUserArea");
  const adminArea = document.getElementById("navAdminArea");
  const usernameLabel = document.getElementById("navUsername");
  const cartBadge = document.getElementById("cart-badge");

  if (!guestArea || !userArea) return;

  if (!user) {
    nascondiTutte([guestArea, userArea, adminArea]);
    guestArea.style.display = "";
    return;
  }

  let admin = false;
  try {
    admin = await isUtenteAdmin(user.uid);
  } catch (errore) {
    admin = false;
  }

  if (idEsecuzione !== ultimaEsecuzione) return;

  nascondiTutte([guestArea, userArea, adminArea]);

  if (admin) {
    if (adminArea) adminArea.style.display = "";
    return;
  }

  userArea.style.display = "";

  try {
    await user.reload();
  } catch (errore) {
    /* se il reload fallisce, si usano comunque i dati già disponibili */
  }

  if (idEsecuzione !== ultimaEsecuzione) return;

  if (usernameLabel) {
    usernameLabel.textContent = user.displayName || user.email;
  }

  if (cartBadge) {
    let count = 0;
    try {
      count = await contaArticoliCarrello(user.uid);
    } catch (errore) {
      count = 0;
    }
    if (idEsecuzione !== ultimaEsecuzione) return;
    cartBadge.textContent = count;
    cartBadge.style.display = count > 0 ? "inline-block" : "none";
  }
});
