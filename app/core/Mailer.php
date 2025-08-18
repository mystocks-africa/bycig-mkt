<?php

namespace App\Core;

require __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . "/../../utils/env.php";

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send($email, $code) 
    {
        global $env;

        $mail = new PHPMailer();
        $mail->SMTPDebug = 2; // Remove this after testing

        // Gmail SMTP configuration
        $mail->isSMTP();
        $mail->Host = $env["SMTP_HOST"];
        $mail->SMTPAuth = true;
        $mail->Username = $env["SMTP_USERNAME"];
        $mail->Password = $env["SMTP_PASSWORD"];
        $mail->SMTPSecure = 'tls'; // Use TLS for port 587
        $mail->Port = $env["SMTP_PORT"];

        $mail->setFrom($env["SMTP_USERNAME"], 'BYCIG MKT');
        $mail->addAddress($email); // Use the parameter
        $mail->Subject = 'Verification Code - BYCIG MKT';
        $mail->Body = 'Your verification code is ' . $code;

        $mail->send();
    }
}
