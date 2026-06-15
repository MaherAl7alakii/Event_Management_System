<?php

return [
    'length' => env('OTP_LENGTH', 6),
    'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 5),
    'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),
    'rate_limit_seconds' => env('OTP_RATE_LIMIT_SECONDS', 60),
    'require_verification' => env('OTP_REQUIRE_VERIFICATION', true),
];