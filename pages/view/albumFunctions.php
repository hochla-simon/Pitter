<?php
	function get_path($firstParentAlbumId, $db)
	{
		if ($firstParentAlbumId != -1){
			$parentAlbumId = $firstParentAlbumId;
			while( $parentAlbumId != -1 ){
				$select_sql_string = "SELECT parentAlbumId, name FROM albums WHERE id=" . mysql_real_escape_string($parentAlbumId);
				$result = $db->query($select_sql_string);
				if (!empty($result)){
					$parentAlbum = mysql_fetch_array($result);
					$path = $parentAlbum['name'] . "/" . $path;
					$parentAlbumId = $parentAlbum['parentAlbumId'];
				}
				else{
					$path = "/";
					$parentAlbumId = -1;
				}
			}
		}
		else{
			$path = "/";
		}
		return $path;
	}
	
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
	
?>