<?php 
    function timer(callable $func) {
        usleep(1500000);  // wait 1.5 seconds
        $start = microtime(true);
        $func();
        $end = microtime(true);
        $duration = $end - $start;
        return $duration;
    }

    $timeTaken = timer(function() {
        echo "Timer done <br>";
        echo "Execution completed! <br>";
    });

    echo "This will concurrent with the timer, but execute before it due to lower processing times.\n";
?>