<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3. 12. 2015
 * Time: 14:48
 */
class TestOwnership extends PHPUnit_Extensions_Selenium2TestCase
{
    static $testImageId = null;
    static $testAlbumId = null;

    public $projectURL;

    protected function setUp() {
        $this->setBrowser('chrome');
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $this->projectURL=$readedConfig['projectURL'];
        $this->setBrowserUrl($this->projectURL.'/view/index.html');
    }

    protected function login() {
        $this->url($this->projectURL.'/users/login.html');
        $this->byId('setting_email')->value('jim.doe@example.org');
        $this->byId('setting_password')->value('test1234');
        $this->byClassName('submit')->click();
    }

    public function testPhotoOwnership() {
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'upload/upload.php'
        );
        $_POST = array(
            'albumId' => 1
        );
        $phpunit = array(
            'isTest' => true
        );

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


        $image = $db->query('SELECT * FROM images WHERE filename="flamingos"');
        if (mysql_num_rows($image) > 0) {
            //getting the id of the last added album with the name "test"
            while($row = mysql_fetch_assoc($image)) {
                self::$testImageId =  $row["id"];
                $ownerId = $row["ownerId"];
            }
        }
        $this->assertEquals(1,$ownerId);
    }

    public function testAlbumOwnership(){

        //testing as Administrator
        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'name' => 'test',
            'parentAlbumId' => '1',
            'description' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $results = $db->query('SELECT * FROM albums WHERE name="test"');
        if (mysql_num_rows($results) > 0) {
            //getting the id of the last added album with the name "test"
            while($row = mysql_fetch_assoc($results)) {
                self::$testAlbumId =  $row["id"];
                $testAlbumOwnerId = $row["ownerId"];
            }
        }
        $this->assertEquals($testAlbumOwnerId,1);

        //testing as nonadministrator
        $_SESSION['id'] = 2;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );


        include(dirname(__FILE__).'/../index.php');


        // Successful creation of user
        $_POST = array(
            'firstName' => 'Jim',
            'lastName' => 'Doe',
            'email' => 'jim.doe@example.org',
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $db->query('UPDATE users SET enabled="1" WHERE email = "jim.doe@example.org"');

        $this->login();

        $currentUser['id'] = 2;
        $_POST = array(
            'Save' => true,
            'name' => 'nonAdmin',
            'ownerId' => 2,
            'parentAlbumId' => '2',
            'description' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $results = $db->query('SELECT * FROM albums WHERE name="nonAdmin"');
        if (mysql_num_rows($results) > 0) {
            //getting the id of the last added album with the name "test"
            while($row = mysql_fetch_assoc($results)) {
                self::$testAlbumId =  $row["id"];
                $testAlbumOwnerId = $row["ownerId"];
            }
        }
        $this->assertEquals($testAlbumOwnerId,2);
    }
}
