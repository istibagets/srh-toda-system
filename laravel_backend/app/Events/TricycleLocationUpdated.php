<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TricycleLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $driverId;
    public float $lat;
    public float $lng;
    public ?float $heading;
    public ?float $speed;
    public ?int $rideId;
    public ?string $rideStatus;

    public function __construct(int $driverId, float $lat, float $lng, ?float $heading = null, ?float $speed = null, ?int $rideId = null, ?string $rideStatus = null)
    {
        $this->driverId = $driverId;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->heading = $heading;
        $this->speed = $speed;
        $this->rideId = $rideId;
        $this->rideStatus = $rideStatus;
    }

    public function broadcastOn(): array
    {
        // Only ever broadcast on the ride's private channel (never to everyone).
        return $this->rideId ? [new PrivateChannel('srh-ride-location.' . $this->rideId)] : [];
    }

    public function broadcastAs(): string
    {
        return 'tricycle.location';
    }
}
