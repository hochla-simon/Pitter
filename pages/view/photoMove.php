<?php
if($currentUser['id'] == ''):
    echo "Unauthorized.";
else:
$path = $_POST["path"];
$imageId = $_POST["imageId"];
$albumId = $_POST["albumId"];
$newAlbumId = $_POST["newAlbumId"];

$sql = "SELECT positionInAlbum FROM imagesToAlbums WHERE albumId = " . mysql_real_escape_string($newAlbumId) . " ORDER BY positionInAlbum DESC LIMIT 1";
$currentMaxPosition = $db->query($sql);
$newMaxPosition = (int) mysql_fetch_array($currentMaxPosition)["positionInAlbum"] + 1;

$sql = "UPDATE imagesToAlbums SET albumId = " . mysql_real_escape_string($newAlbumId) . ", positionInAlbum = " . mysql_real_escape_string($newMaxPosition) . " WHERE imageId = " . mysql_real_escape_string($imageId) . " AND albumId = " . mysql_real_escape_string($albumId);
$db->query($sql);
endif;
if(!$phpunit['isTest']) {
    die();
}
?>