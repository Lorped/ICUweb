import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonAccordion, IonAccordionGroup, IonContent, IonHeader, IonTitle, IonToolbar } from '@ionic/angular';
import { IonButtons, IonMenuButton } from "@ionic/angular";
import { Personaggio } from '../backendservice'
import { IonGrid, IonCol, IonRow } from "@ionic/angular";
import { TimesPipe } from '../pipe/times-pipe';
import { IonLabel, IonItem } from "@ionic/angular";

@Component({
  selector: 'app-tab1',
  templateUrl: './tab1.page.html',
  styleUrls: ['./tab1.page.scss'],
  imports: [IonLabel, IonItem, IonRow, IonCol, IonGrid, IonButtons, IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule, TimesPipe, IonAccordion, IonAccordionGroup]
})
export class Tab1Page implements OnInit {

  public personaggio = inject(Personaggio);

  constructor() { 
    console.log('constructor called');
    // console.log(this.personaggio);
  }

  ngOnInit() {
    console.log('ngOnInit called');
    // console.log(this.personaggio);
  }

  ionViewWillEnter() {
    console.log('ionViewWillEnter called');
    // console.log(this.personaggio);
  } 

}
