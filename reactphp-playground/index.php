<?php
    require 'vendor/autoload.php';

    use React\EventLoop\Loop;
    use React\Http\HttpServer;
    use React\Socket\SocketServer;
    use React\Http\Browser;
    use React\Socket\Connector;
    use React\Http\Message\Response;
    use Psr\Http\Message\ServerRequestInterface;

    $loop = Loop::get();
    $connector = new Connector($loop);
    $browser = new Browser($connector);

    $server = new HttpServer(function (ServerRequestInterface $request) {
        global $loop;

        $path = $request->getUri()->getPath();

        // ==== LEARNING NOTE ====  
        // makes another request to get /favicon.ico for every request made to server
        // we don't want to return anything in that request (if we let regular execution occur, it will re-run same code again)
        
        if ($path == "/favicon.ico") {
            return new Response(204);
        }

        $loop->addTimer(1.5, function (){
            echo "Timer done\n";
            echo "Execution completed!";
        });

        echo "This will concurrent with the timer, but execute before it due to lower processing times.\n";

        return new Response(200, ['Content-Type' => 'text/html'], "Hello from $path");
    });

    $socket = new SocketServer('0.0.0.0:8080', [], $loop);
    $server->listen($socket);

    echo "Server running at http://127.0.0.1:8080\n";

    $loop->run();
?>
