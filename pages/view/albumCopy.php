<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:
    include('albumFunctions.php');
	
	$site['title'] = 'Copy album';
	$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
	$albumId=$_GET['id'];

	if(isset($_POST["Save"])) {
		if ($_POST["albumId"] != '') {
			$select_sql_string = 'SELECT name, description FROM albums WHERE id="' . $_POST["albumId"] . '" ';
			$result = $db->query($select_sql_string);
			if (!empty($result)) {
				$album = mysql_fetch_array($result);

				$insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("' . $_POST["parentAlbumId"] . '", 0,"' . $album["name"] . '", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "' . $album["description"] . '" )';
				$db->query($insert_sql_string);
				$newAlbumId = mysql_insert_id();

				$select_sql_string = 'SELECT imageId FROM imagestoalbums WHERE albumID="' . $_POST["albumId"] . '" ';
				$result = $db->query($select_sql_string);
				while ($image = mysql_fetch_array($result)) {
					$insert_sql_string = 'INSERT INTO imagestoalbums (albumId, imageID) VALUES ("' . $newAlbumId . '", "' . $image["imageId"] . '" )';
					$db->query($insert_sql_string);
				}
			}
			header('Location: ./index.html');
			exit();
		} else {
			http_response_code(500);
			$db->query($delete_sql_string);
			$message = createMessage( "Sorry, there was an error copying your album." );
		}
	}
	print($message);
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}
?>
<h2><?php echo $site['title'];?> to...</h2>

<form action="" method="POST">
		
		<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >

		<div class="row">
			<label>Album to copy:</label>
			<p><?php echo $album['name'];?></p>

		</div>

		<div class="row">
			<label for="parentAlbumId">Destination:</label>
			<select name="parentAlbumId" id="parentAlbumId">
				<?php
					echo obtainSelectAlbum ($db, $currentUser['id']);
				?>
			</select>
		</div>
		<div class="row">
			<input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
			<input class="submit" type="submit" name="Save" value="Save">
		</div>

		
</form>
<?php
endif;
?>