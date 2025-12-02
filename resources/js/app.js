import './bootstrap';

// Import Firebase
import { auth, db, storage } from './config/firebase';

// Make Firebase available globally (optional, but useful)
window.firebaseAuth = auth;
window.firebaseDb = db;
window.firebaseStorage = storage;

console.log('Firebase initialized:', auth.app.options.projectId);