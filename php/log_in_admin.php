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

if($method == 'POST'){
    $path = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if($path = '/log_in_admin.php'){
        $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel","admin","admin");
        $sql = 'SELECT 1 FROM current_admin WHERE online > 0';
        $res = $db->prepare($sql);
        $res->execute();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if($row){
            http_response_code(505);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $phone = $data['phone'];
        if($phone[0] == '+'){
            $phone = substr($phone,1);
        }
        $phone = "%$phone";
        $password = $data['password'];

        $sql = "SELECT password as hash, ca.id FROM current_admin ca
                JOIN employee e 
                ON ca.id = e.id
                WHERE e.telephone LIKE :my_phone";
        
        $res = $db->prepare($sql);
        $res->bindParam(':my_phone',$phone,PDO::PARAM_STR);
        $res->execute();
        $temp = $res->fetchAll(PDO::FETCH_ASSOC);
        if($temp){
            $hash = $temp[0]['hash'];
            if(password_verify($password,$hash)){
                $id = (int)$temp[0]['id'];
                $sql = 'UPDATE current_admin ca SET online = 1 WHERE id = :my_id';
                $res = $db->prepare($sql);
                $res->bindParam(':my_id',$id,PDO::PARAM_INT);
                $res->execute();

                if(isset($_COOKIE['PHPSESSID'])){
                    session_destroy();
                }
                session_start();
                $_SESSION['admin_id'] = $id;

                http_response_code(200);
                exit;
            }else{
                http_response_code(500);
                exit;
            }
        }else{
            http_response_code(500);
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