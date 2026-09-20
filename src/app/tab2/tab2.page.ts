import { AfterViewInit, ChangeDetectorRef, Component, ElementRef, OnDestroy, ViewChild, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent, IonHeader, IonTitle, IonToolbar, IonButtons, IonMenuButton, IonButton, IonRow, IonCol } from '@ionic/angular';
import { LoadingController, Platform } from '@ionic/angular';
import jsQR from 'jsqr';
//import { Router } from '@angular/router';
import { Backendservice, Personaggio, Oggetto } from '../backendservice';
import { IonModal, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent, IonList, IonItem, IonLabel, IonText } from '@ionic/angular';

@Component({
  selector: 'app-tab2',
  templateUrl: './tab2.page.html',
  styleUrls: ['./tab2.page.scss'],
  imports: [IonButton, IonContent, IonHeader, IonTitle, IonToolbar, IonButtons, IonMenuButton, IonRow, IonCol, CommonModule, FormsModule, IonModal, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent, IonList, IonItem, IonLabel, IonText]
})
export class Tab2Page implements AfterViewInit, OnDestroy {
  @ViewChild('video') video?: ElementRef<HTMLVideoElement>;
  @ViewChild('canvas') canvas?: ElementRef<HTMLCanvasElement>;

  canvasElement?: HTMLCanvasElement;
  videoElement?: HTMLVideoElement;
  canvasContext?: CanvasRenderingContext2D;

  animationFrameId?: number;
  lastscantime = 0;

  scanInterval = 100; // milliseconds between scan attempts

  scanActive = false;
  scanResult: string | null = null;

  private loading?: HTMLIonLoadingElement;

  private loadingCtrl = inject(LoadingController);
  //private router = inject(Router);
  private platform = inject(Platform);
  private cdr = inject(ChangeDetectorRef);
  private backendservice = inject(Backendservice);
  private personaggio = inject(Personaggio);

  oggetto: Oggetto = new Oggetto();
  giarisposto = false;
  rispostaselezionata = '';
  isModalOpen = false;
  oldscan: Array<Oggetto> = [];

  constructor() {
    const isStandaloneMode = (): boolean => 
      'standalone' in window.navigator &&  Boolean((window.navigator as Navigator & { standalone?: boolean }).standalone);

    if (this.platform.is('ios') && isStandaloneMode()) {
      console.log ("I'm an iOS PWA!!")
    }
    this.backendservice.getscan(this.personaggio.user_id).subscribe((data) => {
      this.oldscan = data;
      // senza zone.js (polyfills vuoto) serve forzare il change detection
      this.cdr.detectChanges();
    });
  }

  ngAfterViewInit() {
    this.canvasElement = this.canvas?.nativeElement;
    this.videoElement = this.video?.nativeElement;
    this.canvasContext = this.canvasElement?.getContext('2d', { willReadFrequently: true }) ?? undefined;
  }

  ionViewWillLeave() {
    this.stopScan();
  }

  ngOnDestroy() {
    this.scanResult = null;
    this.stopScan();
  }

