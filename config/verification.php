<?php

return[
  // Verification technical
    // default => without any verification
    // email => with email verification using signed URLs (register)
    // cvt => with email verification using custom verification token ( register)
    // passwordless => passwordless authentication (Login)
    // otp => OTP authentication (login)

    'way' => 'otp',
];
