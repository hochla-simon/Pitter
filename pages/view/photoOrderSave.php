<?php
if($currentUser['id'] == ''):
    echo "Unauthorized.";
else:
$parentAlbumId = $_POST["parentAlbumId"];
foreach((array)$_POST['image'] as $order => $id_photo ){
    $order = $order + 1;
    $update_sql_string = "UPDATE `pitter`.`imagesToAlbums` SET `positionInAlbum` = '".$order."' WHERE `imagesToAlbums`.`albumId` = ".$parentAlbumId." AND `imagesToAlbums`.`imageId` =".$id_photo.";";
    echo $update_sql_string;
    $db->query($update_sql_string);
}
endif;
if(!$phpunit['isTest']) {
    die();
}
?>