<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:

    $id = $_GET['id'];

    $sql = "SELECT id, extension, ownerId FROM images WHERE id='".mysql_real_escape_string($id)."'";
    $result = $db->query($sql);
    $row = mysql_fetch_array($result);

    $albumSharedWithUsers = array();
    $select_sql_query = "SELECT userId FROM imagesToAlbums, usersToAlbums WHERE imagesToAlbums.albumId = usersToAlbums.albumId AND imagesToAlbums.imageId = " . mysql_real_escape_string($id);
    $result = $db->query($select_sql_query);
    if ($result != false) {
        while ($user = mysql_fetch_array($result)) {
            array_push($albumSharedWithUsers, $user["userId"]);
        }
    }

    if ($row) {
        $id = $row['id'];
        $extension = $row['extension'];
    } else {
        include(dirname(__FILE__) . '/../common/error404.php');
        if(!$phpunit['isTest']) {
            die();
        }
    }

    if($row['ownerId']!=$currentUser['id'] && !in_array($currentUser['id'], $albumSharedWithUsers)) {
        if(!$phpunit['isTest']) {
            include(dirname(__FILE__) . '/../common/error401.php');
            die();
        }
    }
    deliver_image_content($id, $extension, !$phpunit['isTest']);


if(!$phpunit['isTest']) {
    die();
}
endif;
?>