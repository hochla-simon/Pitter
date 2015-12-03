<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:
	include_once(dirname(__FILE__).'/albumFunctions.php');



	$site['title'] = 'Copy album';
	$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
	$albumId=$_GET['id'];

	$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
	$result = $db->query($select_sql_string);
	if (!empty($result)){
		$album = mysql_fetch_array($result);
		if ($album['ownerId'] != $currentUser['id']) {
			if(!$phpunit['isTest']) {
				include(dirname(__FILE__) . '/../common/error401.php');
				exit();
			}
		}
	}else{
		if(!$phpunit['isTest']) {
			include(dirname(__FILE__) . '/../common/error401.php');
			exit();
		}
	}


	if(isset($_POST["Save"])) {
		if ($_POST["albumId"] != '') {
			$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($_POST["parentAlbumId"]);
			$result = $db->query($select_sql_string);
			if (!empty($result)){
				$album = mysql_fetch_array($result);
				if ($album['ownerId'] != $currentUser['id']) {
					include(dirname(__FILE__) . '/../common/error401.php');
					exit();
				}
			}else{
				include(dirname(__FILE__) . '/../common/error401.php');
				exit();
			}


			copyAlbum($db, $albumId, $_POST["parentAlbumId"]);
			if ( !$phpunit['isTest'] ) {
				header('Location: ./index.html');
				exit();
			}
		} else {
			http_response_code(500);
			$message = createMessage( "Sorry, there was an error copying your album." );
		}
	}
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}
	if ( !$phpunit['isTest'] ) {
		print($message);
		?>
		<h2><?php echo $site['title']; ?> to...</h2>

		<form action="" method="POST">

			<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>">

			<div class="row">
				<label>Album to copy:</label>

				<p><?php echo $album['name']; ?></p>
			</div>

			<div class="row">
				<label for="parentAlbumId">Destination:</label>
				<select name="parentAlbumId" id="parentAlbumId">
					<?php
					echo obtainSelectAlbum($db, $currentUser['id'], $currentUser['id']);
					?>
				</select>
			</div>
			<div class="row">
				<input class="cancel" type="button" name="Cancel" value="Cancel"
					   onclick="window.location='./index.html';">
				<input class="submit" type="submit" name="Save" value="Save">
			</div>


		</form>
		<?php
	}
endif;
?>