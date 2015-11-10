<?php
    include('albumFunctions.php');

	$site['title'] = 'Move album';
	$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
	$albumId=$_GET['id'];
	
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}
?>

<form action="./albumMoveBdd.html" method="POST">
		
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
