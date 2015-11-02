<?php
    if ($_POST["name"] != '') {
        $insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("'.$_POST["parentAlbumId"].'", 0,"'.$_POST["name"].'", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "'.$_POST["description"].'" )';
        $db->query($insert_sql_string);
		
		header('Location: ./index.html');
		exit();
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        echo "Sorry, there was an error creating your album.";
    }
	die();
?>