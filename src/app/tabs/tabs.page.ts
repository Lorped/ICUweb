import { Component, OnInit, inject, signal, ChangeDetectorRef } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { Personaggio, Backendservice } from '../backendservice';
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
  IonInput,
  IonButtons,
  IonHeader,
  IonModal,
  IonToolbar,
  IonTitle,
  IonLabel,
  IonText,
  IonToast,
 } from "@ionic/angular";

import { addIcons } from 'ionicons';
import { contractOutline, keypadOutline, logOutOutline, personOutline, logoAppflow } from 'ionicons/icons';

addIcons({ contractOutline, keypadOutline, logOutOutline, personOutline, logoAppflow });


@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.page.html',
  styleUrls: ['./tabs.page.scss'],
  imports: [IonText, 
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
    IonInput,
    IonButtons,
    IonHeader,
    IonModal,
    IonToolbar,
    IonTitle,
    IonList,
    IonLabel,
    IonToast,
  ],
  standalone: true,
})
export class TabsPage implements OnInit {

  private backendservice = inject(Backendservice);
  private personaggio = inject(Personaggio);
  private router = inject(Router);
  paletteToggle = false;
  isArbitroModalOpen = signal(false);
  messaggioArbitro = '';
  isSendingArbitro = false;
  messaggioArbitroError = '';
  isArbitroErrorOpen = false;
  private changeDetectorRef = inject(ChangeDetectorRef);
  

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

  openArbitro() {
    this.isArbitroModalOpen.set(true);
  }

  closeArbitro() {
    this.isArbitroModalOpen.set(false);
  }

  setArbitroErrorOpen(isOpen: boolean) {
    this.isArbitroErrorOpen = isOpen;
  }

  mandaArbitro() {
    if (this.isSendingArbitro) {
      return;
    }

    const messaggio = this.messaggioArbitro.trim();
    if (!messaggio) {
      return;
    }

    this.isSendingArbitro = true;
    this.changeDetectorRef.markForCheck();

    this.backendservice
      .msgtomaster(
        this.personaggio.user_id,
        `Richiesta di intervento da parte di un Arbitro in Nero. ${messaggio}`
      )
      .subscribe({
        next: () => {
          this.isSendingArbitro = false;
          this.isArbitroModalOpen.set(false);
          this.messaggioArbitro = '';
          this.backendservice.notificaMessaggiAggiornati();
          this.changeDetectorRef.markForCheck();
        },
        error: (error) => {
          this.isSendingArbitro = false;
          this.messaggioArbitroError =
            'Invio non riuscito. Riprova mantenendo il messaggio.';
          this.isArbitroErrorOpen = true;
          this.changeDetectorRef.markForCheck();
          console.error('Errore durante invio messaggio', error);
        },
      });
  }

}
