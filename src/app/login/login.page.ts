import { Component, inject, OnInit } from '@angular/core';
import { PushNotificationService } from '../push-notification.service';
import {
  IonButton,
  IonCheckbox,
  IonCol,
  IonContent,
  IonHeader,
  IonInput,
  IonInputPasswordToggle,
  IonItem,
  IonList,
  IonLoading,
  IonRow,
  IonText,
  IonTitle,
  IonToolbar,
  LoadingController,
} from '@ionic/angular';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { FormsModule } from '@angular/forms';
import { Backendservice } from '../backendservice';
import { routes } from '../app.routes';


@Component({
  selector: 'app-login',
  templateUrl: 'login.page.html',
  styleUrls: ['login.page.scss'],
  imports: [
    FormsModule,
    ReactiveFormsModule,
    IonButton,
    IonCheckbox,
    IonCol,
    IonContent,
    IonHeader,
    IonInput,
    IonInputPasswordToggle,
    IonItem,
    IonList,
    IonLoading,
    IonRow,
    IonText,
    IonTitle,
    IonToolbar,
  ],
})
export class LoginPage implements OnInit {
  private loadingCtrl = inject(LoadingController);
  private backendService = inject(Backendservice);
  private pushNotificationService = inject(PushNotificationService);

  saveme = {
    checked: false,
  };
  isDarkMode = false;

  loginForm = new FormGroup({
    loginName: new FormControl('', { nonNullable: true, validators: Validators.required }),
    password: new FormControl('', { nonNullable: true, validators: Validators.required })
  });

  user_id = 0;

  constructor() {
    const loginName = window.localStorage.getItem('ICUuserid') ?? '';
    const password = window.localStorage.getItem('ICUpasswd') ?? '';

    this.loginForm.patchValue({ loginName, password });
    if (loginName !== '') {
      this.saveme.checked = true;
    }
  }

  ngOnInit() {
    this.applyAppPalette();
  }
  
  private applyAppPalette() {
    let savedDarkMode = window.localStorage.getItem('ICUdarkmode');
    if (savedDarkMode === null) {
      savedDarkMode = 'false';
      window.localStorage.setItem('ICUdarkmode', savedDarkMode);
    }

    this.isDarkMode = savedDarkMode === 'true';
    document.documentElement.classList.toggle('ion-palette-dark', this.isDarkMode);
    document.documentElement.classList.remove('ion-palette-light');
  }


  async doLogin() {
    if (this.saveme.checked) {
      window.localStorage.setItem('ICUuserid', this.loginForm.value.loginName ?? '');
      window.localStorage.setItem('ICUpasswd', this.loginForm.value.password ?? '');
    } else {
      window.localStorage.removeItem('ICUuserid');
      window.localStorage.removeItem('ICUpasswd');
    }
    // Proceed with login logic here, e.g., call an API or navigate to another page
    // console.log('Login attempted with', this.loginForm.value);

    this.backendService.login(this.loginForm.value.loginName ?? '', this.loginForm.value.password ?? '').subscribe({
      next: (response) => {
        // console.log('Login successful', response);
        this.user_id = response.user_id ?? 0;
        this.loadingCtrl.dismiss();

        this.pushNotificationService.setup(this.user_id );
      },
      error: (error) => {
        console.error('Login failed', error);
        this.loadingCtrl.dismiss();
      }
    });

    
  }


}
