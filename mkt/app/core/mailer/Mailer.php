<?php
namespace App\Core\Mailers;

include_once __DIR__ . "/../../../utils/env.php";

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send($email, $message): void 
    {
        global $env;

        $mail = new PHPMailer();

        // Enable HTML emails
        $mail->isHTML(true);

        // Gmail SMTP configuration
        $mail->isSMTP();
        $mail->Host = $env["SMTP_HOST"];
        $mail->SMTPAuth = true;
        $mail->Username = $env["SMTP_USERNAME"];
        $mail->Password = $env["SMTP_PASSWORD"];
        $mail->SMTPSecure = 'tls'; // Use TLS for port 587
        $mail->Port = $env["SMTP_PORT"];

        $mail->setFrom($env["SMTP_USERNAME"], 'BYCIG MKT');
        $mail->addAddress($email);
        $mail->Subject = 'Verification Code - BYCIG MKT';
        $mail->Body = $message;

        $mail->send();
    }
}
