import { Component, ChangeDetectorRef, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonAccordion,
  IonAccordionGroup,
  IonButtons,
  IonCol,
  IonContent,
  IonGrid,
  IonHeader,
  IonItem,
  IonLabel,
  IonMenuButton,
  IonRow,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';
import { TimesPipe } from '../pipe/times-pipe';
import { Personaggio } from '../backendservice';

@Component({
  selector: 'app-tab1',
  templateUrl: './tab1.page.html',
  styleUrls: ['./tab1.page.scss'],
  imports: [IonLabel, IonItem, IonRow, IonCol, IonGrid, IonButtons, IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, TimesPipe, IonAccordion, IonAccordionGroup]
})
export class Tab1Page {

  public personaggio = inject(Personaggio);
  private cdr = inject(ChangeDetectorRef);

  ionViewWillEnter() {
    this.cdr.detectChanges();
  }

}
