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

        $config['installed'] = false;
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $dataToPost = array('submit' => true);
        $_POST = array_merge($readedConfig, $dataToPost, array(
            'adminFirstName' => 'John',
            'adminLastName' => 'Doe',
            'adminEmail' => 'john@example.org',
            'adminPassword' => 'test',
            'adminPassword2' => 'test')
        );

        include(dirname(__FILE__).'/../index.php');


        $this->assertContains('Installation successful.', $message);
        $this->assertEquals($config['installed'], true);


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
        include(dirname(__FILE__).'/../pages/upload/upload.php');

        $this->assertEquals(0, $uploadOk);
        $this->assertEquals(404, $response_code);

        // Error because of not existing album
        $_POST = array(
            'albumId' => -2
        );
        include(dirname(__FILE__).'/../pages/upload/upload.php');

        $this->assertEquals(0, $uploadOk);
        $this->assertEquals(401, $response_code);

        // Correct upload
        $_POST = array(
            'albumId' => 1
        );
        include(dirname(__FILE__).'/../pages/upload/upload.php');

        $this->assertEquals(1, $uploadOk);
        echo $last_id;
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$last_id.'.jpg'));

        @unlink(dirname(__FILE__).'/../data/images/'.$last_id.'.jpg');
    }
}
