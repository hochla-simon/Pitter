<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 22. 10. 2015
 * Time: 3:16
 */
namespace Library;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class UploadTest extends \PHPUnit_Framework_TestCase
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
        $_FILES = array(
            'file' => array(
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => __DIR__ . 'C:\Users\Simon\Desktop\Fotky_Barcelona\IMAG0441.jpg',
                'error' => 0
            )
        );

        //$this->_object = new Upload(__DIR__ . '/_files/');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($_FILES);
        unset($this->_object);
        @unlink(__DIR__ . '/_files/test.jpg');
    }

    /**
     * @covers Upload::receive
     */
    public function testReceive()
    {
        //$this->assertTrue($this->_object->receive('test'));
//        include '../pages/upload/upload.php';
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