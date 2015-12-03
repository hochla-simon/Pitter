<?php

#include settings.php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 12. 11. 2015
 * Time: 23:01
 */
class TestImageUpload extends PHPUnit_Framework_TestCase
{
    private $photoId = null;

    public function testUpload()
    {
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'upload/upload.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        $_FILES = array(
            'file' => array(
                'name' => 'hypo.nef',
                'type' => 'application/nef',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/uploadTest/hypo.NEF',
                'error' => 0
            )
        );

        // Error because of not set album
        $_POST = array();
        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(0, $uploadOk);
        $this->assertEquals(404, $response_code);

        // Error because of not existing album
        $_POST = array(
            'albumId' => -2
        );
        include(dirname(__FILE__).'/../index.php');

        $this->assertEquals(0, $uploadOk);
        $this->assertEquals(401, $response_code);

        // Unsupported format
        $_POST = array(
            'albumId' => 1
        );
        include(dirname(__FILE__).'/../index.php');

        $this->assertEquals(400, $response_code);
        $this->assertEquals(0, $uploadOk);

        // Correct upload of JPEG
        $_FILES = array(
            'file' => array(
                'name' => 'flamingos.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/uploadTest/flamingos.jpg',
                'error' => 0
            )
        );
        include(dirname(__FILE__).'/../index.php');

        $this->photoId = $last_id;
        $this->assertEquals(1, $uploadOk);
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg'));
    }

    public function testThumbnails()
    {
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'view/image.php',
            'id' => $this->photoId
        );
        $phpunit = array(
            'isTest' => true
        );

        // Test without maximum size
        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals($site['content'], file_get_contents(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg'));

        // Test with a maxium size of 1000px
        $_GET['max_size'] = 1000;
        include(dirname(__FILE__).'/../index.php');
        $newFormat = getimagesizefromstring($site['content']);
        $expectedFormat = $this->getDimensions(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg', $_GET['max_size']);
        $this->assertEquals($newFormat[0], $expectedFormat[0]);
        $this->assertEquals($newFormat[1], $expectedFormat[1]);

        // Test with a maxium size of 200px
        $_GET['max_size'] = 200;
        include(dirname(__FILE__).'/../index.php');
        $newFormat = getimagesizefromstring($site['content']);
        $expectedFormat = $this->getDimensions(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg', $_GET['max_size']);
        $this->assertEquals($newFormat[0], $expectedFormat[0]);
        $this->assertEquals($newFormat[1], $expectedFormat[1]);

        // Test with a maxium size of 100px
        $_GET['max_size'] = 100;
        include(dirname(__FILE__).'/../index.php');
        $newFormat = getimagesizefromstring($site['content']);
        $expectedFormat = $this->getDimensions(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg', $_GET['max_size']);
        $this->assertEquals($newFormat[0], $expectedFormat[0]);
        $this->assertEquals($newFormat[1], $expectedFormat[1]);

        // Test with a maxium size of 50px
        $_GET['max_size'] = 50;
        include(dirname(__FILE__).'/../index.php');
        $newFormat = getimagesizefromstring($site['content']);
        $expectedFormat = $this->getDimensions(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg', $_GET['max_size']);
        $this->assertEquals($newFormat[0], $expectedFormat[0]);
        $this->assertEquals($newFormat[1], $expectedFormat[1]);
    }

    private function getDimensions($file, $max_size){
        $image = getimagesize($file);
        if($image[0] > $max_size){
            $image[1] /= $image[0] / $max_size;
            $image[0] = $max_size;
        }
        if($image[1] > $max_size){
            $image[0] /= $image[1] / $max_size;
            $image[1] = $max_size;
        }
        $image[0] = floor($image[0]);
        $image[1] = floor($image[1]);
        return $image;
    }

    public function cleanup(){

        // Delete uploaded foto
        @unlink(dirname(__FILE__).'/../data/images/'.$this->photoId.'.jpg');
    }
}
