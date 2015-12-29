<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:

	include_once(dirname(__FILE__).'/albumFunctions.php');

	$site['title'] = 'Move album';
	$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
	$albumId=$_GET['id'];


	$denied = false;
	$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($_POST["albumId"]);
	$result = $db->query($select_sql_string);
	if (!empty($result)){
		$album = mysql_fetch_array($result);
		if ($album['ownerId'] != $currentUser['id']) {
			$denied = true;
		}
	}else{
		$denied = true;
	}
	if (isset ($_POST["Save"])) {
		if ($_POST["parentAlbumId"] != '') {
			$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($_POST["parentAlbumId"]);
			$result = $db->query($select_sql_string);
			if (!empty($result)) {
				$album = mysql_fetch_array($result);
				if ($album['ownerId'] != $currentUser['id']) {
					$denied = true;
				}
			} else {
				$denied = true;
			}

			if ($denied) {
				if(!$phpunit['isTest']) {
					include(dirname(__FILE__) . '/../common/error401.php');
					exit();
				}
			}
		}
	}
	if (isset ($_POST["Save"])) {
		if ($_POST["albumId"] != '') {
			if (checkNoSon($_POST["albumId"], $_POST["parentAlbumId"], $db)) {
				$update_sql_string = 'UPDATE albums SET parentAlbumId="' . $_POST["parentAlbumId"] . '",modified=CURRENT_TIMESTAMP() WHERE id="' . $_POST["albumId"] . '" ';
				$db->query($update_sql_string);
				if (!$phpunit['isTest']) {
					header('Location: ./index.html?id='.$_POST["albumId"]);
					exit();
				}
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
	if (!$phpunit['isTest']) {
		print ($message);
		?>
		<h2><?php echo $site['title']; ?> to...</h2>

		<form action="" method="POST">

			<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>">

			<div class="row">
				<label>Album to move:</label>
				<p><?php echo $album['name']; ?></p>
			</div>

			<div class="row">
				<label for="parentAlbumId">Destination:</label>

				<select name="parentAlbumId" id="parentAlbumId">
					<?php
					echo obtainSelectAlbum($db, $currentUser['id'], $album['id']);
					?>
				</select>
			</div>

			<div class="row">
				<input class="cancel" type="button" name="Cancel" value="Cancel"
					   onclick="history.back();">
				<input class="submit" type="submit" name="Save" value="Save">
			</div>

		</form>
<?php
	}
endif;
?>