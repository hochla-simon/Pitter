<?php
$albumId = $_GET['id'];
if (!$albumId) {
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}
$query_for_album = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($albumId) . "'";
$album_data = mysql_fetch_array($db->query($query_for_album));
if (!empty($album_data)) {
    if ($album_data['ownerId'] != $currentUser['id']) {
        include(dirname(__FILE__) . '/../common/error401.php');
    }else{
        $sql = "SELECT * FROM `linkToAlbums` WHERE `albumId` = ".$albumId.";";
        $result = $db->query($sql);
        $row = mysql_fetch_array($result);
        if(empty($row)){
            $link="";
            $iter=0;
            $linkValid = false;
            do{
                $link=base64_encode(crypt($albumId+$iter, strval(rand(0, 1048576))));
                $sql = "SELECT * FROM `linkToAlbums` WHERE  `link` = '".mysql_real_escape_string($link)."'";
                $result = $db->query($sql);
                $row = mysql_fetch_array($result);
                if(empty($row)) {
                    $linkValid = true;
                }

            }while(!$linkValid);
            $sqlInsertQuery = 'INSERT INTO linkToAlbums (albumId,link) VALUES (' . $albumId . ',"' . mysql_real_escape_string($link) . '");';
            $db->query($sqlInsertQuery);
            if (!$phpunit['isTest']) {
                echo $link;
            }
        }else{
            if (!$phpunit['isTest']) {
                echo $row['link'];
            }
        }
    }
}
if (!$phpunit['isTest']) {
    die();
}