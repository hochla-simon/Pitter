<?php
    include('albumFunctions.php');
	
	$site['title'] = 'Add new album';
	$parentAlbumId=$_GET['parentId'];

	if (!$parentAlbumId) {
		$parentAlbumId = -1;
	}

	if (isset ($_POST["Save"])) {
		if ($_POST["name"] != '') {
			$insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("' . $_POST["parentAlbumId"] . '", 0,"' . $_POST["name"] . '", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "' . $_POST["description"] . '" )';
			$db->query($insert_sql_string);

			header('Location: ./index.html');
			exit();
		} else {
			http_response_code(500);
			$db->query($delete_sql_string);
			$message = createMessage( "Sorry, there was an error creating your album." );
		}
	}
	print ($message);
?>
<h2><?php echo $site['title'];?></h2>

<form action="" method="POST">
		
	<input type="hidden" name="parentAlbumId" id="parentAlbumId" value="<?php echo $parentAlbumId; ?>" >

	<div class="row">
		<label for="path">Path :</label>
		<input type="text" name="path" id="path" size="60" disabled value="<?php echo $path = get_path($parentAlbumId, $db); ?>" >
	</div>

	<div class="row">
		<label for="name">Name :</label>
		<input type="text" name="name" id="name" size="60" value="">
	</div>

	<div class="row">
		<label for="description">Description :</label>
		<textarea name="description" id="description" cols="60" rows="5"></textarea>
	</div>

	<div class="row">
		<input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input class="submit" type="submit" name="Save" value="Save">
	</div>

</form>