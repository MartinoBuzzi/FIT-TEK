import { db } from "./firebase-init.js?v=3";
import {
  collection,
  doc,
  getDoc,
  getDocs,
  addDoc,
  updateDoc,
  deleteDoc
} from "https://www.gstatic.com/firebasejs/12.18.0/firebase-firestore.js";

const prodottiRef = collection(db, "prodotti");

export async function leggiProdotti() {
  const snap = await getDocs(prodottiRef);
  return snap.docs.map((d) => ({ id: d.id, ...d.data() }));
}

export async function leggiProdotto(id) {
  const snap = await getDoc(doc(db, "prodotti", id));
  return snap.exists() ? { id: snap.id, ...snap.data() } : null;
}

export function creaProdotto(prodotto) {
  return addDoc(prodottiRef, {
    nome: prodotto.nome,
    descrizione: prodotto.descrizione,
    prezzo: Number(prodotto.prezzo),
    categoria: prodotto.categoria,
    immagine: prodotto.immagine || "default.jpg"
  });
}

export function aggiornaPrezzoProdotto(id, prezzo) {
  return updateDoc(doc(db, "prodotti", id), { prezzo: Number(prezzo) });
}

export function eliminaProdotto(id) {
  return deleteDoc(doc(db, "prodotti", id));
}
