<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 9. 11. 2015
 * Time: 20:07
 */
class TestDragAndDropAlbums extends PHPUnit_Extensions_Selenium2TestCase
{

    public $email = ''; //Admin email here
    public $password = ''; //Admin password here
    public $testAlbumName = '%$¤testNewAlbum¤$%';
    public $projectURL;

    protected function login() {
        $this->url($this->projectURL.'/users/login.html');
        $this->byId('setting_email')->value($this->email);
        $this->byId('setting_password')->value($this->password);
        $this->byClassName('submit')->click();
    }

    protected function addTestAlbum($parentId) {

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
            'parentAlbumId' => $parentId,
            'description' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumCreate.php');


        $this->url($this->projectURL.'/view/albumCreate.html?parentId=' . $parentId);
        $this->byId('name')->value($this->testAlbumName);
        $this->byClassName('submit')->click();

        $album = $db->query('SELECT id FROM albums WHERE name="' . $this->testAlbumName .'"');
        if (mysql_num_rows($album) > 0) {
            //getting the id of the last added album with the name "test"
            while($row = mysql_fetch_assoc($album)) {
                $testAlbumId =  $row["id"];
            }
        }
        return $testAlbumId;
    }

    protected function removeTestAlbum($albumId) {
        $this->url($this->projectURL.'/view/albumDelete.html?id=' . $albumId);
        $this->byClassName('submit')->click();
    }

    protected function setUp()
    {
        global $config;

        $this->setBrowser('chrome');
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $this->projectURL=$readedConfig['projectURL'];
        $this->setBrowserUrl($this->projectURL.'/view/index.html');
    }

    public function testMakeSiblingAlbumFromChildAlbum() {
        $this->login();

        $testAlbumId = $this->addTestAlbum(1);
        $this->addTestAlbum($testAlbumId);

        $this->url($this->projectURL.'/view/index.html');

        $siblingAlbumsCount = count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li')));
        $childAlbumsCount = count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li')));


        sleep(2);
        $this->byCssSelector('#albumsContainer > ul > li > img')->click();
        sleep(2);
        $this->byCssSelector('#albumsContainer > ul > li > ul > li > img')->click();
        sleep(3);

        $srcDrag=$this->byCssSelector('#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li');
        $targetDrop=$this->byCssSelector('#albumsContainer > ul');

        sleep(2);
        $this->moveto($srcDrag);
        sleep(2);
        $this->buttondown();
        sleep(2);
        $this->moveto($targetDrop);
        sleep(2);
        $this->buttonup();
        sleep(2);

        $newChildAlbumsCount = $childAlbumsCount - 1;
        $newSiblingAlbumsCount = $siblingAlbumsCount + 1;

        $this->assertEquals($newSiblingAlbumsCount, count($this->elements($this->using(
            'css selector')->value('#albumsContainer > ul > li > ul > li'))));
        $this->assertEquals($newChildAlbumsCount, count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li'))));

        $this->removeTestAlbum($testAlbumId);
    }

//    public function testMakeChildAlbumFromSiblingAlbum() {
//        $this->login();
//
//        global $config;
//
//        $this->url($this->projectURL.'view/index.html');
//
//        $siblingAlbumsCount = count($this->elements($this->using('css selector')->value(
//            '#albumsContainer > ul > li > ul > li')));
//        $childAlbumsCount = count($this->elements($this->using('css selector')->value(
//            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li')));
//
//        $srcDrag=$this->byCssSelector('#albumsContainer > ul > li > ul > li:nth-child(2)');
//        $targetDrop=$this->byCssSelector('#albumsContainer > ul > li > ul > li:nth-child(1) > ul');
//
//        sleep(2);
//        $this->moveto($srcDrag);
//        sleep(2);
//        $this->buttondown();
//        sleep(2);
//        $this->moveto($targetDrop);
//        sleep(2);
//
//        $this->buttonup();
//
//        sleep(2);
//
//        $newSiblingAlbumsCount = $siblingAlbumsCount - 1;
//        $newChildAlbumsCount = $childAlbumsCount + 1;
//
////        $this->assertEquals($newSiblingAlbumsCount, count($this->elements($this->using('css selector')->value(
////            '#albumsContainer > ul > li > ul > li'))));
////        $this->assertEquals($newChildAlbumsCount, count($this->elements($this->using('css selector')->value(
////            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li'))));
//    }
}