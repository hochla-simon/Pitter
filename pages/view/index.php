<?php

function orderAlbums($id, &$children, $albumsToOrder) {
	foreach($albumsToOrder as $albumId => $album) {
		if ($album['parentAlbumId'] == $id) {
			$children[$albumId] = $album;
			orderAlbums($albumId, $children[$albumId]['childAlbums'], $albumsToOrder);
		}
	}
}

function createAlbums($albums, $subNumber, $parentId) {
	global $config;
	$display = '';
	if ($subNumber != 0) {
		$display = 'none';
	}
	foreach ($albums as $albumId => $album) {
		$visibility = '';
		if (empty($album['childAlbums'])) {
			$visibility = 'hidden';
		}
		echo '<li class="context-menu-one box menu-1" data-id ="' . $albumId . '" data-parentAlbumId="' . $parentId . '" style="margin-left: ' . $subNumber * 20 . 'px; display: ' . $display . '"><img class="toggleArrow" style="visibility: ' . $visibility . '" src="' . $config['projectURL'] . 'images/arrow_right.png" alt=""/><img src="' . $config['projectURL'] . 'images/folder.png" alt=""/><a href="?id=' . $albumId . '">' . $album[name] . '</a></li>';
		createAlbums($album['childAlbums'], $subNumber + 1, $albumId);
	}
}

$site['title'] = 'Photos';

echo '<link rel="stylesheet" href="' . $config['projectURL'] . '/css/jquery.contextMenu.css" type="text/css" />';
echo '<script src="' . $config['projectURL'] . '/js/jquery.contextMenu.js" type="text/javascript"></script>';
echo '<script src="' . $config['projectURL'] . '/js/jquery.ui.position.js" type="text/javascript"></script>';
echo '<script src="' . $config['projectURL'] . '/js/albumViewScripts.js" type="text/javascript"></script>';

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
		orderAlbums('-1', $orderedAlbumObjects);

    echo '<div id="albumsContainer">';

    function createAlbumsBrothers ($albums, $subNumber, $parentId){
        global $config;
        $display=null;
        if ($subNumber != 0) {
            $display = 'none';
        }

        echo '<ul class="albums" data-parentAlbumId="' . $parentId . '">';
        foreach ($albums as $albumId => $album) {
            $visibility=null;
            if (empty($album['childAlbums'])) {
                $visibility = 'hidden';
            }
            echo '<li data-id ="' . $albumId . 'display: ' . $display .
                '"><img class="toggleArrow" style="visibility: ' . $visibility . '" src="'
                . $config['projectURL'] . 'images/arrow_right.png" alt=""/><img src="' .
                $config['projectURL'] . 'images/folder.png" alt=""/><a href="?id=' . $albumId .
                '">' . $album[name] . '</a>';

            createAlbumsBrothers($album['childAlbums'], $subNumber+1, $albumId);

            echo '</li>';
        }
        echo '</ul>';
    }
    createAlbumsBrothers($orderedAlbumObjects, 0, '-1');

	echo '</div>';
}

echo '<div id="albumView">';

if (!$albumId) {
	$sql = "SELECT id, filename, extension FROM images";
} else {
	echo '<div id="albumTitle"><img src="' . $config['projectURL'] . 'images/folder.png" alt=""/><h2>' . $albumName . '</h2></div>';
	$sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId = " . mysql_real_escape_string($albumId) . " ORDER BY imagesToAlbums.positionInAlbum";
}
$images = $db->query($sql);

echo '<div id="upload">

<form action="../upload/upload.html"
      class="dropzone"
      id="myDropzone">';
	if($albumId) echo '<input type="hidden" name="albumId" value="'.$albumId.'" />';
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
                    	debugger;
                        htmlNewTag = \'<div class="thumbnail"><span class="center_img"></span><a href="photoView.html?id=\'+response.lastId+\'"><img src="image.html?id=\'+response.lastId+\'&max_size=100"></img></a></div>\';
                        $("div#photos").append(htmlNewTag);
                        console.log(file)
                        this.removeFile(file);
                        console.log(response);
                    }
                });
            }
        }

</script>
</div>';

if (!empty($images)) {
	echo '<div id="photos">';
	while($row = mysql_fetch_array($images)) {
		if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
			echo '<div class="thumbnail"><span class="center_img"></span></span><a href="photoView.html?id=' . $row['id'] . '"><img src="image.html?id=' . $row['id'] . '&max_size=100"/></a></div>';
		}
	}
	echo '</div>';
} else {
	echo '<h2>No photos!</h2>';
}
echo '</div>';
?>
