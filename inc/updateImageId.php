<?php

$imageId = $_POST["imageId"];

# Update imageId in db.json
$db = file_get_contents('db.json');
$db = json_decode($db, true);
$db['imageId'] = (int)$imageId;

unlink('db.json');
file_put_contents('db.json', json_encode($db));

header('Content-Type: application/json');
echo '{"msg": "done"}';

?>

