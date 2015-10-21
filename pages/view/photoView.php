<?php
$site['title'] = 'View photo';

$id = $_GET['id'];
if ($id != '') {
    echo '<img id="picView" src="image.html?id=' . $id .'" alt=""/>';
} else {
    include(dirname(__FILE__) . '/../common/error404.php');
}
?>