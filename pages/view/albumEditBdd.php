<?php
    if ($_POST["name"] != '') {
        $update_sql_string = 'UPDATE albums SET name="'.$_POST["name"].'",modified=CURRENT_TIMESTAMP(),description="'.$_POST["description"].'" WHERE id="'.$_POST["albumId"].'" ';
		
        $db->query($update_sql_string);

		header('Location: ./index.html');
		exit();
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        echo "Sorry, there was an error editing your album.";
    }
	die();
?>