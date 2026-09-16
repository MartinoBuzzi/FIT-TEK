import { db } from "./firebase-init.js?v=3";
import { doc, getDoc } from "https://www.gstatic.com/firebasejs/12.18.0/firebase-firestore.js";

export async function isUtenteAdmin(uid) {
  const snap = await getDoc(doc(db, "utenti", uid));
  return snap.exists() && snap.data().isAdmin === true;
}
