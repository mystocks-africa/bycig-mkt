<?php
require_once __DIR__ . "/../../vendor/autoload.php";
include_once __DIR__ . "/../../utils/env.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Core\Session;

class Mailer
{
    public static function send($email, $code) 
    {
        global $env; // use the associative array from env.php

        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $env["SMTP_HOST"];
            $mail->SMTPAuth   = true;
            $mail->Username   = $env["SMTP_USERNAME"];
            $mail->Password   = $env["SMTP_PASSWORD"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $env["SMTP_PORT"];

            // Sender
            $mail->setFrom($env["SMTP_USERNAME"], $env["SMTP_FROM_NAME"] ?? 'Your App Name');

            // Recipient
            $mail->addAddress( $email);

            // Content
            $mail->isHTML(true);
            $mail->Subject =  'Password Reset Code';
            $mail->Body    = "Your password reset code is: <b>{$code}</b>";
            $mail->AltBody = strip_tags($mail->Body);

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
        }
    }
}
