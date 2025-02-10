<?php
$name = 'klient';
$db = new PDO("pgsql:host=localhost;port=5432;dbname=mini_hotel", "admin", "admin");
$sql = 'SELECT * FROM '.$name;
echo $sql;
$res = $db->prepare($sql);
$res->execute();
$row = $res->fetchAll(PDO::FETCH_ASSOC);

print_r($row);

$columns = array_keys($row[0] ?? []);
$result = [
    'columns' => $columns,
    'data' => $row,
];

echo json_encode($result);
