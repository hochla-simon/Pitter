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
    function testUpload()
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

        $this->assertEquals(1, $uploadOk);
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$last_id.'.jpg'));

        // Delete uploaded foto
        @unlink(dirname(__FILE__).'/../data/images/'.$last_id.'.jpg');
    }
}
