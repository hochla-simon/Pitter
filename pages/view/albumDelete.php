<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:

	$site['title'] = 'Delete album';
	$albumId=$_GET['id'];
	
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, ownerId, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
			if ($album['ownerId'] != $currentUser['id']) {
				$denied = true;
				include(dirname(__FILE__) . '/../common/error401.php');
				exit();
			}
		}
	}

	if (isset ($_POST["Delete"])) {
		$delete_sql_string = 'DELETE FROM albums WHERE id="' . $_POST["albumId"] . '" ';
		$db->query($delete_sql_string);
		$delete_sql_string = "DELETE FROM imagestoalbums WHERE albumId=" . mysql_real_escape_string($albumId);
		$db->query($delete_sql_string);
		header('Location: ./index.html');
		exit();
	}
?>

<h2><?php echo $site['title'];?></h2>

<form action="" method="POST">

	<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >

	<div class="row">
		<label>Album to delete:</label>
		<p><?php echo $album['name']; ?></p>
	</div>
	
	<div class="row">
		<input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input class="submit" type="submit" name="Delete" value="Delete">
	</div>
</form>
<?php
endif;
?>
