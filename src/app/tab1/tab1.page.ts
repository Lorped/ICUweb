import { Component, ChangeDetectorRef, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonAccordion, IonAccordionGroup, IonContent, IonHeader, IonTitle, IonToolbar } from '@ionic/angular';
import { IonButtons, IonMenuButton } from "@ionic/angular";
import { IonGrid, IonCol, IonRow } from "@ionic/angular";
import { TimesPipe } from '../pipe/times-pipe';
import { IonLabel, IonItem } from "@ionic/angular";
import { Personaggio } from '../backendservice';

@Component({
  selector: 'app-tab1',
  templateUrl: './tab1.page.html',
  styleUrls: ['./tab1.page.scss'],
  imports: [IonLabel, IonItem, IonRow, IonCol, IonGrid, IonButtons, IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule, TimesPipe, IonAccordion, IonAccordionGroup]
})
export class Tab1Page {

  public personaggio = inject(Personaggio);
  private cdr = inject(ChangeDetectorRef);

  ionViewWillEnter() {
    this.cdr.detectChanges();
  }

}
