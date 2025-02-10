<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);  
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path == '/rooms_description') {
        $db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
        if (!$db) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }

        $sql = "SELECT * FROM room_type";
        $res = $db->query($sql);
		$room_type = $res->fetchAll(PDO::FETCH_ASSOC);
		foreach($room_type as $key => &$value){
			$value['photo'] = [];
		}
		
		$sql = "SELECT rt.id, rp.photo FROM room_type rt JOIN room_photo rp ON rt.id = rp.id_room_type";
		$res = $db->query($sql);
		$room_photo = $res->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($room_photo as $key => &$value){
			array_push($room_type[$value['id']-1]['photo'],$value['photo']);
		}

        echo json_encode($room_type);
		
    }else if($path == '/additional_services'){
		$db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
		if (!$db) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
		$sql = "SELECT * FROM additional_services";
        $res = $db->query($sql);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
		if($rows){
			http_response_code(200);
			echo json_encode($rows);
		}else{
			http_response_code(404);
		}
		
	}else if($path == '/review'){
		$db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
		if (!$db) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
		$sql = "SELECT pod.fio, pod.star, r2.description FROM	
				(SELECT MIN(r.id) as id, k.fio, MAX(r.star) as star FROM review r 
					JOIN booked_log bl
						ON r.id = bl.id
					JOIN klient k
						ON bl.id_klient = k.id
					GROUP BY k.fio) pod
				JOIN review r2
				ON pod.id = r2.id
				ORDER BY pod.star DESC 
				LIMIT 3";
		$res = $db->query($sql);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
		if($rows){
			http_response_code(200);
			echo json_encode($rows);
		}else{
			http_response_code(404);
		}
	}
	
}else if($method == 'POST'){
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	if($path == '/signup'){
		try{
			$testPhone = "/^\+(7|3)\d{10}$/";
			$emailTest = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
			$fioTest = '/^[А-ЯЁ][а-яё]{1,19}(?:-[А-ЯЁ][а-яё]{1,19})?\s+[А-ЯЁ][а-яё]{1,19}\s+[А-ЯЁ][а-яё]{1,19}$/u';
			$data = json_decode(file_get_contents('php://input'), true);
			
			if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Неверный формат JSON']);
                exit;
            }

            $email = $data['email'] ?? '';
            $phone = $data['phone'] ?? '';
            $password = $data['password'] ?? '';
            $fio = $data['fio'] ?? '';
			
			if (empty($phone) || !preg_match($testPhone, $phone)) {
				http_response_code(400);
				echo json_encode(['Неправильный или пустой телефон']);
				exit;
			} 
			
			if (empty($email) || !preg_match($emailTest, $email)) {
				http_response_code(400);
				echo json_encode(['Неправильный или пустой email']);
				exit;
			}
			
			if(empty($password) || strlen($password)<3){
				http_response_code(400);
				echo json_encode(['Пустой пароль или короче трех символов']);
				exit;
			}
			
			if(empty($fio) || !preg_match($fioTest,$fio)){
				http_response_code(400);
				echo json_encode(['Пустой фио или неккоректный']);
				exit;
			}
			
			$db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
			if (!$db) {
				http_response_code(500);
				echo json_encode(['Database connection failed']);
				exit;
			}
			
			$sql = "SELECT id FROM klient WHERE email = :email";
			$res = $db->prepare($sql);
			$res->bindParam(':email',$email,PDO::PARAM_STR);
			$res->execute();
			
			$temp = $res->fetchAll(PDO::FETCH_ASSOC);
			if($temp){
				http_response_code(500);
				echo json_encode(['Пользователь с таким email уже существует']);
				exit;
			}
			
			$hash = password_hash($password, PASSWORD_DEFAULT);
			
			$sql = "INSERT INTO klient (id,fio,email,telephone,password_hash) VALUES (nextval('klient_sq'),:fio,:email,:telephone,:password_hash)";
			
			$res = $db->prepare($sql);
			$res->bindParam(':fio', $fio, PDO::PARAM_STR);
			$res->bindParam(':email', $email, PDO::PARAM_STR);
			$res->bindParam(':telephone', $phone, PDO::PARAM_STR);
			$res->bindParam(':password_hash', $hash, PDO::PARAM_STR);
			
			$res->execute();
			
			http_response_code(200);
			echo json_encode(['Пользователь успешно добавлен']);
		}
		catch (PDOException $e) {
			echo "Ошибка: " . $e->getMessage();
		}
	}else{
		http_response_code(400);
		exit;
	}
}else{
	http_response_code(404);
	exit;
}

?>
