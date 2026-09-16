import { db } from "./firebase-init.js?v=3";
import {
  collection,
  addDoc,
  serverTimestamp
} from "https://www.gstatic.com/firebasejs/12.18.0/firebase-firestore.js";

export async function creaOrdine(uid, email, articoli, totale) {
  return addDoc(collection(db, "ordini"), {
    uid,
    email,
    articoli: articoli.map((a) => ({
      prodottoId: a.id,
      nome: a.nome,
      prezzo: a.prezzo,
      quantita: a.quantita
    })),
    totale,
    creato: serverTimestamp()
  });
}
