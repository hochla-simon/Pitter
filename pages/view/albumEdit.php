<?php
    include('albumFunctions.php');
	
	$site['title'] = 'Edit album';
	$albumId=$_GET['id'];
	
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}
?>

<form action="./albumEditBdd.html" method="POST">
		
		<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >
		
		<label for="path">Path :</label>
		<input type="text" name="path" id="path" size="60" disabled value="<?php echo get_path($album['parentAlbumId'], $db);?>" >
		
		<label for="name">Name :</label>
		<input type="text" name="name" id="name" size="60" value="<?php echo $album['name']; ?>">
		
		<label for="description">Description :</label>
		<textarea name="description" id="description" cols="60" rows="5"> <?php echo $album['description']; ?> </textarea>
		
		<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input type="submit" name="Save" value="Save">
		
</form>