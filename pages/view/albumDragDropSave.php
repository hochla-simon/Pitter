<?php
if($currentUser['id'] == ''):
    echo "Unauthorized.";
else:
    $select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($_POST["albumId"]);
    $result = $db->query($select_sql_string);
    if (!empty($result)){
        $album = mysql_fetch_array($result);
        if ($album['ownerId'] != $currentUser['id']) {
            $denied = true;
        }
    }else{
        $denied = true;
    }
    $select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($_POST["parentAlbumId"]);
    $result = $db->query($select_sql_string);
    if (!empty($result)){
        $album = mysql_fetch_array($result);
        if ($album['ownerId'] != $currentUser['id']) {
            $denied = true;
        }
    }else{
        $denied = true;
    }

    if($denied){
        include(dirname(__FILE__) . '/../common/error401.php');
        exit();
    }else {
        if (isset($_POST["albumId"]) && isset($_POST["parentAlbumId"])) {
            $update_sql_string = 'UPDATE albums SET parentAlbumId="' . $_POST["parentAlbumId"] . '",modified=CURRENT_TIMESTAMP() WHERE id="' . $_POST["albumId"] . '" ';
            $db->query($update_sql_string);
        } else {
            http_response_code(500);
            $db->query($delete_sql_string);
            $message = createMessage("Sorry, there was an error moving your album.");
        }
        die();
   }
endif;
if(!$phpunit['isTest']) {
    die();
}
?>