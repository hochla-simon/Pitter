<?php
class TestAlbum extends PHPUnit_Framework_TestCase {
    public function testCreation(){

        // Initialization
        $_GET = array(
            'page' => 'view/albumCreate.php',
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );

        $config['installed'] = false;
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $dataToPost = array('submit' => true);
        $_POST = array_merge($readedConfig, $dataToPost);
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
    }
}
