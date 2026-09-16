import { db } from "./firebase-init.js?v=3";
import {
  collection,
  doc,
  getDoc,
  getDocs,
  setDoc,
  updateDoc,
  deleteDoc
} from "https://www.gstatic.com/firebasejs/12.18.0/firebase-firestore.js";
import { leggiProdotto } from "./products.js?v=3";

function articoliRef(uid) {
  return collection(db, "carrelli", uid, "articoli");
}

export async function leggiCarrello(uid) {
  const snap = await getDocs(articoliRef(uid));
  const righe = await Promise.all(
    snap.docs.map(async (d) => {
      const prodotto = await leggiProdotto(d.id);
      if (!prodotto) return null;
      return {
        id: d.id,
        quantita: d.data().quantita,
        nome: prodotto.nome,
        prezzo: prodotto.prezzo,
        immagine: prodotto.immagine,
        categoria: prodotto.categoria,
        subtotale: prodotto.prezzo * d.data().quantita
      };
    })
  );
  return righe.filter(Boolean);
}

export async function aggiungiAlCarrello(uid, prodottoId) {
  const ref = doc(db, "carrelli", uid, "articoli", prodottoId);
  const attuale = await getDoc(ref);
  const quantita = attuale.exists() ? attuale.data().quantita + 1 : 1;

  if (attuale.exists()) {
    await updateDoc(ref, { quantita });
  } else {
    await setDoc(ref, { quantita });
  }
}

export async function aggiornaQuantita(uid, prodottoId, quantita) {
  const ref = doc(db, "carrelli", uid, "articoli", prodottoId);

  if (quantita <= 0) {
    await deleteDoc(ref);
  } else {
    await updateDoc(ref, { quantita });
  }
}

export async function rimuoviDalCarrello(uid, prodottoId) {
  await deleteDoc(doc(db, "carrelli", uid, "articoli", prodottoId));
}

export async function svuotaCarrello(uid) {
  const snap = await getDocs(articoliRef(uid));
  await Promise.all(snap.docs.map((d) => deleteDoc(d.ref)));
}

export async function contaArticoliCarrello(uid) {
  const snap = await getDocs(articoliRef(uid));
  let totale = 0;
  snap.forEach((d) => { totale += d.data().quantita; });
  return totale;
}
