<?php

$file_name = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR . '4.jpg';

// Get new dimensions
list($width, $height) = getimagesize($file_name);

$percent;
if(isset($_GET["max_size"])){
    $max_size = $_GET["max_size"];
    if ($width > $height){
        $percent = $max_size/$width;
    }else{
        $percent = $max_size/$height;
    }
} else {
    $percent = 0.5;
}



$new_width = $width * $percent;
$new_height = $height * $percent;

// Resample
$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($file_name);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Output
imagejpeg($image_p, null, 100);

header( "Content-type: image/jpeg" );
imagedestroy( $image_p );
die();