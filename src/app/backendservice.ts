import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

export interface LoginResponse {
  user_id: number;
}


export class Disciplina {
  IDdisciplina: number = 0;
  nomedisciplina: string = '';  //nome della disciplina DA LEFT JOIN
  livello: number = 0;  //livello della disciplina
}


export class Subskill {
    public IDskill = 0;
    public nomeskill = '';
    public livello = 0;
}
export class Skill {
    public IDskill = 0;
    public nomeskill = '';
    public livello = 0;
    public subskills: Subskill[] = [];  //array delle sottocompetenze della skill
}
export class Otherskill {  //classe per le altre skill del personaggio
    public IDskill = 0;
    public nomeskill = '';
    public livello = 0;
}

export class Esito {
    public motivo = '';
    public descrizione = '';
    public sino = '';
}

export class Oggetto {
    public IDoggetto = '';
    public nomeoggetto = '';
    public descrizione = '';
    public esito: Array<Esito> = [];
    public domanda = '';
    public R1 = '';
    public R2 = '';
    public esitoSI: Array<Esito> = [];
    public esitoNO: Array<Esito> = [];
    public datascan = '';
}


@Injectable({
  providedIn: 'root'
})
export class Personaggio {
  user_id: number = 0;
  // nomeutente: string = '';  //per il login
  //password: string = '';
  nomeplayer: string = '';  //nome del giocatore
  nomepg: string = '';  //nome del personaggio
  IDclan: number = 0;
  nomeclan: string = '';  //nome del clan DA LEFT JOIN
  IDsocieta: number = 0;
  nomesocieta: string = '';  //nome della società DA LEFT JOIN
  IDdominio: number = 0;
  nomedominio: string = '';  //nome del dominio DA LEFT JOIN
  discipline: Disciplina[] = [];  //array delle discipline del personaggio
  skills: Skill[] = [];  //array delle skill del personaggio
  otherskills: Otherskill[] = [];  //array delle altre skill del personaggio
  attributi: Otherskill[] = [];  //array degli attributi del personaggio
}


@Injectable({
  providedIn: 'root'
})
export class Backendservice {
    private http = inject(HttpClient);
    constructor() {}

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
      return this.http.get<any>('https://www.roma-by-night.it/ICU/barcode.php?id=' + user_id + '&barcode=' + barcode);
    }

    getscan(user_id: number) {
      return this.http.get<any>('https://www.roma-by-night.it/ICU/getscan.php?user_id=' + user_id);
    }

}
