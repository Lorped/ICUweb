import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonMenuButton, IonContent, IonHeader, IonTitle, IonToolbar, IonButtons } from '@ionic/angular';

@Component({
  selector: 'app-tab3',
  templateUrl: './tab3.page.html',
  styleUrls: ['./tab3.page.scss'],
  imports: [IonMenuButton, IonButtons, IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule]
})
export class Tab3Page implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
