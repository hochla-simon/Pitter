<?php
$site['title'] = 'Photos';

$sql = "SELECT id, filename, extension FROM images";
$result = $db->query($sql);

if (!empty($result)) {
	echo '<ul id="picList">';
	while($row = mysql_fetch_array($result)) {
		echo '<li><a href="photoView.html?id=' . $row['id'] . '">' . $row['filename'] . '.' . $row['extension'] . '</a></li>';
	}
	echo '</ul>';
} else {
	echo '<h2>No photos!</h2>';
}
?>