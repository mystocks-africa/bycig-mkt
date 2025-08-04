<?php

namespace App;

class Controller
{
    protected function render($view, $data = [])
    {
        extract($data);

        include __DIR__ . "/../views/$view.php";
    }

    protected function redirectAuth() {
        $session_id_cookie = $_COOKIE['session_id'] ?? null;

        if (isset($session_id_cookie)) {
            header("Location: signout");
            exit();
        }
    }

    protected function redirectNotAuth() {
        $session_id_cookie = $_COOKIE['session_idd'] ?? null;

        if (!isset($session_id_cookie)) {
            header("Location: signin");
            exit();
        }
    }
}