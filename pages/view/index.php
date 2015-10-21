<?php
$site['title'] = 'Photos';

$conn = new mysqli('localhost', 'root', '', 'pitter');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, imageFormat FROM Pictures";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo '<ul id="picList">';
	while($row = $result->fetch_assoc()) {
		echo '<li><a href="photoView.html?id=' . $row["id"] . '">' . $row["name"] . '.' . $row["imageFormat"] . '</a></li>';
	}
	echo '</ul>';
} else {
	echo "No photos!";
}
$conn->close();
?>