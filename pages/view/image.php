<?php
$id = $_GET['id'];

$sql = "SELECT id, extension FROM images WHERE id=" . mysql_real_escape_string($id);
$result = $db->query($sql);
$row = mysql_fetch_array($result);

if ($row) {
    $id = $row['id'];
    $extension = $row['extension'];
} else {
    include(dirname(__FILE__) . '/../common/error404.php');
    die();
}

$path = dirname(__FILE__) . '/../../data/images/' . $id . '.' . $extension;

$extension = strtolower($extension);
if ($extension == 'jpg') {
    $extension = "jpeg";
}
header('Content-Type: image/' . $extension);



if(isset($_GET["max_size"])){
    list($width, $height) = getimagesize($path);
    $percent;
    $max_size = $_GET["max_size"];
    if ($width > $height){
        $percent = $max_size/$width;
    }else{
        $percent = $max_size/$height;
    }
    $new_width = $width * $percent;
    $new_height = $height * $percent;

    // Resample
    $image_p = imagecreatetruecolor($new_width, $new_height);
    $image;
    if ($extension == 'jpeg') {
        $image = imagecreatefromjpeg($path);
    }else {
        if($extension == 'gif'){
            $image = imagecreatefromgif($path);
        }else {
            $image = imagecreatefrompng($path);
        }
    }
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    if ($extension == 'jpeg') {
        imagejpeg($image_p,null,100);
    }else if($extension == 'gif'){
        imagegif($image_p,null,100);
    }else {
        imagepng($image_p,null,100);
    }
    imagedestroy( $image_p );
} else {
    readfile($path);
}


die();