<?php

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost:8082');  

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);  
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET'){
    $path = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if($path == '/check_session_admin.php'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['admin_id'])){
                http_response_code(200);
                exit;
            }else{
                http_response_code(403);
                exit;
            }
        }else{
            http_response_code(403);
            exit;
        }
    }else{
        http_response_code(404);
        exit;
    }
}else{
    http_response_code(404);
    exit;
}