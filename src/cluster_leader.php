<?php    
    require 'vendor/autoload.php';

    include 'database.php';

    use React\EventLoop\Loop;
    use React\MySQL\QueryResult;

    $loop = Loop::get();

    $request_method = $_SERVER["REQUEST_METHOD"];

    if ($request_method === "GET") {
        $query = "
            SELECT user_login 
            FROM `wp_usermeta` 
            INNER JOIN `wp_users` ON `wp_usermeta`.`user_id` = `wp_users`.`ID` 
            WHERE `meta_key` = 'wp_capabilities' 
            AND `meta_value` = 'a:1:{s:14:\"cluster-leader\";b:1;}';
        ";

        $mysql->query($query)->then(
            function (QueryResult $result) use ($loop) {
                header('Content-Type: application/json');

                $leaders = array_map(function ($row) {
                    return $row['user_login'];
                }, $result->resultRows);

                echo json_encode($leaders);

                $loop->stop(); 
                exit;
            },
            function (Exception $e) use ($loop) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(["error" => $e->getMessage()]);
                $loop->stop(); 
                exit;
            }
        );

        $loop->run(); 
    }
