<?php
class TestUserBasedSharing extends PHPUnit_Framework_TestCase {
    public function testSuccesfulSharing(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'albumId' => '1',
            'userId' => '2'
        );

        $results = $db->query('SELECT * FROM usersToAlbums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/shareAlbum.php');
        $results = $db->query('SELECT * FROM usersToAlbums');
        $this->assertEquals($initialNumber + 1, mysql_num_rows($results));
    }

    public function testSharingSameAlbumToSameUser(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'albumId' => '1',
            'userId' => '2'
        );

        $results = $db->query('SELECT * FROM usersToAlbums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/shareAlbum.php');
        $results = $db->query('SELECT * FROM usersToAlbums');
        $this->assertEquals($initialNumber, mysql_num_rows($results));
    }

    public function testSharingAlbumThatUserDoesNotOwn(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'albumId' => '2',
            'userId' => '2'
        );

        $results = $db->query('SELECT * FROM usersToAlbums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/shareAlbum.php');
        $results = $db->query('SELECT * FROM usersToAlbums');
        $this->assertEquals($initialNumber, mysql_num_rows($results));
    }

    public function testSharingToOwner(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'albumId' => '1',
            'userId' => '1'
        );

        $results = $db->query('SELECT * FROM usersToAlbums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/shareAlbum.php');
        $results = $db->query('SELECT * FROM usersToAlbums');
        $this->assertEquals($initialNumber, mysql_num_rows($results));
    }

    public function testSharingToUserThatDoesNotExist(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'albumId' => '1',
            'userId' => '100'
        );

        $results = $db->query('SELECT * FROM usersToAlbums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/shareAlbum.php');
        $results = $db->query('SELECT * FROM usersToAlbums');
        $this->assertEquals($initialNumber, mysql_num_rows($results));
    }

    public function testSharingAlbumThatDoesNotExist(){
        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'albumId' => '100',
            'userId' => '1'
        );

        $results = $db->query('SELECT * FROM usersToAlbums');
        $initialNumber = mysql_num_rows($results);
        include(dirname(__FILE__).'/../pages/view/shareAlbum.php');
        $results = $db->query('SELECT * FROM usersToAlbums');
        $this->assertEquals($initialNumber, mysql_num_rows($results));
    }
}
?>