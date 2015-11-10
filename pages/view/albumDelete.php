<?php
	$site['title'] = 'Delete album';
	$albumId=$_GET['id'];
	
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}

	if (isset ($_POST["Save"])) {
		$delete_sql_string = 'DELETE FROM albums WHERE id="' . $_POST["albumId"] . '" ';
		$db->query($delete_sql_string);
		$delete_sql_string = "DELETE FROM imagestoalbums WHERE albumId=" . mysql_real_escape_string($albumId);
		$db->query($delete_sql_string);
		header('Location: ./index.html');
		exit();
	}
?>

<form action="" method="POST">

	Do you want to delete the album : <?php echo $album['name']; ?>
	
	<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >
	
	<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
	<input type="submit" name="Delete" value="Delete">
</form>