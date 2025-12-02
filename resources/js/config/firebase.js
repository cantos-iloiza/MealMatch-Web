// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";
import { getFirestore } from "firebase/firestore";
import { getStorage } from "firebase/storage";

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyD0jeAW3z1842r3-44MYmpkPUrZDnvhuvI",
  authDomain: "mealmatch-web.firebaseapp.com",
  projectId: "mealmatch-web",
  storageBucket: "mealmatch-web.firebasestorage.app",
  messagingSenderId: "360288389804",
  appId: "1:360288389804:web:03355df9920be35dac2d09"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase services
export const auth = getAuth(app);
export const db = getFirestore(app);
export const storage = getStorage(app);

export default app;