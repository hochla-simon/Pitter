<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:
$site['title'] = 'View photo';

$id = $_GET['id'];
if ($id != '') {
    $sql = "SELECT id,ownerId,name FROM images WHERE id=" . mysql_real_escape_string($id);
    $result = $db->query($sql);
    $row = mysql_fetch_array($result);
    if ($row) {
        if($row['ownerId']==$currentUser['id']) {
            echo '<img id="back_button" src="' . $config['projectURL'] . '/images/back.png" alt="" onclick="history.go(-1)">';
            echo '<h2 id="photo_name">' . $row['name'] . '</h2>';
            echo '<script src="' . $config['projectURL'] . '/js/photoViewScripts.js" type="text/javascript"></script>';
            echo '<img id="picView" src="image.html?id=' . $id . '" alt=""/>';
        }else{
            include(dirname(__FILE__) . '/../common/error401.php');
        }
    } else {
        include(dirname(__FILE__) . '/../common/error404.php');
    }

} else {
    include(dirname(__FILE__) . '/../common/error404.php');
}
endif;
?>