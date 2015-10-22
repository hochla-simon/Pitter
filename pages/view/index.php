<?php
$site['title'] = 'Photos';

$sql = "SELECT id, name, imageFormat FROM Pictures";
$result = $db->query($sql);

if (mysql_num_rows($result) > 0) {
	echo '<ul id="picList">';
	while($row = mysql_fetch_array($result)) {
		echo '<li><a href="photoView.html?id=' . $row["id"] . '">' . $row["name"] . '.' . $row["imageFormat"] . '</a></li>';
	}
	echo '</ul>';
} else {
	echo "No photos!";
}
?>