import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Subject } from 'rxjs';

export interface LoginResponse {
  user_id: number;
}

export interface BarcodeResponse {
  nomeoggetto: string;
  descrizione: string;
  esito: Esito[];
  domanda: string;
  R1: string;
  R2: string;
  esitoSI: Esito[];
  esitoNO: Esito[];
  refreshEffetti?: boolean;
}


export class Disciplina {
  IDdisciplina = 0;
  nomedisciplina = '';  //nome della disciplina DA LEFT JOIN
  livello = 0;  //livello della disciplina
}


export class Subskill {
    IDskill = 0;
    nomeskill = '';
    livello = 0;
}
export class Skill {
    IDskill = 0;
    nomeskill = '';
    livello = 0;
    subskills: Subskill[] = [];  //array delle sottocompetenze della skill
}
export class Otherskill {  //classe per le altre skill del personaggio
    IDskill = 0;
    nomeskill = '';
    livello = 0;
}

export class Esito {
    motivo = '';
    descrizione = '';
    sino = '';
}

export class Oggetto {
    IDoggetto = '';
    nomeoggetto = '';
    descrizione = '';
    esito: Esito[] = [];
    domanda = '';
    R1 = '';
    R2 = '';
    esitoSI: Esito[] = [];
    esitoNO: Esito[] = [];
    datascan = '';
    refreshEffetti = false;
}


@Injectable({
  providedIn: 'root'
})
export class Personaggio {
  user_id = 0;
  // nomeutente = '';  //per il login
  //password = '';
  nomeplayer = '';  //nome del giocatore
  nomepg = '';  //nome del personaggio
  IDclan = 0;
  nomeclan = '';  //nome del clan DA LEFT JOIN
  IDsocieta = 0;
  nomesocieta = '';  //nome della società DA LEFT JOIN
  IDdominio = 0;
  nomedominio = '';  //nome del dominio DA LEFT JOIN
  discipline: Disciplina[] = [];  //array delle discipline del personaggio
  skills: Skill[] = [];  //array delle skill del personaggio
  otherskills: Otherskill[] = [];  //array delle altre skill del personaggio
  attributi: Otherskill[] = [];  //array degli attributi del personaggio
}

export class messaggi {
	nomepg = '';
	Ora = '';
	Testo = '';
}

export interface MsgResponse {
    messaggi: messaggi[];  //array dei messaggi ricevuti
}


@Injectable({
  providedIn: 'root'
})
export class Backendservice {
    private http = inject(HttpClient);
    private readonly messaggiRefreshSubject = new Subject<void>();
    readonly messaggiRefresh$ = this.messaggiRefreshSubject.asObservable();


    notificaMessaggiAggiornati() {
      this.messaggiRefreshSubject.next();
    }

    login (nomeutente: string, password: string) {
      return this.http.get<LoginResponse>(`https://www.roma-by-night.it/ICU/login.php?nomeutente=${nomeutente}&password=${password}`);
    }

    savePushToken (user_id: number, token: string) {
      return this.http.get(`https://www.roma-by-night.it/ICU/savePushToken.php?user_id=${user_id}&token=${token}`);
    }

    getPersonaggio(user_id: number) {
      return this.http.get<Personaggio>(`https://www.roma-by-night.it/ICU/getpg.php?user_id=${user_id}`);
    }

    barcode(user_id: number, barcode: string) {
      return this.http.get<BarcodeResponse>('https://www.roma-by-night.it/ICU/barcode.php?user_id=' + user_id + '&barcode=' + barcode);
    }

    getscan(user_id: number) {
      return this.http.get<Oggetto[]>('https://www.roma-by-night.it/ICU/getscan.php?user_id=' + user_id);
    }

    getmessaggi(user_id: number) {
      return this.http.get<MsgResponse>('https://www.roma-by-night.it/ICU/getmessaggi.php?user_id=' + user_id + '&refresh=' + Date.now());
    }

    msgtomaster(user_id: number, messaggio: string) {
      return this.http.post(`https://www.roma-by-night.it/ICU/msgtomaster.php`, {
        idutente: user_id,
        messaggio: messaggio
      });
    }


}
