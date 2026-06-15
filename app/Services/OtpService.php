<?php 

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpService
{
    protected int $otpLength = 6;
    protected int $otpExpiryMinutes = 5;
    protected int $maxAttempts = 5;
    protected int $rateLimitSeconds = 60; 

    /**
     * Generate a new OTP and store it in Redis.
     * Invalidates any previous OTPs for the user.
     */
    public function generateOtp(string $email): string
    {
        $this->checkRateLimit($email);

        Cache::forget($this->getOtpKey($email));
        Cache::forget($this->getAttemptsKey($email));

        $otp = $this->generateRandomOtp();

        Cache::put($this->getOtpKey($email), $otp, now()->addMinutes($this->otpExpiryMinutes));

        return $otp;
    }

    /** Verify the given OTP against the stored one.*/
    public function verifyOtp(string $email, string $otp): bool
    {
        $storedOtp = Cache::get($this->getOtpKey($email));

        if (!$storedOtp || $storedOtp !== $otp) {
            $this->incrementAttempts($email);
            return false;
        }
        Cache::forget($this->getOtpKey($email));
        Cache::forget($this->getAttemptsKey($email));

        return true;
    }

    /**Get the remaining attempts for an email.*/
    public function getRemainingAttempts(string $email): int
    {
        return $this->maxAttempts - (int) Cache::get($this->getAttemptsKey($email), 0);
    }

    /**Check if the OTP for the given email has expired.*/
    public function isOtpExpired(string $email): bool
    {
        return !Cache::has($this->getOtpKey($email));
    }

    /**
     * Increment the failed attempts counter for an email.
     * If max attempts reached, invalidate the OTP.
     */
    protected function incrementAttempts(string $email): void
    {
        $attempts = Cache::increment($this->getAttemptsKey($email));

        if ($attempts >= $this->maxAttempts) {
            Cache::forget($this->getOtpKey($email)); // Invalidate OTP after max attempts
            // Optionally, you can add a temporary block for this email here
            // Cache::put($this->getBlockKey($email), true, now()->addMinutes(10));
        }
    }

    /**Check if the user is rate-limited.*/
    protected function checkRateLimit(string $email): void
    {
        $lastRequestTime = Cache::get($this->getRateLimitKey($email));

        if ($lastRequestTime && (now()->timestamp - $lastRequestTime) < $this->rateLimitSeconds) {
            $remainingTime = $this->rateLimitSeconds - (now()->timestamp - $lastRequestTime);
            throw new \Exception("Please wait {$remainingTime} seconds before requesting another OTP.");
        }

        Cache::put($this->getRateLimitKey($email), now()->timestamp, now()->addSeconds($this->rateLimitSeconds));
    }

    /**Generate a random numeric OTP.*/
    protected function generateRandomOtp(): string
    {
        return str_pad(random_int(0, (10 ** $this->otpLength) - 1), $this->otpLength, '0', STR_PAD_LEFT);
    }

    /**Get the Redis key for OTP storage.*/
    protected function getOtpKey(string $email): string
    {
        return 'otp:' . md5($email);
    }

    /**Get the Redis key for OTP attempts.*/
    protected function getAttemptsKey(string $email): string
    {
        return 'otp_attempts:' . md5($email);
    }

    /**Get the Redis key for rate limiting.*/
    protected function getRateLimitKey(string $email): string
    {
        return 'otp_rate_limit:' . md5($email);
    }
} 