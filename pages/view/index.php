<?php
$site['title'] = 'Photos';

$albumId = $_GET['id'];
$albumName;

$sql = "SELECT parentAlbumId, id, name FROM albums";
$albums = $db->query($sql);

if (!empty($albums)) {
	echo '<div id="albums"><ul>';
	while($row = mysql_fetch_array($albums)) {
		echo '<li><img src="/images/folder.png" alt=""/><a href="?id=' . $row[id] . '">' . $row[name] . '</a></li>';
		if ($row['id'] == $albumId) {
			$albumName = $row['name'];
		}
	}
	echo '</ul></div>';
}

echo '<div id="albumView">';

if (!$albumId) {
	$sql = "SELECT id, filename, extension FROM images";
} else {
	echo '<div id="albumTitle"><img src="/images/folder.png" alt=""/><h2>' . $albumName . '</h2></div>';
	$sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId = " . mysql_real_escape_string($albumId);
}
$images = $db->query($sql);

echo '<div id="upload">

<form action="../upload/upload.html"
      class="dropzone"
      id="myDropzone">';
	if($albumId) echo '<input type="hidden" name="albumId" value="'.$albumId.'" />';
echo	'</form>

<script>

        Dropzone.options.myDropzone = {
            dictInvalidFileType : "only jpg, jpeg, png and gif are accepted",
            acceptedFiles: "image/jpeg,image/png,image/gif",
            init: function() {
                this.on("complete", function (file) {
                    if(file.accepted){
                        console.log(file)
                        this.removeFile(file);
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

<div>
	<input type="button" value="Add new album" onclick="window.location='./albumCreate.html';">
	<ul>
		<li>
			album 1
			<input type="button" value="Add new album" onclick="window.location='./albumCreate.html?parentId=1';">
			<input type="button" value="Edit album" onclick="window.location='./albumEdit.html?id=1';">
			<input type="button" value="Delete album" onclick="window.location='./albumDelete.html?id=1'">
			<input type="button" value="Copy to" onclick="window.location='./albumCopy.html?id=1';">
			<input type="button" value="Move to" >
			<ul>
				<li> sous-album 1 </li>
				<li> sous-album 2 </li>
			</ul>
		</li>
		<li>
			album 2
		</li>
	</ul>
</div>
