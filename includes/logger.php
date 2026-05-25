<?php

function logApi($endpoint, $status, $latency, $error = "") {

    $logFile = $logFile = __DIR__ . "/../api_logs.json";

    $logs = [];

    if(file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true);
    }

    $logs[] = [
        "time" => date("h:i:s A"),
        "endpoint" => $endpoint,
        "status" => $status,
        "latency" => $latency,
        "error" => $error
    ];

    if(count($logs) > 100){
        array_shift($logs);
    }

    file_put_contents($logFile, json_encode($logs));
}
?>