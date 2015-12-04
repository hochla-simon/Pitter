<?php
class TestImage extends PHPUnit_Framework_TestCase {
    public function testEdit(){
        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'id' => '1'
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //Test photo
        $_POST = array(
            'Save' => true,
            'imageId' => '1',
            'name' => 'test',
            'description' => 'description'
        );

        include(dirname(__FILE__).'/../pages/view/photoEdit.php');
        $results = mysql_fetch_array($db->query('SELECT * FROM images WHERE id="1"'));
        $this->assertEquals('test',$results['name']);
        $this->assertEquals('description',$results['description']);
    }

    public function testDelete(){
        // Initialization
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //Test delete photo from one album
        $db->query('INSERT INTO images (ownerId, name, filename, extension, created, description) VALUES ("1", "", "flamingo","jpg", CURRENT_TIMESTAMP(),"")');
        $newImageId = mysql_insert_id();
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ("1", "'. $newImageId .'", "1")');

        $target = dirname(__FILE__).'/data/uploadTest/flamingos.jpg'; // Ceci est le fichier qui existe actuellement
        $link = dirname(__FILE__).'/../data/images/'.$newImageId.'.jpg';  // Ceci sera le nom du fichier que vous voulez lier
        link($target, $link);
        $_GET = array(
            'id' => $newImageId
        );
        $_POST = array(
            'Delete' => true,
            'album' => array(1)
        );

        include(dirname(__FILE__).'/../pages/view/photoDelete.php');
        $results = mysql_num_rows($db->query('SELECT * FROM images'));
        $this->assertEquals('1',$results);

        //Test delete photo from one album but existing in two
        $db->query('INSERT INTO images (ownerId, name, filename, extension, created, description) VALUES ("1", "", "flamingo","jpg", CURRENT_TIMESTAMP(),"")');
        $newImageId = mysql_insert_id();
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ("1", "'. $newImageId .'", "1")');
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ("2", "'. $newImageId .'", "1")');

        $target = dirname(__FILE__).'/data/uploadTest/flamingos.jpg'; // Ceci est le fichier qui existe actuellement
        $link = dirname(__FILE__).'/../data/images/'.$newImageId.'.jpg';  // Ceci sera le nom du fichier que vous voulez lier
        link($target, $link);
        $_GET = array(
            'id' => $newImageId
        );
        $_POST = array(
            'Delete' => true,
            'album' => array(1)
        );

        include(dirname(__FILE__).'/../pages/view/photoDelete.php');
        $results = mysql_num_rows($db->query('SELECT * FROM images'));
        $this->assertEquals('2',$results);

        //Test delete photo from two album, photo in only two albums
        $db->query('INSERT INTO imagestoalbums (albumId, imageId, positionInAlbum) VALUES ("1", "'. $newImageId .'", "1")');

        $_GET = array(
            'id' => $newImageId
        );
        $_POST = array(
            'Delete' => true,
            'album' => array(1, 2)
        );

        include(dirname(__FILE__).'/../pages/view/photoDelete.php');
        $results = mysql_num_rows($db->query('SELECT * FROM images'));
        $this->assertEquals('1',$results);

    }

}