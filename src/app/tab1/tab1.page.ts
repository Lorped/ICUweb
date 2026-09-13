import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent, IonHeader, IonTitle, IonToolbar } from '@ionic/angular';
import { IonButtons, IonMenuButton } from "@ionic/angular";
import { Personaggio } from '../backendservice'

@Component({
  selector: 'app-tab1',
  templateUrl: './tab1.page.html',
  styleUrls: ['./tab1.page.scss'],
  imports: [IonButtons, IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule]
})
export class Tab1Page implements OnInit {

  private personaggio = inject(Personaggio);

  constructor() { 
    console.log('constructor called');
    console.log(this.personaggio);
  }

  ngOnInit() {
    console.log('ngOnInit called');
    console.log(this.personaggio);
  }

  ionViewWillEnter() {
    console.log('ionViewWillEnter called');
    console.log(this.personaggio);
  } 

}
