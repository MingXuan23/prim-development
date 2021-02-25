/* Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker. */
importScripts("https://www.gstatic.com/firebasejs/8.2.8/firebase-app.js");

// <!-- If you enabled Analytics in your project, add the Firebase SDK for Analytic -->
importScripts("https://www.gstatic.com/firebasejs/8.2.8/firebase-analytics.js");

// <!-- Add Firebase products that you want to use -->
importScripts("https://www.gstatic.com/firebasejs/8.2.8/firebase-auth.js");
importScripts("https://www.gstatic.com/firebasejs/8.2.8/firebase-firestore.js");
importScripts("https://www.gstatic.com/firebasejs/8.2.8/firebase-messaging.js");

/* Initialize the Firebase app in the service worker by passing in the messagingSenderId. New configuration for app@pulseservice.com */
firebase.initializeApp({
  apiKey: "AIzaSyDNqEXol-c8yRpS7Vrsha5H1WGLBaqfWbI",
  authDomain: "primmy.firebaseapp.com",
  projectId: "primmy",
  storageBucket: "primmy.appspot.com",
  messagingSenderId: "444112925702",
  appId: "1:444112925702:web:b18cfccc89f9835db27f87",
  measurementId: "G-DFHDL94FKJ"
});

/* Retrieve an instance of Firebase Messaging so that it can handle background messages. */
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
  console.log("[firebase-messaging-sw.js] Received background message ", payload);
  // Customize notification here
  const notificationTitle = "Background Message Title";
  const notificationOptions = {
    body: "Background Message body.",
    icon: "/firebase-logo.png"
  };

  return self.registration.showNotification(notificationTitle, notificationOptions);
});