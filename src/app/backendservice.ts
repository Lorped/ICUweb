import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

export interface LoginResponse {
  user_id: number;
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

}
