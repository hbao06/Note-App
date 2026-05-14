console.log("APP JS LOADED");

import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// import "../../public/js/offline-notes";

// if ("serviceWorker" in navigator) {
//     window.addEventListener("load", () => {
//         navigator.serviceWorker
//             .register("/sw.js")
//             .then(() => {
//                 console.log("Service Worker registered");
//             })
//             .catch((error) => {
//                 console.error("Service Worker registration failed:", error);
//             });
//     });
// }
