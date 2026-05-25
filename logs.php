<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (file_exists("api_logs.json")) {
    echo file_get_contents("api_logs.json");
} else {
    echo json_encode([]);
}
?>