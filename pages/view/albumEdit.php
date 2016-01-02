<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:

	include_once(dirname(__FILE__).'/albumFunctions.php');

	$site['title'] = 'Edit album';
	$albumId=$_GET['id'];
	$accessDenied = false;

	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
			if ($album['ownerId'] != $currentUser['id']) {
				$denied = true;
				if(!$phpunit['isTest']) {
					include(dirname(__FILE__) . '/../common/error401.php');
					exit();
				}
				$accessDenied = true;
			}
		}
	}
	if (isset ($_POST["Save"])) {
		if ($_POST["name"] != '') {
			$update_sql_string = 'UPDATE albums SET name="' . $_POST["name"] . '",modified=CURRENT_TIMESTAMP(),description="' . $_POST["description"] . '" WHERE id="' . $_POST["albumId"] . '" ';

			$db->query($update_sql_string);

			if(!$phpunit['isTest']) {
				header('Location: ./index.html?id='.$_POST["albumId"]);
				exit();
			}
		} else {
			http_response_code(500);
			$db->query($delete_sql_string);
			$message  = createMessage("Sorry, there was an error editing your album.");
		}
	}
	if (!$phpunit['isTest']) {
		print ($message);
		?>
		<h2><?php echo $site['title']; ?></h2>

		<form action="" method="POST">
		
			<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >

			<div class="row">
				<label for="path">Path :</label>
				<input type="text" name="path" id="path" size="60" disabled value="<?php echo get_path($album['parentAlbumId'], $db);?>" >
			</div>

			<div class="row">
				<label for="name">Name :</label>
				<input type="text" name="name" id="name" size="60" value="<?php echo $album['name']; ?>">
			</div>

			<div class="row">
				<label for="description">Description :</label>
				<textarea name="description" id="description" cols="60" rows="5"><?php echo $album['description']; ?></textarea>
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