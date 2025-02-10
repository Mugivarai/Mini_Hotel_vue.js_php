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

function writeLog($message) {
    $logFile = 'log.txt';

    $file = fopen($logFile, 'a');

    if ($file) {

        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message" . PHP_EOL;

        fwrite($file, $logMessage);

        fclose($file);
    } else {
        echo 'Не удалось открыть лог-файл для записи.';
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path == '/check_session_user.php') {
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if (isset($_SESSION['user_id'])) {
                http_response_code(200);
                $response = ['status' => $_SESSION['user_id']];
                echo json_encode($response);
            } else {
                $temp = $cookieSessionId;
                http_response_code(401); 
                echo json_encode($temp);
                exit;
            }
        }else{
            http_response_code(403);
        }
    } else {
        $response = ['status' => 'Неверные путь'];
        http_response_code(404);
        echo json_encode($response);
    }
} else {
    http_response_code(405); 
    $response = ['error' => 'Метод не поддерживается'];
    echo json_encode($response);
}
?>