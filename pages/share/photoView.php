<?php

$sharelink = $_GET['sharelink'];
if(!isset($sharelink)){
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}
$id = $_GET['id'];
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

$site['title'] = 'View photo';


if ($id != '') {
    $sql = "SELECT id,ownerId FROM images WHERE id=" . mysql_real_escape_string($id);
    $result = $db->query($sql);
    $row = mysql_fetch_array($result);
    if ($row) {
        echo '<script src="' . $config['projectURL'] . '/js/photoViewScripts.js" type="text/javascript"></script>';
        echo '<img id="picView" src="image.html?id=' . $id . '&sharelink='.$sharelink.'" alt=""/>';
    } else {
        include(dirname(__FILE__) . '/../common/error404.php');
    }

} else {
    include(dirname(__FILE__) . '/../common/error404.php');
}