  async startScan() {
    if (this.scanActive) {
      return;
    }
    if (!navigator.mediaDevices?.getUserMedia ) {
      console.error('Camera is not supported in this browser.');
      return;
    }

    if (!this.videoElement || !this.canvasElement || !this.canvasContext) {
      console.error('Video or canvas elements are not properly initialized.');
      return;
    }

    try {
      this.loading = await this.loadingCtrl.create();
      await this.loading.present();

      const stream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: { ideal: 'environment' },

          // no risoluzioni eccessive per evitare problemi di performance
          width: { ideal: 1280 },
          height: { ideal: 720 },
        },
        audio: false
      });

      this.videoElement.srcObject = stream;
      this.videoElement.playsInline = true;
      this.videoElement.muted = true;
      await this.videoElement.play();

      this.scanActive = true;
      this.lastscantime = 0;
      this.cdr.detectChanges();
      this.scheduleNextScan();

    } catch (error) {
      console.error('Error starting scan:', error);
      await this.stopScan();
    }
  }


  async stopScan() {
    this.scanActive = false;

    if (this.animationFrameId !== undefined) {
      cancelAnimationFrame(this.animationFrameId);
      this.animationFrameId = undefined;
    }

    const stream = this.videoElement?.srcObject ;

    if (stream instanceof MediaStream) {
      stream.getTracks().forEach((track) => track.stop());
    }
    if (this.videoElement) {
      this.videoElement.pause();
      this.videoElement.srcObject = null;
    }
    if (this.loading) {
      await this.loading.dismiss().catch(() => undefined);
      this.loading = undefined;
    }
    this.cdr.detectChanges();
  }


  scheduleNextScan() {
    if (!this.scanActive){
      return;
    }
    this.animationFrameId = requestAnimationFrame((timestamp) => this.scan(timestamp));    
  }


  private async scan(timestamp: number) {
    if (!this.scanActive || !this.videoElement || !this.canvasElement || !this.canvasContext) {
      return;
    }
    if (timestamp - this.lastscantime  < this.scanInterval || this.videoElement.readyState < this.videoElement.HAVE_CURRENT_DATA) {
      this.scheduleNextScan();
      return;
    }

    this.lastscantime = timestamp;

    if (this.loading) {
      await this.loading.dismiss().catch(() => undefined);
      this.loading = undefined;
    } 
    if (!this.scanActive) {
      return;
    }
      
    const targetWidth = 640;
    const scale = targetWidth / this.videoElement.videoWidth;
    const targetHeight = Math.round (this.videoElement.videoHeight * scale);

    if (this.canvasElement.width !== targetWidth || this.canvasElement.height !== targetHeight) {
      this.canvasElement.width = targetWidth;
      this.canvasElement.height = targetHeight;
    }
    
    this.canvasContext.drawImage(this.videoElement, 0, 0, targetWidth, targetHeight);

    const imagedata = this.canvasContext.getImageData(0, 0, targetWidth, targetHeight);
    const code = jsQR(imagedata.data, imagedata.width, imagedata.height, { inversionAttempts: 'dontInvert' });
    if (!code) {
      this.scheduleNextScan();
      return;
    }

    const value = code.data.trim();

    if (!this.isValidIdentifier(value)) {
      alert(`Invalid QR code identifier: ${value}`);
      this.scheduleNextScan();
      return;
    }

    this.scanResult = value;
    await this.stopScan();
    console.log(`Scanned QR code: ${value}`);
     //alert(`Scanned QR code: ${value}`);

    this.backendservice.barcode(this.personaggio.user_id, this.scanResult).subscribe((data) => {
      //alert(`data: ${JSON.stringify(data)}`);
      
      // console.log(data);

      this.oggetto.nomeoggetto = data.nomeoggetto;
      this.oggetto.descrizione = data.descrizione;
      this.oggetto.esito = data.esito;
      this.oggetto.domanda = data.domanda;
      this.oggetto.R1 = data.R1;
      this.oggetto.R2 = data.R2;
      this.oggetto.esitoSI = data.esitoSI;
      this.oggetto.esitoNO = data.esitoNO;  

      this.giarisposto = false;
      this.rispostaselezionata = '';

      this.isModalOpen = true;
      this.cdr.detectChanges();

      //console.log(this.oggetto);
    },
    error => {
      alert(`Error fetching barcode data: ${JSON.stringify(error)}`);
    }
  );    
  }


  risposta(risposta: string) {
    //console.log('Risposta selezionata:', risposta);
    this.giarisposto = true;
    this.rispostaselezionata = risposta;
  }


  cancel() {
    this.isModalOpen = false;
    this.backendservice.getscan(this.personaggio.user_id).subscribe((data) => {
      this.oldscan = data;
      this.cdr.detectChanges();
    });
  }



  isValidIdentifier(value: string): boolean {
    // Accept identifiers that contain any non-digit prefix but end with 12 digits
    const pattern = /\d{12}$/;
    return pattern.test(value);
  }

  ionViewWillEnter() {
    this.backendservice.getscan(this.personaggio.user_id).subscribe((data) => {
      this.oldscan = data;
      this.cdr.detectChanges();
    });
  }


}
