<?php

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');
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

if($method == 'GET'){
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if($path == '/user_account_info.php/main_user_info'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['user_id'])){
                try{
                    $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","user_role","");
                    $sql = 'SELECT k.email, k.fio, k.telephone FROM klient k WHERE k.id = :my_id ';
                    $res = $db->prepare($sql);
                    $id = (int)$_SESSION['user_id'];
                    $res->bindParam(':my_id', $id, PDO::PARAM_INT);
                    $res->execute();
                    $row = $res->fetchAll(PDO::FETCH_ASSOC);
                    if($row){
                        http_response_code(200);
                        echo json_encode($row);
                        exit;
                    }else{
                        http_response_code(400);
                        exit;
                    }
                }
                catch(PDOException $e){
                    error_log($e->getMessage());
                }
                
            }else{
                http_response_code(418);
                exit;
            }
        }else{
            http_response_code(401);
            exit;
        }
    }else if($path == '/user_account_info.php/booked_log'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['user_id'])){
                try{
                    $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","user_role","");
                    $sql = "SELECT bl.id, bl.date_booked, bl.date_check_in, bl.date_departure, bl.status, bl.id_room,
                                    rt.name as room_name, e.fio as employee_fio, p.summ, rp.photo
                            FROM booked_log bl
                            JOIN hotel_number hn ON bl.id_room = hn.idintifier
                            JOIN room_type rt ON hn.id_type = rt.id
                            JOIN employee e ON bl.id_employee = e.id
                            JOIN payment p ON bl.id = p.id 
                            JOIN room_photo rp ON rt.id = rp.id_room_type  
                            WHERE bl.id_klient = :my_id AND rp.photo NOT LIKE '/image_BD/%\_%'";
                    $res = $db->prepare($sql);
                    $id = (int)$_SESSION['user_id'];
                    $res->bindParam(':my_id', $id, PDO::PARAM_INT);
                    $res->execute();
                    $row = $res->fetchAll(PDO::FETCH_ASSOC);

                    if($row){

                        foreach($row as $key => &$value){
                            $value['add_services'] = [];
                        }

                        $sql = "SELECT bl.id, ase.name FROM booked_log bl
                                JOIN add_services_order_log asol
                                    ON bl.id = asol.id_booked
                                JOIN additional_services ase
                                    ON asol.id_services = ase.id
                                WHERE bl.id_klient = :my_id";
                        $res = $db->prepare($sql);
                        $res->bindParam(':my_id', $id, PDO::PARAM_INT);
                        $res->execute();
                        $row2 = $res->fetchAll(PDO::FETCH_ASSOC);

                        if($row2){
                            foreach($row2 as $key => &$value){
                                foreach($row as $k => &$v){
                                    if($v['id']==$value['id']){
                                        array_push($v['add_services'],$value['name']);
                                        break;
                                    }
                                }
                            }
                        }
                        http_response_code(200);
                        echo json_encode($row);
                        exit;
                    }else{
                        writeLog(json_encode($row));
                        writeLog($id);
                        http_response_code(400);
                        exit;
                    }
                }
                catch(PDOException $e){
                    error_log($e->getMessage());
                }
                
            }else{
                http_response_code(418);
                exit;
            }
        }else{
            http_response_code(401);
            exit;
        }
    }
}else if($method == 'DELETE'){
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if($path == '/user_account_info.php/log_out'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(session_destroy()){
                setcookie('PHPSESSID', '', time() - 3600, '/');
                http_response_code(200);
                exit;
            }else{
                http_response_code(418);
                exit;
            }
        }else{
            http_response_code(404);
            exit;
        }
    }else{
        http_response_code(400);
        exit;
    }
}