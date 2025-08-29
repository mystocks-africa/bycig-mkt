<?php
namespace App\Core\Mailers\HTMLMessages;

class HTMLMessages 
{
    public static function getForgottenPassword($code) {
        // Determine protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $resetLink = "{$protocol}{$host}/auth/update-pwd?code={$code}";

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Reset</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #fff; color: #000; padding: 20px; }
                .container { background-color: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; border: 1px solid #000; }
                h2 { color: #000; }
                p { font-size: 16px; color: #222; }
                .link { 
                    display: inline-block;
                    padding: 12px 24px;
                    font-size: 18px;
                    font-weight: bold;
                    color: #fff !important;  /* Force white color */
                    background-color: #000;
                    border: none;
                    border-radius: 4px;
                    text-decoration: none;
                    cursor: pointer;
                    transition: background 0.2s;
                    margin-top: 16px;
                }
                .link:hover { 
                    background-color: #222; 
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Password Reset (NEVER SHARE WITH ANYONE)</h2>
                <p>You requested to reset your password. Use the verification code below to proceed:</p>
                <p>Press this link to proceed further:</p>
                <a href='{$resetLink}' class='link'>Reset Password</a>
                <p>The code will expire in 5 minutes, request a new one if this happens. If you did not request this, please ignore this email.</p>
            </div>
        </body>
        </html>
        ";
    }
}
