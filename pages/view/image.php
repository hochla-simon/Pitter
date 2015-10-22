<?php
$id = $_GET['id'];

$sql = "SELECT id, imageFormat FROM Pictures WHERE id=" . $id;
$result = $dbConnection->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row["id"];
    $extension = $row["imageFormat"];
} else {
    http_response_code(404);
    die();
}

$path = dirname(__FILE__) . '/../../data/images/' . $id . '.' . $extension;

if($extension != 'jpg' && $extension != 'JPG' && $extension != 'png' && $extension != 'PNG'  && $extension != 'jpeg' &&
    $extension != 'JPEG' && $extension != 'gif' && $extension != 'GIF') {
    http_response_code(415);
} else {
    $extension = strtolower($extension);
    if ($extension == 'jpg') {
        $extension = "jpeg";
    }
    header('Content-Type: image/' . $extension);
    readfile($path);
}
die();
?>