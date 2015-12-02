<?php
if($currentUser['id'] == ''):
    echo "Unauthorized.";
else:
$path = $_POST["path"];
$imageId = $_POST["imageId"];
$albumId = $_POST["albumId"];
$newAlbumId = $_POST["newAlbumId"];

$denied = false;
$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
$result = $db->query($select_sql_string);
if (!empty($result)){
    $album = mysql_fetch_array($result);
    if ($album['ownerId'] != $currentUser['id']) {
        echo 'fuck1';
        $denied = true;
    }
}else{
    $denied = true;
}
$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($newAlbumId);
$result = $db->query($select_sql_string);
if (!empty($result)){
    $album = mysql_fetch_array($result);
    if ($album['ownerId'] != $currentUser['id']) {
        $denied = true;
    }
}else{
    $denied = true;
}
$sql = "SELECT id,ownerId FROM images WHERE id='" . mysql_real_escape_string($imageId)."'";
$result = $db->query($sql);
$row = mysql_fetch_array($result);
if (!empty($row)) {
    if($row['ownerId']!=$currentUser['id']) {
        $denied = true;
    }
}else{
    $denied = true;
}

if($denied and !$phpunit['isTest']){
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}

$sql = "SELECT positionInAlbum FROM imagesToAlbums WHERE albumId = '" . mysql_real_escape_string($newAlbumId) . "' ORDER BY positionInAlbum DESC LIMIT 1";
$currentMaxPosition = $db->query($sql);
$newMaxPosition = (int) mysql_fetch_array($currentMaxPosition)["positionInAlbum"] + 1;

$sql = "UPDATE imagesToAlbums SET albumId = " . mysql_real_escape_string($newAlbumId) . ", positionInAlbum = " . mysql_real_escape_string($newMaxPosition) . " WHERE imageId = " . mysql_real_escape_string($imageId) . " AND albumId = " . mysql_real_escape_string($albumId);
$db->query($sql);
endif;
if(!$phpunit['isTest']) {
    die();
}
?>
