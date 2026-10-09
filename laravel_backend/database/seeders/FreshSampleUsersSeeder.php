<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Report;
use App\Models\Ride;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Wipes all application data and seeds sample accounts, trip history and reports.
 *
 * Run with: php artisan db:seed --class=FreshSampleUsersSeeder --force
 */
class FreshSampleUsersSeeder extends Seeder
{
    // Reference point for fares: Santa Rosa Homes main gate.
    private const ORIGIN = [15.42955, 120.92240];

    public function run(): void
    {
        $this->wipeData();
        $this->resetAttachments();

        $password = 'admin123';

        // Admin
        $steve = $this->createUser('Steve Gates Roquero', 'steve@gmail.com', 'admin', $password);
        $this->createDriverProfile($steve, '100001', 'Approved');

        // Named drivers
        $named = [
            ['Nathane Martin', 'nathane@gmail.com', '100002', 'Approved'],
            ['Ryan Busty Paras', 'basty@gmail.com',   '100003', 'Approved'],
            ['Ernest Win Montes', 'ernest@gmail.com',  '100004', 'Pending'],
        ];
        foreach ($named as [$name, $email, $mtop, $status]) {
            $user = $this->createUser($name, $email, 'driver', $password);
            $this->createDriverProfile($user, $mtop, $status);
        }

        // 5 passengers
        $passengerNames = ['Maria Clarissa Santos', 'Juan Miguel Dela Cruz', 'Angelica Mae Villanueva', 'Jose Rafael Bautista', 'Rosario Grace Mendoza'];
        foreach ($passengerNames as $i => $name) {
            $n = $i + 1;
            $this->createUser($name, "passenger{$n}@gmail.com", 'passenger', $password);
        }

        // 5 more drivers: 2 approved, 1 pending, 2 rejected
        $extraDrivers = [
            1 => ['Ricardo Manuel Dalisay', 'Approved'],
            2 => ['Eduardo Santiago Reyes', 'Approved'],
            3 => ['Romeo Andres Pascual', 'Pending'],
            4 => ['Danilo Cruz Aquino', 'Rejected'],
            5 => ['Ferdinand Jose Navarro', 'Rejected'],
        ];
        foreach ($extraDrivers as $i => [$name, $status]) {
            $user = $this->createUser($name, "driver{$i}@gmail.com", 'driver', $password);
            $this->createDriverProfile($user, (string) (200000 + $i), $status);
        }

        $this->seedRides();
        $this->seedReports();

        echo 'Seeded ' . User::count() . ' users, ' . Ride::count() . ' rides, ' . Report::count() . ' reports (password: ' . $password . ')' . PHP_EOL;
    }

    private function createUser(string $name, string $email, string $role, string $password): User
    {
        return User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => $password,
            'role'              => $role,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
    }

    private function createDriverProfile(User $user, string $mtop, string $status): Driver
    {
        $mtopPath = "documents/mtop_{$user->id}.png";
        $licensePath = "drivers_license/license_{$user->id}.png";

        File::copy(__DIR__ . '/assets/mtop-sample.png', storage_path("app/public/{$mtopPath}"));
        File::copy(__DIR__ . '/assets/license-sample.png', storage_path("app/public/{$licensePath}"));

        return Driver::create([
            'user_id'              => $user->id,
            'full_name'            => $user->name,
            'mtop_number'          => $mtop,
            'compliance_status'    => $status,
            'is_online'            => false,
            'mtop_certificate_url' => $mtopPath,
            'drivers_license_url'  => $licensePath,
            'appeal_attachments'   => [],
            'suspension_reason'    => $status === 'Rejected'
                ? 'Application rejected: MTOP permit and license could not be verified.'
                : null,
        ]);
    }

