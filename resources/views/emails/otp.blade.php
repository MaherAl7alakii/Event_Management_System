<p>Hello,</p>
<p>Your One-Time Password (OTP) is: <strong>{{ $otp }}</strong></p>
<p>This OTP is valid for {{ config('otp.expiry_minutes', 5) }} minutes.</p>
<p>If you did not request this, please ignore this email.</p>