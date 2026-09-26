import { Component, DestroyRef, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { fromEvent } from 'rxjs';
import { IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, IonButtons } from '@ionic/angular';
import { IonList, IonLabel } from "@ionic/angular";
import { ChangeDetectorRef } from '@angular/core';
import { inject } from '@angular/core';
//import { Subscription } from 'rxjs';
import { Backendservice } from '../backendservice';
import { Personaggio } from '../backendservice';
import { PushNotificationService } from '../push-notification.service';
import { IonItem } from "@ionic/angular";

@Component({
  selector: 'app-tab3',
  templateUrl: './tab3.page.html',
  styleUrls: ['./tab3.page.scss'],
  imports: [IonItem, IonLabel, IonList, IonMenuButton, IonButtons, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule]
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
