<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:

	include_once(dirname(__FILE__).'/albumFunctions.php');

	$site['title'] = 'Add new album';
	$accessDenied = false;

	if ($_GET['parentId']) {
		$parentAlbumId = $_GET['parentId'];
	}
	else{
		$parentAlbumId = 1;
	}

	$query_for_parent_album = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($parentAlbumId)."'";
	$parent_album = mysql_fetch_assoc($db->query($query_for_parent_album));
	if (!empty($parent_album)) {
		if ($parent_album['ownerId'] != $currentUser['id']) {
			if(!$phpunit['isTest']) {
				include(dirname(__FILE__) . '/../common/error401.php');
				exit();
			}
			$accessDenied = true;
		}
	}

	$message='';
	if (isset ($_POST["Save"])) {
		if ($_POST["name"] != '') {


			$insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("' . $_POST["parentAlbumId"] . '", ' . $currentUser['id'] . ',"' . $_POST["name"] . '", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "' . $_POST["description"] . '" )';
			$db->query($insert_sql_string);

			if (!isset ($phpunit['isTest'])) {
				header('Location: ./index.html?id='.mysql_insert_id());
				exit();
			}

		} else {
			http_response_code(500);
			$message = createMessage( "Sorry, you cannot create an album with an empty name." );
		}
	}
	if (!$phpunit['isTest']) {
		print ($message);
	?>
		<h2><?php echo $site['title'];?></h2>

		<form action="" method="POST">

			<input type="hidden" name="parentAlbumId" id="parentAlbumId" value="<?php echo $parentAlbumId; ?>">

			<div class="row">
				<label for="path">Path :</label>
				<input type="text" name="path" id="path" size="60" disabled
					   value="<?php echo $path = get_path($parentAlbumId, $db); ?>">
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
				<input class="cancel" type="button" name="Cancel" value="Cancel" onclick="history.back();">
				<input class="submit" type="submit" name="Save" value="Save">
			</div>

		</form>
	<?php
	}
endif;
?>