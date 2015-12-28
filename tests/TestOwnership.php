<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 4. 12. 2015
 * Time: 20:12
 */
class TestOwnership extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass(){

        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'users/register.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        // Test empty installation POST
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        //create first user (owner)
        $_POST = array(
            'firstName' => 'Jude',
            'lastName' => 'Doe',
            'email' => 'jude.doe@example.org',
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $db->query("update users set enabled='1' where email = 'jude.doe@example.org'");

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jude.doe@example.org' order by id asc limit 0,1"));

        $insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES
        ("-1", ' . $user['id'] . ',"ROOT", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(),"")';
        $db->query($insert_sql_string);

        //create second user (nonowner)
        $_POST = array(
            'firstName' => 'Alice',
            'lastName' => 'Doe',
            'email' => 'alice.doe@example.org',
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $db->query("update users set enabled='1' where email = 'alice.doe@example.org'");

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'alice.doe@example.org' order by id asc limit 0,1"));

        $insert_sql_string = 'INSERT INTO albums (parentAlbumId, ownerId, name, created, modified, description) VALUES
        ("-1", ' . $user['id'] . ', "ROOT", CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(),"")';
        $db->query($insert_sql_string);
    }

    public function loginAsAdministrator(){

        // Initialization
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));

        $_SESSION = array(
            'id' => $user['id']
        );
        $_GET = array(
            'page' => 'users/profile.php'
        );

        // Test empty POST
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        // Successful editing of user
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'john@example.org',
            'password' => 'test',
            'password2' => 'test',
            'submit' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        // Test empty POST
        $_POST = array(
            'login' => true
        );

        // Successful login
        $_POST = array(
            'email' => 'john@example.org',
            'password' => 'test',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
    }

    public function loginAsNonOwner(){
        // Initialization
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jude.doe@example.org' order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        // Test empty POST
        $_POST = array(
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');

        // Successful login
        $_POST = array(
            'email' => 'jude.doe@example.org',
            'password' => 'test1234',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
    }

    public function loginAsOwner(){
        // Initialization
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jude.doe@example.org' order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        // Test empty POST
        $_POST = array(
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');

        // Successful login
        $_POST = array(
            'email' => 'alice.doe@example.org',
            'password' => 'test1234',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
    }

//    public function testAlbumCreation(){
//        $_GET = array(
//            'parentId' => ''
//        );
//        $phpunit = array(
//            'isTest' => true
//        );
//
//        include(dirname(__FILE__).'/../index.php');
//
//        //Test album with name test
//        $_POST = array(
//            'Save' => true,
//            'name' => '!test!',
//            'parentAlbumId' => '-1',
//            'description' => ''
//        );
//        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
//    }
//
//    public function testCopy(){
//
//        $phpunit = array(
//            'isTest' => true
//        );
//
//        include(dirname(__FILE__).'/../index.php');
//
//        $results = $db->query('SELECT * FROM albums WHERE name="!test!"');
//        $albumTest = mysql_fetch_array($results);
//        $_GET = array(
//            'id' => $albumTest['id']
//        );
//
//        //Test copy album
//        $_POST = array(
//            'Save' => true,
//            'albumId' => $albumTest['id'],
//            'parentAlbumId' => '1'
//        );
//
//        include(dirname(__FILE__).'/../pages/view/albumCopy.php');
//
//
//
//        //Test copy album
//        $_POST = array(
//            'Save' => true,
//            'albumId' => $albumTest['id'],
//            'parentAlbumId' => '1'
//        );
//
//        include(dirname(__FILE__).'/../pages/view/albumCopy.php');
//    }

    public function testOwnerAccess() {

    }

    public function testNonOwnerAccess() {

    }

    public function testAdministratorAccess() {

    }
}
