<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3. 12. 2015
 * Time: 10:46
 */
class TestCopyPhoto extends PHPUnit_Framework_TestCase
{
    static $testImageId = null;
    static $testAlbumId = null;

    public static function setUpBeforeClass() {
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        //create a new album with the name "test"
        $_POST = array(
            'Save' => true,
            'name' => 'test',
            'parentAlbumId' => '1',
            'description' => ''
        );
        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
    }

    public static function tearDownAfterClass() {
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $db->query('DELETE FROM albums WHERE id=' . self::$testAlbumId);
        $db->query('DELETE FROM imagesToAlbums WHERE albumId=' . self::$testAlbumId);

        $db->query('DELETE FROM images WHERE id=' . self::$testImageId);
        $db->query('DELETE FROM imagesToAlbums WHERE imageId=' . self::$testImageId);
        @unlink(dirname(__FILE__).'/../data/images/'. self::$testImageId.'.jpg');
    }

    public function testCopy(){
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        //determining the id of the currently created album
        $album = $db->query('SELECT id FROM albums WHERE name="test"');
        if (mysql_num_rows($album) > 0) {
            //getting the id of the last added album with the name "test"
            while($row = mysql_fetch_assoc($album)) {
                self::$testAlbumId =  $row["id"];
            }
        }

        //getting the number of images in the newly created album
        $result = $db->query('SELECT * FROM imagesToAlbums WHERE albumId=' . self::$testAlbumId);
        $initialNumberOfImages = mysql_num_rows($result);

        //upload photo to the root album
        $_FILES = array(
            'file' => array(
                'name' => 'flamingos.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => dirname(__FILE__).'/data/uploadTest/flamingos.jpg',
                'error' => 0
            )
        );
        $_GET = array(
            'page' => 'upload/upload.php'
        );
        $_POST = array(
            'albumId' => 1
        );
        include(dirname(__FILE__).'/../index.php');

        //copy the added photo from the root album to the testAlbum
        $image = $db->query('SELECT * FROM images WHERE filename="flamingos"');
        if (mysql_num_rows($image) > 0) {
            //getting the id of the last added album with the name "test"
            while($row = mysql_fetch_assoc($image)) {
                self::$testImageId =  $row["id"];
            }
        }
        $_GET = array(
            'id' => self::$testImageId
        );
        $_POST = array(
            'albumId' => self::$testAlbumId,
            'imageId'=>self::$testImageId,
            'Copy' => true
        );
        include(dirname(__FILE__).'/../pages/view/photoCopy.php');

        //getting the new number of images in the album "test"
        $testAlbumImages = $db->query('SELECT imageId FROM imagesToAlbums WHERE albumId=' . self::$testAlbumId);

        $this->assertEquals($initialNumberOfImages+1,mysql_num_rows($testAlbumImages));
    }

}
