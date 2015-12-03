<?php
class TestAlbum extends PHPUnit_Framework_TestCase {
    public function testCreation(){

        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //Test album with empty name
        $_POST = array(
            'Save' => true,
        );

        $results = $db->query('SELECT * FROM albums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $results = $db->query('SELECT * FROM albums');
        $this->assertEquals($initialNumber,mysql_num_rows($results));

        //Test album with name test
        $_POST = array(
            'Save' => true,
            'name' => 'test',
            'parentAlbumId' => '1',
            'description' => ''
        );
        $results = $db->query('SELECT * FROM albums WHERE name="test"');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $results = $db->query('SELECT * FROM albums WHERE name="test"');
        $this->assertEquals($initialNumber+1,mysql_num_rows($results));
    }

    public function testEdit(){

        // Initialization
        $_SESSION['id'] = 1;
        
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $results = $db->query('SELECT * FROM albums WHERE name="test"');
        $albumTest = mysql_fetch_array($results);
        $_GET = array(
            'id' => $albumTest['id']
        );

        //Test edit album with empty name
        $_POST = array(
            'Save' => true,
            'name' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumEdit.php');
        $results = mysql_fetch_array($db->query('SELECT * FROM albums WHERE id='. $albumTest['id']));
        $this->assertEquals($albumTest['name'],$results['name']);
        $this->assertEquals($albumTest['description'],$results['description']);
        $this->assertEquals($albumTest['parentAlbumId'],$results['parentAlbumId']);

        //Test album with name testEdit
        $_POST = array(
            'Save' => true,
            'name' => 'testEdit',
            'albumId' => $albumTest['id'],
        );

        include(dirname(__FILE__).'/../pages/view/albumEdit.php');
        $results = mysql_fetch_array($db->query('SELECT * FROM albums WHERE id='. $albumTest['id']));
        $this->assertEquals('testEdit',$results['name']);
        $this->assertEquals($albumTest['description'],$results['description']);
        $this->assertEquals($albumTest['parentAlbumId'],$results['parentAlbumId']);

        //Test album with description
        $_POST = array(
            'Save' => true,
            'name' => 'testEdit',
            'description' => 'testEdit',
            'albumId' => $albumTest['id'],
        );

        include(dirname(__FILE__).'/../pages/view/albumEdit.php');
        $results = mysql_fetch_array($db->query('SELECT * FROM albums WHERE id='. $albumTest['id']));
        $this->assertEquals('testEdit',$results['name']);
        $this->assertEquals('testEdit',$results['description']);
        $this->assertEquals($albumTest['parentAlbumId'],$results['parentAlbumId']);

    }

    public function testMove(){
        // Initialization
        $_SESSION['id'] = 1;

        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $results = $db->query('SELECT * FROM albums WHERE name="testEdit"');
        $albumTest = mysql_fetch_array($results);
        $_GET = array(
            'id' => $albumTest['id']
        );

        //Test move album in it self
        $_POST = array(
            'Save' => true,
            'albumId' => $albumTest['id'],
            'parentAlbumId' => $albumTest['id']
        );

        include(dirname(__FILE__).'/../pages/view/albumMove.php');
        $results = mysql_fetch_array($db->query('SELECT * FROM albums WHERE id='. $albumTest['id']));
        $this->assertContains("Sorry, you cannot move a folder into a child folder.", $message);


        //Test move album in another family
        $db->query('INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES ("1", "1","album 1", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "description")');
        $newAlbumId = mysql_insert_id();
        $_POST = array(
            'Save' => true,
            'albumId' => $albumTest['id'],
            'parentAlbumId' => $newAlbumId
        );

        include(dirname(__FILE__).'/../pages/view/albumMove.php');
        $results = mysql_fetch_array($db->query('SELECT * FROM albums WHERE id='. $albumTest['id']));
        $this->assertEquals($newAlbumId,$results['parentAlbumId']);
    }

    public function checkPhoto($db, $albumId, $newAlbumId){
        $originalImages = $db->query('SELECT imageId FROM imagestoalbums WHERE albumID="' . $albumId . ' "');
        while ($image = mysql_fetch_array($originalImages)) {
            $newImage = mysql_fetch_array($db->query('SELECT imageId FROM imagestoalbums WHERE albumID="' . $newAlbumId . ' AND imageId ='. $image['imageId'] .'"'));
            $this->assertEquals($image['imageId'], $newImage['imageId'] );
        }
    }

    public function checkAlbums($db, $albumRefId, $albumRefName, $albumId){
        $firstChilds = $db->query('SELECT * FROM albums WHERE parentAlbumId="' . $albumId. ' "');
        if (!empty($firstChilds)) {
            while ($childAlbum = mysql_fetch_array($firstChilds)) {
                $results = mysql_fetch_array($db->query('SELECT * FROM albums WHERE parentAlbumId="' . $albumRefId . '" AND name="' . $albumRefName . '" ORDER BY id DESC'));
                checkPhoto($db, $albumRefId, $results['id']);
                checkAlbums($db, $albumRefId, $results['id']);
            }
        }
    }

    public function testCopy(){
        // Initialization
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $results = $db->query('SELECT * FROM albums WHERE name="testEdit"');
        $albumTest = mysql_fetch_array($results);
        $_GET = array(
            'id' => $albumTest['id']
        );

        //Test copy album
        $_POST = array(
            'Save' => true,
            'albumId' => $albumTest['id'],
            'parentAlbumId' => '1'
        );

        include(dirname(__FILE__).'/../pages/view/albumCopy.php');
        $this->checkAlbums($db, $albumTest['id'], $albumTest['name'], $results['id']);
    }

    public function testDelete(){
        // Initialization
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $results = $db->query('SELECT * FROM albums WHERE name="testEdit" ORDER BY id DESC');
        $albumTest = mysql_fetch_array($results);
        $_GET = array(
            'id' => $albumTest['id']
        );

        $results = $db->query('SELECT * FROM imagestoalbums');
        $initialNumber = mysql_num_rows($results);

        $images = $db->query('SELECT * FROM imagestoalbums WHERE albumId="'. $albumTest['id']. '"');
        $imagesNumber = mysql_num_rows($images);

        //Test delete album
        $_POST = array(
            'Delete' => true,
            'albumId' => $albumTest['id'],
        );
        include(dirname(__FILE__).'/../pages/view/albumDelete.php');

        $results = $db->query('SELECT * FROM imagestoalbums');
        $newNumber = mysql_num_rows($results);
        $this->assertEquals($initialNumber-$imagesNumber,$newNumber);
    }
}
