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

<div>
	<input type="button" value="Add new album" onclick="window.location='./albumCreate.html';">
	<ul>
		<li>
			album 1
			<input type="button" value="Add new album" onclick="window.location='./albumCreate.html?parentId=1';">
			<input type="button" value="Edit album" onclick="window.location='./albumEdit.html?id=1';">
			<input type="button" value="Delete album" onclick="window.location='./albumDelete.html?id=1'">
			<input type="button" value="Copy to" >
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
