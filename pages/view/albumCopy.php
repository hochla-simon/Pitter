<?php
    include('albumFunctions.php');
	
	$site['title'] = 'Copy album';
	$albumId=$_GET['id'];
	
	if($albumId != ''){
		$select_sql_string = "SELECT id, parentAlbumId, name, description FROM albums WHERE id=" . mysql_real_escape_string($albumId);
		$result = $db->query($select_sql_string);
		if (!empty($result)){
			$album = mysql_fetch_array($result);
		}
	}
?>

<form action="./albumCopyBdd.html" method="POST">
		
		<input type="hidden" name="albumId" id="albumId" value="<?php echo $album['id']; ?>" >
		
		<label for="parentAlbumId">Where do you want to copy the album <?php echo $album['name'];?> : </label>
	<select name="parentAlbumId" id="parentAlbumId">
		<?php

		$sql = "SELECT parentAlbumId, id, name FROM albums";
		$albums = $db->query($sql);

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

			function orderAlbums($id, &$children)
			{
				global $albumObjects;
				foreach ($albumObjects as $albumId => $album) {
					if ($album['parentAlbumId'] == $id) {
						$children[$albumId] = $album;
						orderAlbums($albumId, $children[$albumId]['childAlbums']);
					}
				}
			}

			orderAlbums('-1', $orderedAlbumObjects);

			function writeSelectAlbum ($albums, $subNumber, $parentId){
				global $config;
				foreach ($albums as $albumId => $album) {
					echo '<option value="' . $albumId . '" >' . str_repeat("&nbsp",$subNumber*3) . $album[name] . '</option>';
					writeSelectAlbum($album['childAlbums'], $subNumber+1, $albumId);

				}
			}
			writeSelectAlbum($orderedAlbumObjects, 0, '-1');

		}
		?>
	</select>


		<input type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
		<input type="submit" name="Save" value="Save">
		
</form>