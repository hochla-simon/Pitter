<?php
    include('albumFunctions.php');
	
	$site['title'] = 'Copy album';
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
		<input type="text" name="parentAlbumId" id="parentAlbumId" size="60" value="" >
		
		<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input type="submit" name="Save" value="Save">
		
</form>