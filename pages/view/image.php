<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:
$id = $_GET['id'];

$sql = "SELECT id, extension, ownerId FROM images WHERE id=" . mysql_real_escape_string($id);
$result = $db->query($sql);
$row = mysql_fetch_array($result);

if ($row) {
    $id = $row['id'];
    $extension = $row['extension'];
} else {
    include(dirname(__FILE__) . '/../common/error404.php');
    die();
}

if($row['ownerId']!=$currentUser['id']) {
    include(dirname(__FILE__) . '/../common/error401.php');
    die();
}
$path = dirname(__FILE__) . '/../../data/images/' . $id . '.' . $extension;

$extension = strtolower($extension);
if ($extension == 'jpg') {
    $extension = "jpeg";
}
header('Content-Type: image/' . $extension);



if(isset($_GET["max_size"])){
    /*header("Pragma: cache");*/
    header("Cache-Control: max-age=" . 24*60*60);
    $time_last_modification = filemtime($path);
    header("Last-Modified: " . date("F d Y H:i:s.", $time_last_modification));
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
        &&
        (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $time_last_modification)) {
        // send the last mod time of the file back
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time_last_modification).' GMT',
            true, 304);
    } else {
        list($width, $height) = getimagesize($path);
        //echo 'path: '.$path;
        $longest_side;
        if ($width > $height) {
            $longest_side = $width;
        } else {
            $longest_side = $height;
        }

        $max_size = $_GET["max_size"];

        if ($max_size >= $longest_side) {
            readfile($path);
        } else {
            $percent;

            $ratio = $longest_side / $max_size;

            $new_width = $width / $ratio;
            $new_height = $height / $ratio;

            //echo 'new width '.$new_width.' new height '.$new_height;

            // Resample

            ini_set('memory_limit', '1000M');
            $image_p = imagecreatetruecolor($new_width, $new_height);
            $image;
            if ($extension == 'jpeg') {
                $image = imagecreatefromjpeg($path);
            } else {
                if ($extension == 'gif') {
                    $image = imagecreatefromgif($path);
                } else {
                    $image = imagecreatefrompng($path);
                }
            }
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            if ($extension == 'jpeg') {
                imagejpeg($image_p, null, 100);
            } else if ($extension == 'gif') {
                imagegif($image_p);
            } else {
                imagepng($image_p);
            }
            imagedestroy($image_p);
            imagedestroy($image);
        }
    }
} else {
    readfile($path);
}
die();
endif;
?>