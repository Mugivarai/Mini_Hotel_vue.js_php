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
    if($path = '/booked_info.php/count_type_of_room'){
        $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","user_role","");
        $sql = "SELECT rt.id,rt.name, rp.photo, rt.price_per_day as price FROM room_type rt 
                JOIN room_photo rp ON rt.id = rp.id_room_type
                WHERE rp.photo NOT LIKE '/image_BD/%\_%'
                ORDER BY rt.id";
        $res = $db->prepare($sql);
        $res->execute();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);

        if($row){
            http_response_code(200);
            echo json_encode($row);
        }else{
            http_response_code(404);
            exit;
        }
    }
}else if($method == 'POST'){
    $path = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if($path == '/booked_info.php/free_rooms'){
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $dateIn = $data['date_in'];
        $dateOut = $data['date_out'];
        $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","user_role","");
        $sql = "SELECT h.idintifier
                FROM hotel_number h
                WHERE h.id_type = :my_id
                AND NOT EXISTS (
                    SELECT 1
                    FROM booked_log b
                    WHERE b.id_room = h.idintifier
                    AND (
                        (DATE(b.date_check_in) <= DATE(:my_date_out) AND DATE(b.date_departure) >= DATE(:my_date_in))
                    )
                )";
        $res = $db->prepare($sql);
        $res->bindParam(':my_id',$id,PDO::PARAM_INT);
        $res->bindParam(':my_date_in',$dateIn,PDO::PARAM_STR);
        $res->bindParam(':my_date_out',$dateOut,PDO::PARAM_STR);
        $res->execute();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if($row){
            http_response_code(200);
            echo json_encode($row);
        }else{
            http_response_code(404);
            exit;
        }
    }else if($path = '/booked_info.php/check_summ'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['user_id'])){
                try{
                    $data = json_decode(file_get_contents('php://input'), true);
                    $id_booked = (int)$data['id_booked'];
                    if($id_booked <= 0){
                        http_response_code(400);
                        exit;
                    }
                    $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","user_role","");
                    $sql = 'SELECT computed_summ_booked(:my_id) as summ';
                    $res = $db->prepare($sql);
                    $res->bindParam(':my_id',$id_booked,PDO::PARAM_INT);
                    $res->execute();
                    $summ = $res->fetchAll(PDO::FETCH_ASSOC);
                    if($summ){
                        http_response_code(200);
                        echo json_encode($summ);
                        exit;
                    }else{
                        http_response_code(404);
                        exit;
                    }
                }
                catch(PDOException $e){
                    error_log($e->getMessage());
                }
            }
        }
    }
}else if($method == 'PUT'){
    if($path = '/booked_info.php/create_payment'){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            if(isset($_SESSION['user_id'])){
                try{
                    $data = json_decode(file_get_contents('php://input'), true);
                    $id_booked = (int)$data['id_booked'];
                    $payment_type = $data['payment_type'];
                    $date = $data['date'];
    
                    if($id_booked <= 0){
                        http_response_code(400);
                        exit;
                    }
    
                    $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","admin","admin");
                    $sql = 'UPDATE payment 
                            SET summ = computed_summ_booked(:my_id_booked),
                                date = :my_date,
                                payment_type = :my_payment_type
                            WHERE id = :my_id_booked';
                    $res = $db->prepare($sql);
                    $res->bindParam(':my_id_booked',$id_booked,PDO::PARAM_INT);
                    $res->bindParam(':my_date',$date,PDO::PARAM_STR);
                    $res->bindParam(':my_payment_type',$payment_type,PDO::PARAM_INT);
                    if($res->execute()){
                        http_response_code(200);
                    }else{
                        http_response_code(404);
                    }
                }
                catch(PDOException $e){
                    error_log($e->getMessage());
                }
            }
        }
    }
    
}