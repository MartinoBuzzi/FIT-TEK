import { initializeApp } from "https://www.gstatic.com/firebasejs/12.18.0/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/12.18.0/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/12.18.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyDLw8EG8cSS6KcWzHL_irlIxB7GX9WCqmg",
  authDomain: "fit-tek.firebaseapp.com",
  projectId: "fit-tek",
  storageBucket: "fit-tek.firebasestorage.app",
  messagingSenderId: "340091070987",
  appId: "1:340091070987:web:2c86e5a6a24dff2478350e"
};

const app = initializeApp(firebaseConfig);

export const auth = getAuth(app);
export const db = getFirestore(app);
