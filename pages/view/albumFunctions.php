<?php
if (!function_exists(get_path)) {
	function get_path($firstParentAlbumId, $db)
	{
		$path = '';
		if ($firstParentAlbumId != -1) {
			$parentAlbumId = $firstParentAlbumId;
			while ($parentAlbumId != -1) {
				$select_sql_string = "SELECT parentAlbumId, name FROM albums WHERE id=" . mysql_real_escape_string($parentAlbumId);
				$result = $db->query($select_sql_string);
				if (!empty($result)) {
					$parentAlbum = mysql_fetch_array($result);
					$path = $parentAlbum['name'] . "/" . $path;
					$parentAlbumId = $parentAlbum['parentAlbumId'];
				} else {
					$path = "/";
					$parentAlbumId = 1;
				}
			}
		}

						return $path;
	}
}
if (!function_exists(checkNoSon)) {
	function checkNoSon ($albumId, $parentAlbumId, $db){
		while ($parentAlbumId != -1 && $parentAlbumId != $albumId){
			$select_sql_string = "SELECT parentAlbumId FROM albums WHERE id=" . $parentAlbumId;
			$result = $db->query($select_sql_string);
			if (!empty($result)){
				$parentAlbum = mysql_fetch_array($result);
				$parentAlbumId = $parentAlbum['parentAlbumId'];
			}
			else{
				return false;
			}
		}
		if ($parentAlbumId == -1){
			return true;
		}
		else{
			return false;
		}
	}
}

if (!function_exists(orderAlbums)) {
	function orderAlbums($id, &$children, $albumObjects)
	{
		foreach ($albumObjects as $albumId => $album) {
			if ($album['parentAlbumId'] == $id) {
				$children[$albumId] = $album;
				orderAlbums($albumId, $children[$albumId]['childAlbums'], $albumObjects);
			}
		}
	}
}
if (!function_exists(writeSelectAlbum)) {
	function writeSelectAlbum($albums, $subNumber, $parentId, &$selectAlbum)
	{
		foreach ($albums as $albumId => $album) {
			$selectAlbum .= '<option value="' . $albumId . '" >' . str_repeat("&nbsp", $subNumber * 3) . $album[name] . '</option>';
			writeSelectAlbum($album['childAlbums'], $subNumber + 1, $albumId, $selectAlbum);
		}
	}
}
if (!function_exists(obtainSelectAlbum)) {
	function obtainSelectAlbum($db, $currentUserId)
	{
		$sql = "SELECT parentAlbumId, id, name FROM albums WHERE ownerID=".$currentUserId;
		$albums = $db->query($sql);
		$selectAlbum = '';
		if (!empty($albums)) {

			$albumObjects = array();
			while ($row = mysql_fetch_array($albums)) {
				$albumObject = array(
					'name' => $row['name'],
					'parentAlbumId' => $row['parentAlbumId'],
					'childAlbums' => array()
				);
				$albumObjects[$row['id']] = $albumObject;
			}

			$orderedAlbumObjects = array();

			orderAlbums('-1', $orderedAlbumObjects, $albumObjects);

			writeSelectAlbum($orderedAlbumObjects, 0, '-1', $selectAlbum);
		}

		return $selectAlbum;
	}
}

if (!function_exists(deleteImage)) {
	function deleteImage($currentUserId, $db, $imageId)
	{
		$select_sql_string = 'SELECT * FROM imagesToAlbums WHERE imageId=' . $imageId ;
		$result = $db->query($select_sql_string);
		if (mysql_num_rows($result)==0) {
			$select_sql_string = 'SELECT * FROM images WHERE id=' . $imageId;
			$result = $db->query($select_sql_string);
			$row = mysql_fetch_array($result);
			if (!empty($row)) {
				if ($row['ownerId'] == $currentUserId) {
				}
				unlink(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $row['id'] . "." . $row['extension']);
				$delete_sql_string = 'DELETE FROM metadata WHERE imageid=' . mysql_real_escape_string($imageId);
				$db->query($delete_sql_string);
				$delete_sql_string = 'DELETE FROM images WHERE id=' . mysql_real_escape_string($imageId);
				$db->query($delete_sql_string);
			}
		}
	}
}


if (!function_exists(deleteAlbumChild)) {
	function deleteAlbumChild($currentUserId, $db, $albumId)
	{
		$albumsChild = $db->query('SELECT * FROM albums WHERE parentAlbumId="' . $albumId . ' "');
		if (!empty($albumsChild)) {
			while ($childAlbum = mysql_fetch_array($albumsChild)) {
				$images = $db->query('SELECT * FROM imagestoalbums WHERE albumId='. $childAlbum['id']);
				if (!empty($images)) {
					while ($image = mysql_fetch_array($images)) {
						$delete_sql_string = 'DELETE FROM imagestoalbums WHERE imageId="' . $image['imageId'] . '" AND albumId="'. $childAlbum['id'] .'"';
						$db->query($delete_sql_string);
						deleteImage($currentUserId, $db, $image['imageId']);
					}
				}
				deleteAlbumChild($currentUserId, $db, $childAlbum['id']);
				$delete_sql_string = 'DELETE FROM albums WHERE id="' . $childAlbum['id'] . '" ';
				$db->query($delete_sql_string);
			}
		}
	}
}
if (!function_exists(copyPhoto)) {
	function copyPhoto($db, $albumId, $newAlbumId)
	{
		$images = $db->query('SELECT * FROM imagestoalbums WHERE albumId="' . $albumId . '"');
		if (!empty($images)) {
			while ($image = mysql_fetch_array($images)) {
				$insert_sql_string = 'INSERT INTO imagestoalbums (albumId, imageId, positioninalbum) VALUES ("' . $newAlbumId . '","' . $image["imageId"] . '","' . $image["positioninalbum"] . '" )';
				$db->query($insert_sql_string);
			}
		}
	}
}

if (!function_exists(copyAlbum)) {
	function copyAlbum($db, $albumId, $newParentId)
	{
		$result = $db->query('SELECT name, description FROM albums WHERE id="'. $albumId .'"');
		if (!empty($result)) {
			$album = mysql_fetch_array($result);
			$insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("' . $newParentId. '", 0,"' . $album["name"] . '", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "' . $album["description"] . '" )';
			$db->query($insert_sql_string);
			$newAlbumId = mysql_insert_id();
			copyPhoto($db, $albumId, $newAlbumId);
			$childAlbums = $db->query('SELECT * FROM albums WHERE parentalbumid="'. $albumId . '"');
			if (!empty($childAlbums)){
				while ($childAlbum = mysql_fetch_array($childAlbums)) {
					copyAlbum($db, $childAlbum['id'], $newAlbumId);
				}
			}
		}
	}
}
?>