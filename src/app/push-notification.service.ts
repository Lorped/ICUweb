import { Injectable, inject } from '@angular/core';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, isSupported, onMessage } from 'firebase/messaging';
import { firebaseConfig } from '../environments/environment';
import { Backendservice } from './backendservice';

@Injectable({
  providedIn: 'root'
})
export class PushNotificationService {
  private backendService = inject(Backendservice);

  async setup(user_id: number): Promise<void> {
    try {
      if (!(await isSupported())) {
        console.warn('Le notifiche push non sono supportate su questo browser/dispositivo');
        return;
      }

      const permission = await Notification.requestPermission();
      if (permission !== 'granted') {
        console.warn('Permesso per le notifiche push negato dall\'utente');
        return;
      }

      const registration = await navigator.serviceWorker.register('firebase-messaging-sw.js');

      const app = initializeApp(firebaseConfig);
      const messaging = getMessaging(app);

      const token = await getToken(messaging, {
        vapidKey: firebaseConfig.vapidKey,
        serviceWorkerRegistration: registration,
      });

      this.saveToken(user_id, token);

      // Notifiche ricevute mentre l'app è in primo piano
      onMessage(messaging, (payload) => {
        console.log('Notifica push ricevuta in foreground', payload);
      });
    } catch (error) {
      console.error('Impossibile configurare le notifiche push', error);
    }
  }

  private saveToken(user_id: number, token: string) {
    // Endpoint non ancora disponibile: il backend PHP verrà sviluppato in seguito
    this.backendService.savePushToken(user_id,token).subscribe({
      next: () => console.log('Token push salvato'),
      error: (error) => console.error('Errore nel salvataggio del token push', error),
    });
  }
}
