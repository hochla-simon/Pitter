<?php
$id = $_GET['id'];

$sql = "SELECT id, extension FROM images WHERE id=" . mysql_real_escape_string($id);
$result = $db->query($sql);
$row = mysql_fetch_array($result);

if ($row) {
    $id = $row['id'];
    $extension = $row['extension'];
} else {
    include(dirname(__FILE__) . '/../common/error404.php');
    die();
}

$path = dirname(__FILE__) . '/../../data/images/' . $id . '.' . $extension;

$extension = strtolower($extension);
if ($extension == 'jpg') {
    $extension = "jpeg";
}
header('Content-Type: image/' . $extension);
readfile($path);

die();
?>