    /** Destinations as [name, lat, lng, weight]. Coordinates are approximate. */
    private function places(): array
    {
        return [
            // Inside / right next to Santa Rosa Homes and Santa Rosa town
            ['Clubhouse', 15.42780, 120.92410, 14],
            ['Main Gate Guard House', 15.42955, 120.92240, 6],
            ['Santa Rosa Public Market', 15.42470, 120.93843, 16],
            ['SM City Cabanatuan', 15.46701, 120.95436, 16],
            ['Santa Rosa Municipal Hall, Santa Rosa, Nueva Ecija', 15.42560, 120.93730, 5],
            ['Brgy. La Fuente, Santa Rosa, Nueva Ecija', 15.43000, 120.93300, 4],
            ['Brgy. Cojuangco, Santa Rosa, Nueva Ecija', 15.43900, 120.92500, 4],
            ['Brgy. Mapalad, Santa Rosa, Nueva Ecija', 15.43300, 120.94800, 4],
            ['Brgy. Rizal, Santa Rosa, Nueva Ecija', 15.41800, 120.94300, 3],
            // Cabanatuan City
            ['Robinsons Cabanatuan', 15.47800, 120.96000, 5],
            ['Cabanatuan City Hall', 15.48690, 120.96720, 3],
            ['Wesleyan University-Philippines, Cabanatuan City', 15.49030, 120.97100, 3],
            ['Nueva Ecija Doctors Hospital, Cabanatuan City', 15.48750, 120.96650, 3],
            // Farther towns
            ['Zaragoza Public Market, Zaragoza, Nueva Ecija', 15.45100, 120.80100, 2],
            ['San Leonardo Public Market, San Leonardo, Nueva Ecija', 15.35700, 120.96800, 2],
            ['Gapan City Public Market, Gapan, Nueva Ecija', 15.30700, 120.94600, 2],
        ];
    }

