<?php
$parentAlbumId = $_POST["parentAlbumId"];
foreach( $_POST['image'] as $order => $id_photo ){
    $order = $order + 1;
    $update_sql_string = 'UPDATE imagestoalbums SET positionInAlbum="' . $order . '" WHERE imageId="' . $id_photo . '" AND albumId="'. $parentAlbumId . '" ';
    $db->query($update_sql_string);
}