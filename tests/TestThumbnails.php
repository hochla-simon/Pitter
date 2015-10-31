<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31/10/15
 * Time: 22:48
 */
class TestThumbnails extends PHPUnit_Framework_TestCase
{
    public function testJPEGThumbnail(){
        $id_for_jpeg = 5;
        $original_max_size = 3264;

        $path = dirname(__FILE__).'/tmp.jpeg';

        $imageString = file_get_contents("http://localhost/view/image.html?id=".$id_for_jpeg);
        file_put_contents($path,$imageString);

        list($width, $height) = getimagesize($path);

        $max_size_downloaded=1;
        if ($width>$height) {
            $max_size_downloaded = $width;
        }else{
            $max_size_downloaded = $height;
        }
        $this->assertEquals($max_size_downloaded, $original_max_size);

    }



}
