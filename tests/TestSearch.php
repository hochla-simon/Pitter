<?php
class TestSearch extends PHPUnit_Framework_TestCase {
    public function testSearchingPhotoName(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'currentUserId' => 1,
            'images_per_group' => 40,
            'group_no' => 0,
            'album_id' => '',
            'keywords' => 'test'
        );

        $db->query('INSERT INTO images (ownerId, name, filename, extension, created, description) VALUES ("1", "tset", "flamingo","jpg", CURRENT_TIMESTAMP(),"Hola")');
        $newImageId = mysql_insert_id();
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ("1", "'. $newImageId .'", "2")');

        include(dirname(__FILE__).'/../pages/view/autoloadProcessSearch.php');
        $this->assertEquals(1, mysql_num_rows($result));
        $photo = mysql_fetch_assoc($result);
        $this->assertEquals("1", $photo["id"]);
        $this->assertEquals("flamingos", $photo["filename"]);
    }

    public function testFailingSearch(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'currentUserId' => 1,
            'images_per_group' => 40,
            'group_no' => 0,
            'album_id' => '',
            'keywords' => 'tests'
        );

        include(dirname(__FILE__).'/../pages/view/autoloadProcessSearch.php');
        $this->assertEquals(0, mysql_num_rows($result));
    }

    public function testInsideAlbumSearch(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'currentUserId' => 1,
            'images_per_group' => 40,
            'group_no' => 0,
            'album_id' => '3',
            'keywords' => 'tset'
        );

        $db->query('INSERT INTO images (ownerId, name, filename, extension, created, description) VALUES ("1", "tset", "bird","jpg", CURRENT_TIMESTAMP(),"Hola")');
        $newImageId = mysql_insert_id();
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ("3", "'. $newImageId .'", "1")');

        include(dirname(__FILE__).'/../pages/view/autoloadProcessSearch.php');
        $this->assertEquals(1, mysql_num_rows($result));
        $photo = mysql_fetch_assoc($result);
        $this->assertEquals($newImageId, $photo["id"]);
        $this->assertEquals("bird", $photo["filename"]);
    }

    public function testFailingInsideAlbumSearch(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'currentUserId' => 1,
            'images_per_group' => 40,
            'group_no' => 0,
            'album_id' => '3',
            'keywords' => 'flamingo'
        );

        include(dirname(__FILE__).'/../pages/view/autoloadProcessSearch.php');
        $this->assertEquals(0, mysql_num_rows($result));
    }

    public function testSearchWithAlbumName(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'currentUserId' => 1,
            'images_per_group' => 40,
            'group_no' => 0,
            'album_id' => '',
            'keywords' => 'testEdit'
        );

        include(dirname(__FILE__).'/../pages/view/autoloadProcessSearch.php');
        $this->assertEquals(1, mysql_num_rows($result));
        $photo = mysql_fetch_assoc($result);
        $this->assertEquals("bird", $photo["filename"]);
    }

    public function testSearchWithMetadata(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'currentUserId' => 1,
            'images_per_group' => 40,
            'group_no' => 0,
            'album_id' => '',
            'keywords' => '1451494651'
        );

        $db->query('INSERT INTO metadata (imageId, name, value) VALUES ("1", "testData", "1451494651")');

        include(dirname(__FILE__).'/../pages/view/autoloadProcessSearch.php');
        $this->assertEquals(1, mysql_num_rows($result));
        $photo = mysql_fetch_assoc($result);
        $this->assertEquals("1", $photo["id"]);
    }
}
?>
