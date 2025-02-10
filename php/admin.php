<?php

header('Access-Control-Allow-Origin: http://localhost:8082');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);  
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET'){
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if($path == '/admin.php/name_tables'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['admin_id'])){
                $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
                $sql = "SELECT tablename
                        FROM pg_tables
                        WHERE schemaname = 'public' ";
                $res = $db->prepare($sql);
                $res->execute();
                $row = $res->fetchAll(PDO::FETCH_ASSOC);
                if($row){
                    http_response_code(200);
                    echo json_encode($row);
                }else{
                    http_response_code(500);
                    exit;
                }
            }else{
                http_response_code(403);
                exit;
            }
        }else{
            http_response_code(403);
            exit;
        }
    }
}else if($method == 'POST'){
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if($path == '/admin.php/get_data'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['admin_id'])){
                $data = json_decode(file_get_contents('php://input'), true);
                $name = $data['nametable'];
                $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
                $sql = "SELECT * FROM ".$name;
                $res = $db->prepare($sql);
                $res->execute();
                $row = $res->fetchAll(PDO::FETCH_ASSOC);

                $columns = array_keys($row[0] ?? []);
                $result = [
                    'columns' => $columns,
                    'data' => $row,
                ];

                http_response_code(200);
                echo json_encode($result);
            }else{
                http_response_code(403);
                exit;
            }
        }else{
            http_response_code(403);
            exit;
        }
    }
}else if($method == 'DELETE'){
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if($path == '/admin.php/delete_session'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['admin_id'])){
                $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
                $sql = "UPDATE current_admin SET online = 0 WHERE online > 0";
                $res = $db->prepare($sql);
                if(!$res->execute()){
                    http_response_code(500);
                    exit;
                }else{
                    session_destroy();
                    setcookie('PHPSESSID', '', time() - 3600, '/');
                    http_response_code(200);
                }
            }
        }
    }
}