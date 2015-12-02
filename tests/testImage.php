<?php
class testImage extends PHPUnit_Framework_TestCase {
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
        $_GET = array(
            'id' => '1'
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //Test delete photo
        $_POST = array(
            'Delete' => true,
            'imageId' => '1',
            'album' => array(1)
        );

        include(dirname(__FILE__).'/../pages/view/photoDelete.php');
        $results = mysql_num_rows($db->query('SELECT * FROM images'));
        $this->assertEquals('0',$results);
    }

}