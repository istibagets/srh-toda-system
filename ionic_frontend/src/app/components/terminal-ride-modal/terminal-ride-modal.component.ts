import { Component, computed, input, output, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DEFAULT_WALKIN_ZONES, WalkinZone, walkinFare } from '../../utils/fare';

export interface TerminalTripDetails {
  destination: string;
  passengerCount: number;
  fare: number;
}

/**
 * Walk-in and wayside trips. The destinations and their fixed fares for 1 to 4 passengers are the
 * walk-in zones set by the Superadministrator, so they can change without a code change.
 */
@Component({
  selector: 'app-terminal-ride-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './terminal-ride-modal.component.html',
  styleUrls: ['./terminal-ride-modal.component.scss'],
})
export class TerminalRideModalComponent {
  isOpen = input<boolean>(false);
  isWayside = input<boolean>(false);
  zones = input<WalkinZone[]>([]);
  closeModal = output<void>();
  confirmRide = output<TerminalTripDetails>();

  private zoneList = computed(() => (this.zones().length ? this.zones() : DEFAULT_WALKIN_ZONES));

  private pickedDestination = signal<string>('');
  passengerCount = signal<number>(1);

  destinationsList = computed(() => {
    const pax = this.passengerCount();
    return this.zoneList().map((z) => ({
      name: z.name,
      shortName: z.name.replace(/^Santa Rosa /, ''),
      fare: walkinFare(z, pax),
    }));
  });

  selectedDestination = computed(() => {
    const list = this.destinationsList();
    return list.find((d) => d.name === this.pickedDestination())?.name ?? list[0]?.name ?? '';
  });

  calculatedFare = computed(() => walkinFare(this.zoneList().find((z) => z.name === this.selectedDestination()), this.passengerCount()));

  onDestinationChange(destName: string): void {
    this.pickedDestination.set(destName);
  }

  setPassengerCount(count: number): void {
    this.passengerCount.set(count);
  }

  onConfirm(): void {
    this.confirmRide.emit({
      destination: this.selectedDestination(),
      passengerCount: this.passengerCount(),
      fare: this.calculatedFare(),
    });
    this.closeModal.emit();
  }

  onClose(): void {
    this.closeModal.emit();
  }
}
