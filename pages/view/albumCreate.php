<?php
    include('albumFunctions.php');
	
	$site['title'] = 'Add new album';
	$parentAlbumId=$_GET['parentId'];
	if (!$parentAlbumId) {
		$parentAlbumId = -1;
	}
?>


<form action="./albumAddBdd.html" method="POST">
		
		<input type="hidden" name="parentAlbumId" id="parentAlbumId" value="<?php echo $parentAlbumId; ?>" >
		
		<label for="path">Path :</label>
		<input type="text" name="path" id="path" size="60" disabled value="<?php echo $path = get_path($parentAlbumId, $db); ?>" >
		
		<label for="name">Name :</label>
		<input type="text" name="name" id="name" size="60" value="">
		
		<label for="description">Description :</label>
		<textarea name="description" id="description" cols="60" rows="5">  </textarea>
		
		<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input type="submit" name="Save" value="Save">
		
</form>