<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31/10/15
 * Time: 22:48
 */
class TestThumbnails extends PHPUnit_Framework_TestCase
{
    protected function getFileSize($id, $ext, $new_file_size){
        //$id_for_jpeg = 5;
        //$original_max_size = 3264;

        $path = dirname(__FILE__).'/tmp.'.$ext;

        $url = "http://localhost/view/image.html?id=".$id;
        if($new_file_size != null){
            $url = $url."&max_size=".$new_file_size;
        }

        $imageString = file_get_contents($url);
        file_put_contents($path,$imageString);

        list($width, $height) = getimagesize($path);

        $max_size_downloaded=1;
        if ($width>$height) {
            $max_size_downloaded = $width;
        }else{
            $max_size_downloaded = $height;
        }
        return $max_size_downloaded;
    }

    public function testJPEGS(){
        $this->assertEquals(3264, $this->getFileSize(5,'jpeg',null));
        $this->assertEquals(200, $this->getFileSize(5,'jpeg',200));
        $this->assertEquals(3264, $this->getFileSize(5,'jpeg',5000));

        $this->assertEquals(800, $this->getFileSize(13,'gif',null));
        $this->assertEquals(200, $this->getFileSize(13,'gif',200));
        $this->assertEquals(800, $this->getFileSize(13,'gif',5000));

        $this->assertEquals(512, $this->getFileSize(14,'png',null));
        $this->assertEquals(200, $this->getFileSize(14,'png',200));
        $this->assertEquals(512, $this->getFileSize(14,'png',5000));
    }


}
