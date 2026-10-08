<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OtpService
{
    /**
     * Generate a 6-digit Email Verification OTP and send it to the user.
     */
    public static function generateAndSend(User $user): string
    {
        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'verification_channel' => 'email',
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        // Log Email OTP for local dev/debugging
        Log::info("=== EMAIL VERIFICATION OTP FOR {$user->email} ===: {$otp}");

        // Dispatch Email Notification with fast pre-check to prevent 504 timeouts on cloud environments that block SMTP
        $mailer = config('mail.default', 'log');
        $smtpHost = config('mail.mailers.smtp.host');
        $smtpPort = (int) config('mail.mailers.smtp.port', 587);

        $canDispatch = true;
        if ($mailer === 'smtp' && !empty($smtpHost)) {
            $errno = 0;
            $errstr = '';
            $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 1.5);
            if (is_resource($socket)) {
                fclose($socket);
            } else {
                $canDispatch = false;
                Log::warning("SMTP server {$smtpHost}:{$smtpPort} is unreachable or blocked. OTP logged safely: {$otp}");
            }
        }

        if ($canDispatch) {
            try {
                $user->notify(new SendOtpNotification($otp));
            } catch (\Throwable $e) {
                Log::warning('Email OTP notification dispatch error: ' . $e->getMessage());
            }
        }

        return $otp;
    }
}
