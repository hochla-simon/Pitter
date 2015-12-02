<?php
class TestAlbum extends PHPUnit_Framework_TestCase {
    public function testCreation(){

        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'view/albumCreate.php',
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //Test album with empty name
        $_POST = array(
            'save' => true,
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
        $_GET = array(
            'page' => 'view/albumEdit.php',
            'id' => ''
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $results = $db->query('SELECT * FROM albums');

       /* $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/albumEdit.php');

        $results = $db->query('SELECT * FROM albums WHERE name="test"');

        echo mysql_num_rows($results);

        $albumTest = $results;
        $_GET = array(
            'page' => 'view/albumEdit.php',
            'id' => $albumTest['id']
        );
        echo "toto" . $albumTest['id'];

        //Test edit album with empty name
        $_POST = array(
            'Save' => true,
            'name' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumEdit.php');
        $results = $db->query('SELECT * FROM albums WHERE id='. $albumTest['id']);
        $this->assertEquals($albumTest['name'],$results['name']);

        //Test album with name test
        /*$_POST = array(
            'save' => true,
            'name' => 'testEdit',
            'albumId' => $albumTest['id'],
            'description' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $results = $db->query('SELECT * FROM albums WHERE id='. $albumTest['id']);
        $this->assertEquals('testEdit',$results['name']);
/*
        $_POST = array(
            'save' => true,
            'name' => 'test',
            'parentAlbumId' => '1',
            'description' => ''
        );
        $results = $db->query('SELECT * FROM albums WHERE name="test"');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $results = $db->query('SELECT * FROM albums WHERE name="test"');
        $this->assertEquals($initialNumber+1,mysql_num_rows($results));
*/
    }
}
