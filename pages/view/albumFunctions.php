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
?>