import { Component, input, output, signal } from '@angular/core';
import { CommonModule } from '@angular/common';

export interface TerminalTripDetails {
  destination: string;
  passengerCount: number;
  fare: number;
}

// Fares are distance-based from the SRH TODA terminal, rounded to the nearest 5
// (calibrated: Public Market = 60, SM Cabanatuan = 120, minimum 50).
const TERMINAL_ORIGIN = { lat: 15.42955, lng: 120.9224 };

const TERMINAL_PLACES = [
  { name: 'Clubhouse, Santa Rosa Homes', shortName: 'Clubhouse', lat: 15.4278, lng: 120.9241 },
  { name: 'Santa Rosa Public Market', shortName: 'Public Market', lat: 15.4247, lng: 120.93843 },
  { name: 'Santa Rosa Municipal Hall', shortName: 'Municipal Hall', lat: 15.4256, lng: 120.9373 },
  { name: 'Brgy. La Fuente, Santa Rosa', shortName: 'La Fuente', lat: 15.43, lng: 120.933 },
  { name: 'Brgy. Cojuangco, Santa Rosa', shortName: 'Cojuangco', lat: 15.439, lng: 120.925 },
  { name: 'Brgy. Mapalad, Santa Rosa', shortName: 'Mapalad', lat: 15.433, lng: 120.948 },
  { name: 'Brgy. Rizal, Santa Rosa', shortName: 'Rizal', lat: 15.418, lng: 120.943 },
  { name: 'SM City Cabanatuan', shortName: 'SM Cabanatuan', lat: 15.46701, lng: 120.95436 },
  { name: 'Robinsons Cabanatuan', shortName: 'Robinsons', lat: 15.478, lng: 120.96 },
  { name: 'Cabanatuan City Hall', shortName: 'Cabanatuan City Hall', lat: 15.4869, lng: 120.9672 },
  { name: 'Zaragoza Public Market', shortName: 'Zaragoza', lat: 15.451, lng: 120.801 },
  { name: 'San Leonardo Public Market', shortName: 'San Leonardo', lat: 15.357, lng: 120.968 },
  { name: 'Gapan City Public Market', shortName: 'Gapan', lat: 15.307, lng: 120.946 },
];

function distanceKm(lat: number, lng: number): number {
  const rad = (d: number) => (d * Math.PI) / 180;
  const dLat = rad(lat - TERMINAL_ORIGIN.lat);
  const dLng = rad(lng - TERMINAL_ORIGIN.lng);
  const a =
    Math.sin(dLat / 2) ** 2 +
    Math.cos(rad(TERMINAL_ORIGIN.lat)) * Math.cos(rad(lat)) * Math.sin(dLng / 2) ** 2;
  return 6371 * 2 * Math.asin(Math.sqrt(a));
}

function fareForKm(km: number): number {
  return Math.max(50, Math.round((30 + 16.7 * km) / 5) * 5);
}

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
  closeModal = output<void>();
  confirmRide = output<TerminalTripDetails>();

  destinations = TERMINAL_PLACES.map((p) => {
    const km = distanceKm(p.lat, p.lng);
    return { name: p.name, shortName: p.shortName, fare: fareForKm(km), distance: `${km.toFixed(1)} km` };
  });

  selectedDestination = signal<string>(this.destinations[0].name);
  passengerCount = signal<number>(1);
  calculatedFare = signal<number>(this.destinations[0].fare);

  onDestinationChange(destName: string): void {
    this.selectedDestination.set(destName);
    const match = this.destinations.find((d) => d.name === destName);
    if (match) {
      const multiplier = this.passengerCount() > 2 ? 1.5 : 1;
      this.calculatedFare.set(Math.round((match.fare * multiplier) / 5) * 5);
    }
  }

  setPassengerCount(count: number): void {
    this.passengerCount.set(count);
    this.onDestinationChange(this.selectedDestination());
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
