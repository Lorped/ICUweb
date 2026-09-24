import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, IonButtons } from '@ionic/angular';
import { IonList, IonLabel } from "@ionic/angular";
import { ChangeDetectorRef } from '@angular/core';
import { inject } from '@angular/core';
//import { Subscription } from 'rxjs';
import { Backendservice } from '../backendservice';
import { Personaggio } from '../backendservice';
//import { PushNotificationService } from '../push-notification.service';
import { IonItem } from "@ionic/angular";

@Component({
  selector: 'app-tab3',
  templateUrl: './tab3.page.html',
  styleUrls: ['./tab3.page.scss'],
  imports: [IonItem, IonLabel, IonList, IonMenuButton, IonButtons, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule]
})
export class Tab3Page implements OnInit {

  private cdr = inject(ChangeDetectorRef);
  private backendservice = inject(Backendservice);
  //private pushNotificationService = inject(PushNotificationService);
  public personaggio = inject(Personaggio);
  messaggi: any[] = [];
  //private messageSubscription?: Subscription;

  constructor() { }

  ngOnInit() {
    console.log('Tab3Page initialized');
    //this.messageSubscription = this.pushNotificationService.message$.subscribe(() => {
    //  this.caricaMessaggi();
    //});
  }

  //ngOnDestroy() {
  //  this.messageSubscription?.unsubscribe();
  //}

  ionViewWillEnter() {
    this.backendservice.getmessaggi(this.personaggio.user_id).subscribe((data: any) => {
      this.messaggi = data.messaggi;
      this.cdr.detectChanges();
    });
  }

}
