<?php
header('Access-Control-Allow-Origin: http://localhost:8082');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Credentials: true');

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if($path == '/log_in.php'){
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $password = $data['password'];

        $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
        $sql = 'SELECT id,password_hash FROM klient WHERE email = :my_email';
        $res = $pdo->prepare($sql);
        $res->bindParam(':my_email',$email,PDO::PARAM_STR);
        $res->execute();

        $temp = $res->fetchAll(PDO::FETCH_ASSOC);

        if($temp && password_verify($password,$temp[0]['password_hash'])){
            if (!isset($_COOKIE['PHPSESSID'])) {
                session_start();
                $_SESSION['user_id'] = $temp[0]['id'];
            }else{
                http_response_code(403);
                exit;
            }

            http_response_code(200);
        }else{
            http_response_code(401);
        }
    }
}

?>