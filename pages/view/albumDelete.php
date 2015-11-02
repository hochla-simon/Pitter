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
?>

<form action="./albumDeleteBdd.html" method="POST">

	Do you want to delete the album : <?php echo $album['name']; ?>
	
	<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >
	
	<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
	<input type="submit" name="Delete" value="Delete">
</form>