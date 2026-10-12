import { inject, Injectable } from '@angular/core';
import { SwUpdate, VersionReadyEvent } from '@angular/service-worker';
import { filter } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class UpdateService {
  private readonly swUpdate = inject(SwUpdate);

  init() {
    if (!this.swUpdate.isEnabled) return;

    this.swUpdate.versionUpdates
      .pipe(filter((e): e is VersionReadyEvent => e.type === 'VERSION_READY'))
      .subscribe(() => this.reload());

    this.swUpdate.unrecoverable.subscribe(() => document.location.reload());

    setInterval(() => this.swUpdate.checkForUpdate().catch(() => undefined), 5 * 60 * 1000);
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') {
        this.swUpdate.checkForUpdate().catch(() => undefined);
      }
    });
  }

  private reload() {
    this.swUpdate.activateUpdate().then(() => document.location.reload());
  }
}
