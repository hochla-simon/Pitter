<?php
$parentAlbumId = $_POST["parentAlbumId"];
foreach( $_POST['image'] as $order => $id_photo ){
    $order = $order + 1;
    $update_sql_string = "UPDATE `pitter`.`imagesToAlbums` SET `positionInAlbum` = '".$order."' WHERE `imagesToAlbums`.`albumId` = ".$parentAlbumId." AND `imagesToAlbums`.`imageId` =".$id_photo.";";
    echo $update_sql_string;
    $db->query($update_sql_string);
}

die();