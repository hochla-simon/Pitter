<?php
$id = $_GET['id'];

$sql = "SELECT id, extension FROM images WHERE id=" . $id;
$result = $db->query($sql);

if (!empty($result)) {
    $row = mysql_fetch_array($result);
    $id = $row['id'];
    $extension = $row['extension'];
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