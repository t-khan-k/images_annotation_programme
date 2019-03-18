<?php

$db = file_get_contents("db.json");
$db = json_decode($db, true);

$data = array ("imageId" => $db['imageId'], "totalImagesCount" => $db['totalImagesCount']);

header('Content-Type: application/json');
echo json_encode($data);
?>