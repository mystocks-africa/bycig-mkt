<?php

class HTMLMessages 
{
    public static function getForgottenPassword($code) {
        // Determine protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $resetLink = $protocol . $host . "/auth/update-pwd";

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Reset</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .container { background-color: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
                h2 { color: #333; }
                p { font-size: 16px; color: #555; }
                .code { font-size: 20px; font-weight: bold; color: #000; background: #eee; padding: 10px; border-radius: 4px; display: inline-block; }
                .link:hover { background-color: #0056b3; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Password Reset</h2>
                <p>You requested to reset your password. Use the verification code below to proceed:</p>
                <p class='code'>{$code}</p>
                <p>Or click the button below to go directly to the password reset page:</p>
                <a href='{$resetLink}'>Reset Password</a>
                <p>If you did not request this, please ignore this email.</p>
            </div>
        </body>
        </html>
        ";
    }
}
