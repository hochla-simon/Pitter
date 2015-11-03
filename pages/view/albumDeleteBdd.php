<?php
	$site['title'] = 'Delete album';
	
	$delete_sql_string = 'DELETE FROM albums WHERE id="'.$_POST["albumId"].'" ';
	$db->query($delete_sql_string);
	$delete_sql_string = "DELETE FROM imagestoalbums WHERE albumId=". mysql_real_escape_string($albumId);
	$db->query($delete_sql_string);
	header('Location: ./index.html');
	exit();
?>