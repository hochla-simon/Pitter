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
        $_GET = array(
            'page' => 'upload/upload.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        $config['installed'] = true;

        $_FILES = array(
            'file' => array(
                'name' => 'flamingos.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/uploadTest/flamingos.jpg',
                'error' => 0
            )
        );

        // Error because of not set album
        $_POST = array();
        include(dirname(__FILE__).'/../index.php');

        $this->assertEquals($uploadOk, 0);
        $this->assertEquals($response_code, 500);

        // Error because of not existing album
        $_POST = array(
            'albumId' => -2
        );
        include(dirname(__FILE__).'/../index.php');

        $this->assertEquals($uploadOk, 0);
        $this->assertEquals($response_code, 500);

        // Correct upload
        $_POST = array(
            'albumId' => 1
        );
        include(dirname(__FILE__).'/../index.php');

        $this->assertEquals($uploadOk, 1);
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$last_id.'.jpg'));

        @unlink(dirname(__FILE__).'/../data/images/'.$last_id.'.jpg');
    }
}
