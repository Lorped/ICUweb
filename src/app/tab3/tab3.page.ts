import {
  ChangeDetectorRef,
  Component,
  DestroyRef,
  inject,
  OnInit,
} from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { fromEvent } from 'rxjs';
import {
  IonButtons,
  IonContent,
  IonHeader,
  IonItem,
  IonLabel,
  IonList,
  IonMenuButton,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';
import { Backendservice, Personaggio } from '../backendservice';
import { PushNotificationService } from '../push-notification.service';

@Component({
  selector: 'app-tab3',
  templateUrl: './tab3.page.html',
  styleUrls: ['./tab3.page.scss'],
  imports: [IonItem, IonLabel, IonList, IonMenuButton, IonButtons, IonContent, IonHeader, IonTitle, IonToolbar]
})
export class Tab3Page implements OnInit {

  private cdr = inject(ChangeDetectorRef);
  private destroyRef = inject(DestroyRef);
  private backendservice = inject(Backendservice);
  private pushNotificationService = inject(PushNotificationService);
  public personaggio = inject(Personaggio);
  messaggi: any[] = [];

  constructor() { }

  ngOnInit() {
    console.log('Tab3Page initialized');
    this.backendservice.messaggiRefresh$
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(() => this.caricaMessaggi());
    this.pushNotificationService.message$
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(() => this.caricaMessaggi());
    fromEvent(document, 'visibilitychange')
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(() => {
        if (document.visibilityState === 'visible') {
          this.caricaMessaggi();
        }
      });
  }

  ionViewWillEnter() {
    this.caricaMessaggi();
  }

  private caricaMessaggi() {
    this.backendservice.getmessaggi(this.personaggio.user_id).subscribe((data: any) => {
      this.messaggi = data.messaggi;
      this.cdr.detectChanges();
    });
  }

}
