<?php
	if ($_POST["albumId"] != '') {
		$select_sql_string = 'SELECT name, description FROM albums WHERE id="'.$_POST["albumId"].'" ';
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		
			$insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("'.$_POST["parentAlbumId"].'", 0,"'.$album["name"].'", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "'.$album["description"].'" )';
			$db->query($insert_sql_string);
			$newAlbumId = mysql_insert_id();
	
			$select_sql_string = 'SELECT imageId FROM imagestoalbums WHERE albumID="'.$_POST["albumId"].'" ';
			$result = $db->query($select_sql_string);
			while($image = mysql_fetch_array($result)){
				$insert_sql_string = 'INSERT INTO imagestoalbums (albumId, imageID) VALUES ("'.$newAlbumId.'", "'.$image["imageId"].'" )';
				$db->query($insert_sql_string);
			}
		}
		header('Location: ./index.html');
		exit();
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        echo "Sorry, there was an error copying your album.";
    }
	die();
?>