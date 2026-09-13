import { Component, OnInit, ChangeDetectionStrategy, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { Personaggio } from '../backendservice';
import { IonContent,
  IonTabBar,
  IonTabButton,
  IonTabs,
  IonToggle,
  IonMenu,
  IonMenuToggle,
  IonList,
  IonIcon,
  IonItem,
  IonButton,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonLabel,
 } from "@ionic/angular";

import { addIcons } from 'ionicons';
import { contractOutline, keypadOutline, logOutOutline, personOutline } from 'ionicons/icons';
addIcons({ contractOutline, keypadOutline, logOutOutline, personOutline });


@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.page.html',
  styleUrls: ['./tabs.page.scss'],
  imports: [
    FormsModule,
    IonContent,
    IonTabBar,
    IonTabButton,
    IonTabs,
    IonToggle,
    IonMenu,
    IonMenuToggle,
    IonIcon,
    IonItem,
    IonButton,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonList,
    IonLabel
  ],
  standalone: true,
})
export class TabsPage implements OnInit {

  private personaggio = inject(Personaggio);
  private router = inject(Router);
  paletteToggle = false;

  constructor() {
      addIcons({logOutOutline,personOutline,contractOutline,keypadOutline});}

  ngOnInit() {
    let savedDarkMode = window.localStorage.getItem('ICUdarkmode');
    if (savedDarkMode === null) {
      savedDarkMode = 'false';
      window.localStorage.setItem('ICUdarkmode', savedDarkMode);
    }

    this.paletteToggle = savedDarkMode === 'true';
    this.toggleDarkPalette(this.paletteToggle, false);


  }

  // Check/uncheck the toggle and update the palette based on isDark
  initializeDarkPalette(isDark: boolean) {
    this.paletteToggle = isDark;
    this.toggleDarkPalette(isDark);


    // console.log ('Dark mode is ' + (isDark ? 'enabled' : 'disabled'));

    window.localStorage.setItem(
      'ICUdarkmode',
      isDark ? 'true' : 'false'
    );
  }

  // Listen for the toggle check/uncheck to toggle the dark palette
  toggleChange(event: CustomEvent) {
    const shouldAdd = event.detail.checked;
    this.paletteToggle = shouldAdd;

    // console.log('Dark mode is ' + (shouldAdd ? 'enabled' : 'disabled'));

    this.toggleDarkPalette(shouldAdd);
  }

  // Add or remove the "ion-palette-dark" class on the html element
  toggleDarkPalette(shouldAdd: boolean, savePreference = true) {
    document.documentElement.classList.toggle('ion-palette-dark', shouldAdd);
    document.documentElement.classList.remove('ion-palette-light');
    if (!savePreference) {
      return;
    }

        window.localStorage.setItem(
      'ICUdarkmode',
      shouldAdd ? 'true' : 'false'
    );
  }
  

  
  logout() {
    this.router.navigate(['/login']);
  }



}
