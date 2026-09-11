// Service worker per la gestione delle notifiche push di Firebase Cloud Messaging in background.
importScripts('https://www.gstatic.com/firebasejs/12.1.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.1.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: 'AIzaSyC20kunAcadjoK2e24_9JGxVp6ysxPj_fs',
  authDomain: 'icuweb-f09cf.firebaseapp.com',
  projectId: 'icuweb-f09cf',
  storageBucket: 'icuweb-f09cf.firebasestorage.app',
  messagingSenderId: '458907376626',
  appId: '1:458907376626:web:57dc82973f942fd063696b',
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  const notificationTitle = payload.notification?.title ?? 'ICU';
  const notificationOptions = {
    body: payload.notification?.body ?? '',
    icon: '/icons/icon-192x192.png',
    data: payload.data,
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if ('focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow('/');
      }
    })
  );
});
