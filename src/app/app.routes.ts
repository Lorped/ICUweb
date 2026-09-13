import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./login/login.page').then((m) => m.LoginPage),
  },
  {
    path: 'tabs',
    loadChildren: () => import('./tabs/tabs.route').then((m) => m.TABS_ROUTES)
  },
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
];
