<?php
$to      = 'luis.tavarez@upr.edu';
$subject = 'Test Email';
$message = 'This is a test email';
$headers = 'From: luis.tavarez@outlook.com' . "\r\n" .
    'Reply-To: luis.tavarez@outlook.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?> 