    private function distanceKm(float $lat, float $lng): float
    {
        [$lat0, $lng0] = self::ORIGIN;
        $dLat = deg2rad($lat - $lat0);
        $dLng = deg2rad($lng - $lng0);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat0)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * asin(sqrt($a));
    }

    /**
     * Fare from Santa Rosa Homes, in steps of 5 with no decimals.
     * Calibrated so Public Market = 60 and SM Cabanatuan = 120; minimum 50.
     */
    private function fareFor(float $lat, float $lng): int
    {
        $raw = 30 + 16.7 * $this->distanceKm($lat, $lng);

        return max(50, (int) (round($raw / 5) * 5));
    }

    private function weightedPlace(): array
    {
        $places = $this->places();
        $roll = mt_rand(1, array_sum(array_column($places, 3)));
        foreach ($places as $p) {
            $roll -= $p[3];
            if ($roll <= 0) {
                return $p;
            }
        }

        return $places[0];
    }

    private function seedRides(): void
    {
        mt_srand(2026);
        $passengers = User::where('role', 'passenger')->get();
        // Every approved driver profile, including the admin who also drives.
        $drivers = Driver::where('compliance_status', 'Approved')->get();
        $tags = ['Punctual', 'Polite Driver', 'Clean Tricycle', 'Safe Driving', 'Fair Fare'];
        $comments = [
            5 => ['Very courteous and safe driving!', 'Fast and polite, thank you.'],
            4 => ['Good trip.', 'Okay naman po, on time.'],
        ];

        foreach ($passengers as $pax) {
            $count = mt_rand(5, 8);
            for ($n = 0; $n < $count; $n++) {
                [$name, $lat, $lng] = $this->weightedPlace();
                $blockLot = 'Blk ' . mt_rand(1, 40) . ' Lot ' . mt_rand(1, 30) . ', Santa Rosa Homes';
                $homeLat = 15.4285 + mt_rand(-40, 40) / 10000;
                $homeLng = 120.9235 + mt_rand(-40, 40) / 10000;
                $fare = $this->fareFor($lat, $lng);

                // Most trips start at home; some come back from the destination.
                $fromHome = mt_rand(1, 100) <= 75 || $name === 'Clubhouse';
                $pickup = $fromHome ? [$blockLot, $homeLat, $homeLng] : [$name, $lat, $lng];
                $drop = $fromHome ? [$name, $lat, $lng] : [$blockLot, $homeLat, $homeLng];

                $created = Carbon::now()->subDays(mt_rand(0, 14))->subHours(mt_rand(1, 10))->subMinutes(mt_rand(1, 59));
                $completed = mt_rand(1, 100) <= 85;
                $rating = $completed ? (mt_rand(1, 100) <= 70 ? 5 : 4) : null;

                Ride::create([
                    'passenger_id'    => $pax->id,
                    'driver_id'       => $drivers->random()->user_id,
                    'status'          => $completed ? 'completed' : 'cancelled',
                    'pickup_location' => $pickup[0],
                    'pickup_lat'      => $pickup[1],
                    'pickup_lng'      => $pickup[2],
                    'destination'     => $drop[0],
                    'destination_lat' => $drop[1],
                    'destination_lng' => $drop[2],
                    'fare'            => $fare,
                    'rating'          => $rating,
                    'review_comment'  => $completed ? $comments[$rating][array_rand($comments[$rating])] : null,
                    'feedback_tags'   => $completed ? implode(', ', (array) array_rand(array_flip($tags), mt_rand(1, 3))) : null,
                    'created_at'      => $created,
                    'updated_at'      => $created->copy()->addMinutes(mt_rand(10, 40)),
                ]);
            }
        }
    }

    private function seedReports(): void
    {
        $samples = [
            ['Overcharging', 'Fare higher than the posted matrix', 'Driver asked for more than the posted fare for this trip.', 'pending', null],
            ['Reckless Driving', 'Speeding near the highway', 'Driver was overtaking aggressively along the national highway.', 'investigating', 'TODA officer is verifying the complaint with the terminal marshal.'],
            ['Rude Behavior', 'Discourteous to passenger', 'Driver had no change and raised his voice when asked politely.', 'resolved', 'Driver was reprimanded at the TODA office and the difference was refunded.'],
            ['Lost Item', 'Left a bag in the tricycle', 'Left a backpack on the sidecar seat after drop-off.', 'resolved', 'Bag was surrendered to the TODA office and returned to the owner.'],
            ['Vehicle Condition', 'Broken sidecar seat', 'Sidecar seat spring was sticking out and tore a jacket.', 'investigating', 'Vehicle inspection scheduled before permit renewal.'],
            ['Refusal to Convey', 'Refused the destination', 'Driver declined the trip saying traffic was too heavy.', 'dismissed', 'Tricycle had a flat tire and was on the way to repair.'],
            ['Overcharging', 'Extra night charge', 'Driver added a night surcharge that is not in the approved fare matrix.', 'pending', null],
            ['Other', 'Question about scheduled trips', 'Asking if advance booking is supported through dispatch.', 'dismissed', 'General inquiry, not a safety report. Advised to coordinate with the TODA office.'],
        ];

        $completed = Ride::where('status', 'completed')->orderBy('id')->get();
        // One report per distinct driver first, then fill from the rest.
        $pool = $completed->unique('driver_id')->concat($completed)->unique('id')->values();

        foreach ($samples as $i => [$category, $subject, $description, $status, $notes]) {
            $ride = $pool[$i] ?? null;
            if (! $ride) {
                continue;
            }
            $created = Carbon::parse($ride->created_at)->addHours(mt_rand(1, 6));
            $resolved = in_array($status, ['resolved', 'dismissed']) ? $created->copy()->addHours(mt_rand(2, 20)) : null;

            Report::create([
                'reporter_id' => $ride->passenger_id,
                'driver_id'   => $ride->driver_id,
                'ride_id'     => $ride->id,
                'category'    => $category,
                'subject'     => $subject,
                'description' => $description,
                'status'      => $status,
                'admin_notes' => $notes,
                'resolved_at' => $resolved,
                'created_at'  => $created,
                'updated_at'  => $resolved ?? $created,
            ]);
        }
    }

    /** Truncate every table except Laravel's migrations bookkeeping. */
    private function wipeData(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (Schema::getTableListing(schemaQualified: false) as $table) {
            if ($table === 'migrations') {
                continue;
            }
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }

    /** Remove previously uploaded attachments so only sample files remain. */
    private function resetAttachments(): void
    {
        foreach (['documents', 'drivers_license', 'appeals', 'profile-photos'] as $dir) {
            $path = storage_path("app/public/{$dir}");
            File::deleteDirectory($path);
            File::makeDirectory($path, 0755, true);
        }
    }
}
