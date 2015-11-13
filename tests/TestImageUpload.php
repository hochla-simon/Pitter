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
    /**
     * @var Upload
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // Initialization
        $_GET = array(
            'page' => 'administration/settings.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        $config['installed'] = true;
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $dataToPost = array('submit' => true);

        $_POST = array_merge($readedConfig, $dataToPost);


        $_FILES = array(
            'file' => array(
                'name' => 'flamingos.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => 'data/images',
                'error' => 0
            )
        );
        $_POST["albumId"] = '1';

        include('../pages/upload/upload.php');

//        $this->_object = new upload(__DIR__ . '/_files/');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($_FILES);
        unset($this->_object);
        @unlink(__DIR__ . 'data/images/flamingos.jpg');
    }

    /**
     * @covers Upload::receive
     */
    public function testReceive()
    {
        $this->assertTrue($this->_object->receive('test'));
    }

    function is_uploaded_file($filename)
    {
        //Check only if file exists
        return file_exists($filename);
    }

    function move_uploaded_file($filename, $destination)
    {
        //Copy file
        return copy($filename, $destination);
    }
}
