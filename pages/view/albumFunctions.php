<?php
if (!function_exists(get_path)) {
	function get_path($firstParentAlbumId, $db)
	{
		if ($firstParentAlbumId != 1) {
			$path = '';
			$parentAlbumId = $firstParentAlbumId;
			while ($parentAlbumId != 1) {
				$select_sql_string = "SELECT parentAlbumId, name FROM albums WHERE id=" . mysql_real_escape_string($parentAlbumId);
				$result = $db->query($select_sql_string);
				if (!empty($result)) {
					$parentAlbum = mysql_fetch_array($result);
					$path = $parentAlbum['name'] . "/" . $path;
					$parentAlbumId = $parentAlbum['parentAlbumId'];
				} else {
					$path = "/";
					$parentAlbumId = -1;
				}
			}
		} else {
			$path = "/";
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
	function obtainSelectAlbum($db)
	{
		$sql = "SELECT parentAlbumId, id, name FROM albums";
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
?>