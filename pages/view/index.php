<?php
$site['title'] = 'Photos';

$sql = "SELECT id, filename, extension FROM images";
$result = $db->query($sql);

if (!empty($result)) {
	echo '<div id="albums"></div>';
	echo '<div id="upload"></div>';
	echo '<div id="photos">';
	while($row = mysql_fetch_array($result)) {
		if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
			echo '<div class="thumbnail"><span class="center_img"></span></span><a href="photoView.html?id=' . $row['id'] . '"><img src="image.html?id=' . $row['id'] . '&max_size=100"/></a></div>';
		}
	}
	echo '</div>';
} else {
	echo '<h2>No photos!</h2>';
}
?>