<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    protected int $otpLength = 6;
    protected int $otpExpiryMinutes = 5;
    protected int $maxAttempts = 5;
    protected int $rateLimitSeconds = 60;

    /*
    |------------------------------------------------------------
    | Generate OTP
    |------------------------------------------------------------
    | type = email_verify | password_reset
    */
    public function generateOtp(string $email, string $type): string
    {
        $this->checkRateLimit($email, $type);

        Cache::forget($this->getOtpKey($email, $type));
        Cache::forget($this->getAttemptsKey($email, $type));

        $otp = $this->generateRandomOtp();

        Cache::put(
            $this->getOtpKey($email, $type),
            $otp,
            now()->addMinutes($this->otpExpiryMinutes)
        );

        return $otp;
    }

    /*
    |------------------------------------------------------------
    | Verify OTP
    |------------------------------------------------------------
    */
    public function verifyOtp(string $email, string $type, string $otp): bool
    {
        $storedOtp = Cache::get($this->getOtpKey($email, $type));

        if (!$storedOtp || $storedOtp !== $otp) {
            $this->incrementAttempts($email, $type);
            return false;
        }

        Cache::forget($this->getOtpKey($email, $type));
        Cache::forget($this->getAttemptsKey($email, $type));

        return true;
    }

    /*
    |------------------------------------------------------------
    | Remaining Attempts
    |------------------------------------------------------------
    */
    public function getRemainingAttempts(string $email, string $type): int
    {
        return $this->maxAttempts - (int) Cache::get($this->getAttemptsKey($email, $type), 0);
    }

    /*
    |------------------------------------------------------------
    | Check Expiry
    |------------------------------------------------------------
    */
    public function isOtpExpired(string $email, string $type): bool
    {
        return !Cache::has($this->getOtpKey($email, $type));
    }

    /*
    |------------------------------------------------------------
    | Increment Attempts
    |------------------------------------------------------------
    */
    protected function incrementAttempts(string $email, string $type): void
    {
        $attempts = Cache::increment($this->getAttemptsKey($email, $type));

        if ($attempts >= $this->maxAttempts) {
            Cache::forget($this->getOtpKey($email, $type));
            Cache::forget($this->getAttemptsKey($email, $type));
        }
    }

    /*
    |------------------------------------------------------------
    | Rate Limiting
    |------------------------------------------------------------
    */
    protected function checkRateLimit(string $email, string $type): void
    {
        $key = $this->getRateLimitKey($email, $type);

        $lastRequestTime = Cache::get($key);

        if ($lastRequestTime && (now()->timestamp - $lastRequestTime) < $this->rateLimitSeconds) {
            $remainingTime = $this->rateLimitSeconds - (now()->timestamp - $lastRequestTime);

            throw new \Exception("Please wait {$remainingTime} seconds before requesting another OTP.");
        }

        Cache::put($key, now()->timestamp, now()->addSeconds($this->rateLimitSeconds));
    }

    /*
    |------------------------------------------------------------
    | Generate OTP
    |------------------------------------------------------------
    */
    protected function generateRandomOtp(): string
    {
        return str_pad(
            random_int(0, (10 ** $this->otpLength) - 1),
            $this->otpLength,
            '0',
            STR_PAD_LEFT
        );
    }

    /*
    |------------------------------------------------------------
    | Keys (IMPORTANT CHANGE)
    |------------------------------------------------------------
    */

    protected function getOtpKey(string $email, string $type): string
    {
        return "otp:{$type}:" . md5($email);
    }

    protected function getAttemptsKey(string $email, string $type): string
    {
        return "otp_attempts:{$type}:" . md5($email);
    }

    protected function getRateLimitKey(string $email, string $type): string
    {
        return "otp_rate_limit:{$type}:" . md5($email);
    }

    /*
    |------------------------------------------------------------
    | Temporary Success Flag (for reset password / protected actions)
    |------------------------------------------------------------
    */
    public function setTemporaryFlag(string $email, string $action, int $minutes = 15): void
    {
        Cache::put($this->getFlagKey($email, $action), true, now()->addMinutes($minutes));
    }

    public function hasTemporaryFlag(string $email, string $action): bool
    {
        return Cache::has($this->getFlagKey($email, $action));
    }

    public function clearTemporaryFlag(string $email, string $action): void
    {
        Cache::forget($this->getFlagKey($email, $action));
    }

    protected function getFlagKey(string $email, string $action): string
    {
        return "temp_flag:{$action}:" . md5($email);
    }
}