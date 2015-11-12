<?php

$site['title'] = 'Photos';
$site['script'] = '<script type="text/javascript" src="' . $config['projectURL'] . 'js/jquery.ui.position.js"></script>
	<script type="text/javascript" src="' . $config['projectURL'] . 'js/jquery.contextMenu.js"></script>
	<script type="text/javascript" src="' . $config['projectURL'] . 'js/albumViewScripts.js"></script>';

function orderAlbums($id, &$children, $albumsToOrder) {
	foreach($albumsToOrder as $albumId => $album) {
		if ($album['parentAlbumId'] == $id) {
			$children[$albumId] = $album;
			orderAlbums($albumId, $children[$albumId]['childAlbums'], $albumsToOrder);
		}
	}
}

function createAlbums ($albums, $subNumber, $parentId) {
	global $config;
	$display = 'none';
	if ($subNumber == 0) {
		$display = '';
		$albumClass = 'albums';
	} else {
		$albumClass = 'childAlbums';
	}

	echo '<ul class="' . $albumClass . '" data-parentAlbumId="' . $parentId . '" style="display: ' . $display . '">';
	foreach ($albums as $albumId => $album) {
		$visibility = '';
		if (empty($album['childAlbums'])) {
			$visibility = 'hidden';
		}
		echo '<li class="context-menu-one box menu-1" data-id ="' . $albumId . '" data-path="' . $config['projectURL'] . '">
			<img class="toggleArrow" style="visibility: ' . $visibility . '" src="' . $config['projectURL'] . 'images/arrow_right.png" alt=""/>
			<a href="?id=' . $albumId .'">
				<img src="' . $config['projectURL'] . 'images/folder.png" alt=""/>
				<span>' . $album[name] . '</span>
			</a>';

		createAlbums($album['childAlbums'], $subNumber + 1, $albumId);

		echo '</li>';
	}
	echo '</ul>';
}

$albumId = $_GET['id'];
$albumName;

$sql = "SELECT parentAlbumId, id, name FROM albums";
$albums = $db->query($sql);

if (!empty($albums)) {

	$albumObjects = array();
	while($row = mysql_fetch_array($albums)) {
		$albumObject = array(
			'name' => $row['name'],
			'parentAlbumId' => $row['parentAlbumId'],
			'childAlbums' => array()
		);
		$albumObjects[$row['id']] = $albumObject;
		if ($row['id'] == $albumId) {
			$albumName = $row['name'];
		}
	}

	$orderedAlbumObjects = array();

	orderAlbums('-1', $orderedAlbumObjects, $albumObjects);

	echo '<div id="albumsContainer">';

	createAlbums($orderedAlbumObjects, 0, '-1');

	echo '</div>';
}

echo '<div id="albumView">';

if (!$albumId) {
	$albumId = '1';
	$albumName = '/';
}
echo '<div id="albumTitle"><img src="' . $config['projectURL'] . 'images/folder.png" alt=""/><h2>' . $albumName . '</h2></div>';

echo '<div id="upload">

<form action="'.$config['projectURL'].'upload/upload.html"
      class="dropzone"
      id="myDropzone">';
	if($albumId) echo '<input type="hidden" name="albumId" value="'.$albumId.'" />';
	else echo '<input type="hidden" name="albumId" value="1" />';
echo '</form>';

echo '<script>

        Dropzone.options.myDropzone = {
            dictInvalidFileType : "only jpg, jpeg, png and gif are accepted",
            acceptedFiles: "image/jpeg,image/png,image/gif",
            init: function() {
                this.on("error", function (file, errorMessage, XMLHttpRequestMessage) {
                	debugger;
                	if (XMLHttpRequestMessage===undefined){

                	}else{
                		$($(file.previewElement).find("div.dz-error-message")[0]).find("span").html("Error uploading");
                	}
                });
                this.on("success", function (file, response) {
                    if(file.accepted){
                        htmlNewTag = \'<div class="thumbnail"><span class="center_img"></span><a href="photoView.html?id=\'+response.lastId+\'"><img src="image.html?id=\'+response.lastId+\'&max_size=100"></img></a></div>\';
                        $("div#photos").append(htmlNewTag);
                        this.removeFile(file);
                    }
                });
            }
        }

</script>
</div>';

$sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId = " . mysql_real_escape_string($albumId) . " ORDER BY imagesToAlbums.positionInAlbum";
$images = $db->query($sql);

if (!empty($images)) {
	echo '<div id="photos">';
	while($row = mysql_fetch_array($images)) {
		if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
			echo '<a href="photoView.html?id=' . $row['id'] . '"><div class="thumbnail" title="' . $row['filename'] . '.' . $row['extension'] . '"><span class="center_img"></span><img src="image.html?id=' . $row['id'] . '&max_size=100"/></div></a>';
		}
	}
	echo '</div>';
} else {
	echo '<h2>No photos!</h2>';
}
echo '</div>';
?>

<!--
<div id="albumsContainer">
	<ul class="albums" data-parentAlbumId="-1">
		<li class="context-menu-one box menu-1" data-id ="2">
			<img class="toggleArrow" style="visibility: " src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
			<img src="http://localhost/Pitter/images/folder.png" alt=""/>
			<a href="?id=2">album2</a>
			<ul class="albums" data-parentAlbumId="2">
				<li class="context-menu-one box menu-1" data-id ="18">
					<img class="toggleArrow" style="visibility: hidden" src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
					<img src="http://localhost/Pitter/images/folder.png" alt=""/>
					<a href="?id=18">Album1</a>
					<ul class="albums" data-parentAlbumId="18">
					</ul>
				</li>
			</ul>
		</li>
		<li class="context-menu-one box menu-1" data-id ="3">
			<img class="toggleArrow" style="visibility: " src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
			<img src="http://localhost/Pitter/images/folder.png" alt=""/>
			<a href="?id=3">Album3</a>
			<ul class="albums" data-parentAlbumId="3">
				<li class="context-menu-one box menu-1" data-id ="9display: none">
					<img class="toggleArrow" style="visibility: hidden" src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
					<img src="http://localhost/Pitter/images/folder.png" alt=""/>
					<a href="?id=9">Album4</a>
					<ul class="albums" data-parentAlbumId="9">
					</ul>
				</li>
			</ul>
		</li>
		<li class="context-menu-one box menu-1" data-id ="4display: ">
			<img class="toggleArrow" style="visibility: hidden" src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
			<img src="http://localhost/Pitter/images/folder.png" alt=""/>
			<a href="?id=4">Album4</a>
			<ul class="albums" data-parentAlbumId="4"></ul>
		</li>
	</ul>
</div>

-->
