<?php
$sharelink = $_GET['sharelink'];
$id = $_GET['id'];


if(!isset($sharelink)){
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}

$sql = "SELECT * FROM `linkToAlbums` WHERE `link` = '".$sharelink."'";
$result = $db->query($sql);
$row = mysql_fetch_array($result);
if(empty($row)){
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}else{
    $albumId = $row['albumId'];
    $sql = "SELECT * FROM `imagesToAlbums` WHERE `albumId` = ".$albumId." AND `imageId` = ".$id;
    $result = $db->query($sql);
    $row = mysql_fetch_array($result);
    if(empty($row)){
        include(dirname(__FILE__) . '/../common/error401.php');
        exit();
    }
}

$sql = "SELECT id, extension, ownerId FROM images WHERE id='".mysql_real_escape_string($id)."'";
$result = $db->query($sql);
$row = mysql_fetch_array($result);

if ($row) {
    $id = $row['id'];
    $extension = $row['extension'];
} else {
    include(dirname(__FILE__) . '/../common/error404.php');
    if(!$phpunit['isTest']) {
        die();
    }
}

deliver_image_content($id, $extension, !$phpunit['isTest']);


if(!$phpunit['isTest']) {
    die();
}
