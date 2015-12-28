<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:
	$site['title'] = 'Share album';
	$albumId = $_GET['albumId'];

	if (isset ($_POST["Save"])) {
		if ($_POST["albumId"] != '' && $_POST["userId"] != '') {
			$insert_sql_string = "INSERT INTO usersToAlbums (albumId, userId) VALUES (" . mysql_real_escape_string($_POST["albumId"]) . "," . mysql_real_escape_string($_POST["userId"]) . ")";
			$result = $db->query($insert_sql_string);
			if ($result) {
				echo createMessage('Album has been shared successfully!', 'confirm');
			} else {
				echo createMessage('Error has occured when sharing the album!', 'error');
			}
		}
	}

	if($albumId != ''){
		$select_sql_string = "SELECT name FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}

		$albumSharedWithUsers = array();
		$select_sql_string = "SELECT userId FROM usersToAlbums WHERE albumId=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if ($result != false) {
			while ($user = mysql_fetch_array($result)) {
				array_push($albumSharedWithUsers, $user["userId"]);
			}
		}
	}
	$select_sql_string = "SELECT id, email FROM users";
	$users = $db->query($select_sql_string);
	if (!$phpunit['isTest']) {
		print ($message);
?>
		<h2><?php echo $site['title']; ?></h2>

		<form action="" method="POST">

			<input type="hidden" name="albumId" id="albumId" value="<?php echo $albumId ?>">

			<div class="row">
				<label>Album to share:</label>
				<p><?php echo $album['name']; ?></p>
			</div>

			<div class="row">
				<label for="userId">User to share it to:</label>

				<select name="userId" id="userId">
					<?php 
						while($user = mysql_fetch_array($users)) {
							if ($user['id'] != $currentUser['id'] && !in_array($user["id"], $albumSharedWithUsers))
							echo '<option value="' . $user['id'] . '">' . $user['email'] . '</option>';
						}
					?>
				</select>
			</div>

			<div class="row">
				<input class="cancel" type="button" name="Cancel" value="Cancel"
					   onclick="history.back();">
				<input class="submit" type="submit" name="Save" value="Share">
			</div>

		</form>
<?php
	}
endif;
?>