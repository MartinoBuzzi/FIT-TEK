import { auth } from "./firebase-init.js?v=3";
import {
  createUserWithEmailAndPassword,
  signInWithEmailAndPassword,
  signOut,
  onAuthStateChanged,
  updateProfile
} from "https://www.gstatic.com/firebasejs/12.18.0/firebase-auth.js";

export async function registrati(username, email, password) {
  const cred = await createUserWithEmailAndPassword(auth, email, password);
  await updateProfile(cred.user, { displayName: username });
  return cred;
}

export function accedi(email, password) {
  return signInWithEmailAndPassword(auth, email, password);
}

export function esci() {
  return signOut(auth);
}

export function osservaAutenticazione(callback) {
  onAuthStateChanged(auth, callback);
}

export function messaggioErroreAuth(errore) {
  const codici = {
    "auth/email-already-in-use": "Questo indirizzo email è già registrato.",
    "auth/invalid-email": "Indirizzo email non valido.",
    "auth/weak-password": "La password deve avere almeno 6 caratteri.",
    "auth/user-not-found": "Nessun account trovato con questa email.",
    "auth/wrong-password": "Password non corretta.",
    "auth/invalid-credential": "Email o password non corrette.",
    "auth/too-many-requests": "Troppi tentativi, riprova tra qualche minuto."
  };
  return codici[errore.code] || "Si è verificato un errore, riprova.";
}
