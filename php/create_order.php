<?php

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost:8082');  

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(201);  
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
    $path = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if($path == '/create_order.php/create'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['user_id'])){
                try{
                    $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","user_role","");

                    $data = json_decode(file_get_contents('php://input'), true);
                    $id_room = $data['id_room'];
                    $date_booked = $data['date_booked'];
                    $dateIn = $data['date_in'];
                    $dateOut = $data['date_out'];
                    $add_serv = $data['add_serv'];

                    $sql = 'SELECT COUNT(*) as count FROM booked_log bl
                            WHERE bl.id_klient = :my_id AND bl.status = 1';
                    $res = $db->prepare($sql);
                    $id = (int)$_SESSION['user_id'];
                    $res->bindParam(':my_id',$id,PDO::PARAM_INT);
                    if (!$res->execute()) {
                        throw new PDOException(json_encode($res->errorInfo()));
                    }
                    $checkCount = $res->fetchAll(PDO::FETCH_ASSOC);
                    if($checkCount[0]['count']>1){
                        http_response_code(400);
                        exit;
                    }

                    $sql = 'SELECT id_employee FROM current_admin WHERE online = 1';
                    $res = $db->prepare($sql);
                    if (!$res->execute()) {
                        throw new PDOException(json_encode($res->errorInfo()));
                    }
                    $temp = $res->fetchAll(PDO::FETCH_ASSOC);
                    if(!$temp){
                        $sql = 'SELECT id_employee FROM current_admin WHERE reserve_admin = 1';
                        $res = $db->prepare($sql);
                        $res->execute();
                        $temp = $res->fetchAll(PDO::FETCH_ASSOC);
                    }

                    $id_employee = $temp[0]['id_employee'];
                    $sql = "SELECT nextval('booked_log_sq') as id_order";
                    $res = $db->prepare($sql);
                    if (!$res->execute()) {
                        throw new PDOException(json_encode($res->errorInfo()));
                    }
                    $temp = $res->fetchAll(PDO::FETCH_ASSOC);
                    if(!$temp){
                        http_response_code(500);
                        exit;
                    }
                    $id_order = $temp[0]['id_order'];

                    $sql = "INSERT INTO booked_log (id, id_klient, id_room, id_employee, status, date_booked, date_check_in, date_departure) VALUES
                        (:my_id_order, :my_id, :my_id_room, :my_id_employee, 1, :my_date_booked, :my_date_in, :my_date_out)";
                    $res = $db->prepare($sql);
                    $res->bindParam(':my_id_order',$id_order,PDO::PARAM_INT);
                    $res->bindParam(':my_id',$id,PDO::PARAM_INT);
                    $res->bindParam(':my_id_employee',$id_employee,PDO::PARAM_INT);
                    $res->bindParam(':my_date_booked',$date_booked,PDO::PARAM_STR);
                    $res->bindParam(':my_id_room', $id_room, PDO::PARAM_INT);
                    $res->bindParam(':my_date_in',$dateIn,PDO::PARAM_STR);
                    $res->bindParam(':my_date_out',$dateOut,PDO::PARAM_STR);
                    if (!$res->execute()) {
                        throw new PDOException(json_encode($res->errorInfo()));
                    }

                    if(!empty($add_serv)){
                        foreach($add_serv as $key => &$value){
                            $sql = 'INSERT INTO add_services_order_log (id_booked, id_services) VALUES
                                    (:my_id_order,:my_id_serv)';
                            $res = $db->prepare($sql);
                            $res->bindParam(':my_id_order',$id_order,PDO::PARAM_INT);
                            $res->bindParam(':my_id_serv',$value,PDO::PARAM_INT);
                            if (!$res->execute()) {
                                throw new PDOException(json_encode($res->errorInfo()));
                            }
                        }
                    }
                
                    http_response_code(200);
                    exit;
                }
                catch(PDOException $e){
                    die($e->getMessage());
                    http_response_code(418);
                    exit;
                }
                
            }else{
                http_response_code(404);
                exit;
            }
        }
    }
}else if($method == "GET"){
    http_response_code(505);
}