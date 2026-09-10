import { Component, inject } from '@angular/core';
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
export class LoginPage {
  private loadingCtrl = inject(LoadingController);

  saveme = {
    checked: false,
  };

  loginForm = new FormGroup({
    loginName: new FormControl('', { nonNullable: true, validators: Validators.required }),
    password: new FormControl('', { nonNullable: true, validators: Validators.required })
  });

  constructor() {
    const loginName = window.localStorage.getItem('ICUuserid') ?? '';
    const password = window.localStorage.getItem('ICUpasswd') ?? '';

    this.loginForm.patchValue({ loginName, password });
    if (loginName !== '') {
      this.saveme.checked = true;
    }
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
    console.log('Login attempted with', this.loginForm.value);

    await this.delay(2000);

    console.log('Finished waiting');
    
    await this.loadingCtrl.dismiss();
  }

  delay(ms: number) {
    return new Promise( resolve => setTimeout(resolve, ms) );
  }

}
