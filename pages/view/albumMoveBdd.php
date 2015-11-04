<?php
    include('albumFunctions.php');
    if ($_POST["albumId"] != '') {
        if (checkNoSon ($_POST["albumId"], $_POST["parentAlbumId"], $db)){
			$update_sql_string = 'UPDATE albums SET parentAlbumId="'.$_POST["parentAlbumId"].'",modified=CURRENT_TIMESTAMP() WHERE id="'.$_POST["albumId"].'" ';
			$db->query($update_sql_string);

			header('Location: ./index.html');
			exit();
		}
        else{
            echo "Sorry, you cannot move a folder into a child folder.";
        }
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        echo "Sorry, there was an error moving your album.";
    }
	die();
?>