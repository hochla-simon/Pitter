<?php
    include('albumFunctions.php');

	$site['title'] = 'Move album';
	$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
	$albumId=$_GET['id'];

	if (isset ($_POST["Save"])) {
		if ($_POST["albumId"] != '') {
			if (checkNoSon($_POST["albumId"], $_POST["parentAlbumId"], $db)) {
				$update_sql_string = 'UPDATE albums SET parentAlbumId="' . $_POST["parentAlbumId"] . '",modified=CURRENT_TIMESTAMP() WHERE id="' . $_POST["albumId"] . '" ';
				$db->query($update_sql_string);
				header('Location: ./index.html');
				exit();
			} else {
				$message = createMessage("Sorry, you cannot move a folder into a child folder.");

			}
		} else {
			http_response_code(500);
			$db->query($delete_sql_string);
			$message = createMessage("Sorry, there was an error moving your album.");
		}
	}
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}
	print $message;
?>

<form action="" method="POST">
		
		<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >
		
		<label for="parentAlbumId">Where do you want to move the album <?php echo $album['name'];?> : </label>

		<select name="parentAlbumId" id="parentAlbumId">
		<?php
			echo obtainSelectAlbum ($db);
		?>
		</select>


		<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input type="submit" name="Save" value="Save">
		
</form>
