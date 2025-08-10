<?php 

namespace App\Core;

class ControllerHelper
{
    public function render($view, $data = [])
    {
        extract($data);

        include __DIR__ . "/../views/$view.php";
    }

    public function redirectToResult($msg, $msgType) 
    {
        try {

            if ($msgType == "success" || $msgType == "error") {
                header("Location: /redirect?message=$msg&message_type=$msgType");
            }

            else {
                throw new Exception("Message type is not valid. It needs to be either success or error");
            }
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }
}