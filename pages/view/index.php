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

<div>
	<input type="button" value="Add new album" onclick="window.location='./albumCreate.html';">
	<ul>
		<li>
			album 1
			<input type="button" value="Add new album" onclick="window.location='./albumCreate.html?parentId=1';">
			<input type="button" value="Edit album" onclick="window.location='./albumEdit.html?id=1';">
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