<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 9. 1. 2016
 * Time: 12:13
 */
class TestMetadata extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'upload/upload.php'
        );
        $phpunit = array(
            'isTest' => true
        );
        $_POST = array(
            'albumId' => 1
        );

        //upload of JPEG
        $_FILES = array(
            'file' => array(
                'name' => 'flamingos.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/metadataTest/flamingos.jpg',
                'error' => 0
            )
        );
        include(dirname(__FILE__).'/../index.php');

        $this->jpegId = $last_id;

        $this->assertEquals(1, $uploadOk);
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$this->jpegId.'.jpg'));


        //upload of PNG
        $_FILES = array(
            'file' => array(
                'name' => 'dice.png',
                'type' => 'image/png',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/metadataTest/dice.png',
                'error' => 0
            )
        );
        include(dirname(__FILE__).'/../index.php');

        $this->pngId = $last_id;

        $this->assertEquals(1, $uploadOk);
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$this->pngId.'.png'));


        //upload of GIF
        $_FILES = array(
            'file' => array(
                'name' => 'ring.gif',
                'type' => 'image/gif',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/metadataTest/ring.gif',
                'error' => 0
            )
        );
        include(dirname(__FILE__).'/../index.php');

        $this->gifId = $last_id;

        $this->assertEquals(1, $uploadOk);
        $this->assertTrue(file_exists(dirname(__FILE__).'/../data/images/'.$this->gifId.'.gif'));
    }

    public function TestJPEGCameraInfo() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //test IFD0 values
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->jpegId . '\' AND name = \'IFD0 Model\' LIMIT 1'));
        $this->assertEquals('NIKON D60',$metadata['value']);

        //test COMPUTED values
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->jpegId . '\' AND name = \'COMPUTED Width\' LIMIT 1'));
        $this->assertEquals('3900',$metadata['value']);

        //test FILE values
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->jpegId . '\' AND name = \'FILE FileName\' LIMIT 1'));
        $this->assertEquals('flamingos.jpg',$metadata['value']);

        //test EXIF values
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->jpegId . '\' AND name = \'EXIF ExposureTime\' LIMIT 1'));
        $this->assertEquals('10/1000',$metadata['value']);
    }

    public function TestPNG()
    {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__) . '/../index.php');

        //test MIME value
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->pngId . '\' AND name = \'MIME type\' LIMIT 1'));
        $this->assertEquals('image/png', $metadata['value']);

        //test WIDTH
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->pngId . '\' AND name = \'Image width\' LIMIT 1'));
        $this->assertEquals('800', $metadata['value']);

        //test HEIGHT
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->pngId . '\' AND name = \'Image height\' LIMIT 1'));
        $this->assertEquals('600', $metadata['value']);
    }

    public function TestGif() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //test MIME value
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->gifId . '\' AND name = \'MIME type\' LIMIT 1'));
        $this->assertEquals('image/gif',$metadata['value']);

        //test WIDTH
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->gifId . '\' AND name = \'Image width\' LIMIT 1'));
        $this->assertEquals('640',$metadata['value']);

        //test HEIGHT
        $metadata = mysql_fetch_assoc($db->query('SELECT * FROM metadata WHERE imageId = \''
            . $this->gifId . '\' AND name = \'Image height\' LIMIT 1'));
        $this->assertEquals('640',$metadata['value']);
    }

    public function teardown() {
        $this->deletePhoto($this->jpegId);
        $this->deletePhoto($this->pngId);
        $this->deletePhoto($this->gifId);
    }

    public function deletePhoto($imageId){
        $_SESSION['id'] = 1;

        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $imageId
        );
        $_POST = array(
            'Delete' => true,
            'albumId' => 1
        );

        include(dirname(__FILE__).'/../pages/view/photoDelete.php');
    }
}
