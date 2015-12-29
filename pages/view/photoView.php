<?php

if(!function_exists('addMetadata')) {
    function addMetadata($id, $db) {
        $select_sql_query = "SELECT name, value FROM metadata WHERE imageId = " . mysql_real_escape_string($id);
        $result = $db->query($select_sql_query);
        if ($result != false) {
            while ($row = mysql_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' . $row['name'] . ':</td>';
                echo '<td>' . $row['value'] . '</td>';
                echo '</tr>';
            }
        }
    }
}

if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:
$site['title'] = 'View photo';

$id = $_GET['id'];
if ($id != '') {
    $sql = "SELECT id,ownerId,name,filename,extension FROM images WHERE id=" . mysql_real_escape_string($id);
    $result = $db->query($sql);
    $row = mysql_fetch_array($result);

    $albumSharedWithUsers = array();
    $select_sql_query = "SELECT userId FROM imagesToAlbums, usersToAlbums WHERE imagesToAlbums.albumId = usersToAlbums.albumId AND imagesToAlbums.imageId = " . mysql_real_escape_string($id);
    $result = $db->query($select_sql_query);
    while ($user = mysql_fetch_array($result)) {
        array_push($albumSharedWithUsers, $user["userId"]);
    }

    if ($row) {
        if($row['ownerId']==$currentUser['id'] || in_array($currentUser['id'], $albumSharedWithUsers)) {
            echo '<img id="back_button" src="' . $config['projectURL'] . '/images/back.png" alt="" onclick="history.go(-1)">';
            echo '<h2 id="photo_name">' . $row['name'] . '</h2>';
            echo '<script src="' . $config['projectURL'] . '/js/photoViewScripts.js" type="text/javascript"></script>';
            echo '<img id="picView" src="image.html?id=' . $id . '" alt=""/>';

            echo '<div>';
            echo '<table id="metadata">';
            echo '<tr>';
            echo '<th colspan="2">Metadata</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>Original filename:</td>';
            echo '<td>' . $row['filename'] . '.' . $row['extension'] . '</td>';
            echo '</tr>';
            addMetadata($id, $db);
            echo '</table>';
            echo '</div>';
        }else{
            include(dirname(__FILE__) . '/../common/error401.php');
        }
    } else {
        include(dirname(__FILE__) . '/../common/error404.php');
    }

} else {
    include(dirname(__FILE__) . '/../common/error404.php');
}
endif;
?>