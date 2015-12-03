<?php
class TestAlbumTree extends PHPUnit_Extensions_Selenium2TestCase {
    public $email = ''; //Admin email here
    public $password = ''; //Admin password here
    public $testAlbumName = '%$¤testNewAlbum¤$%';
    public $projectURL;

    protected function setUp() {
        $this->setBrowser('chrome');
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $this->projectURL=$readedConfig['projectURL'];
        $this->setBrowserUrl($this->projectURL.'/view/index.html');
    }

    protected function login() {
        $this->url($this->projectURL.'/users/login.html');
        $this->byId('setting_email')->value($this->email);
        $this->byId('setting_password')->value($this->password);
        $this->byClassName('submit')->click();
    }

    protected function addTestAlbum() {
        $this->url($this->projectURL.'/view/albumCreate.html?parentId=1');
        $this->byId('name')->value($this->testAlbumName);
        $this->byClassName('submit')->click();
    }

    protected function removeTestAlbum($albumId) {
        $this->url($this->projectURL.'/view/albumDelete.html?id=' . $albumId);
        $this->byClassName('submit')->click();
    }

    public function testRootAlbum() {
        $this->login();
        $this->url($this->projectURL.'/view/index.html');

        $this->assertEquals('ROOT', $this->byCssSelector('.droppableAlbum.active span')->text());
    }

    public function testToggleArrow() {
        $this->login();
        $this->addTestAlbum();

        $this->byClassName('toggleArrow')->click();
        $albums = $this->elements($this->using('css selector')->value('.droppableAlbum span'));
        $count = 0;
        $index = 0;
        foreach($albums as $album) {
            if ($album->text() == $this->testAlbumName) {
                $index = $count;
            }
            $count++;
        }
        $this->assertEquals($this->testAlbumName, $albums[$index]->text());

        $this->byClassName('toggleArrow')->click();
        $this->assertEquals('', $albums[$index]->text());

        $this->byClassName('toggleArrow')->click();
        $albumLinks = $this->elements($this->using('css selector')->value('.droppableAlbum'));
        $albumLink = $albumLinks[$index]->attribute('href');
        $albumId = substr($albumLink, strrpos($albumLinks[$index]->attribute('href'), '=') + 1);
        $this->removeTestAlbum($albumId);
    }

    public function testNewAlbum() {
        $this->login();
        $this->addTestAlbum();

        $this->byClassName('toggleArrow')->click();
        $albums = $this->elements($this->using('css selector')->value('.droppableAlbum span'));
        $count = 0;
        $index = 0;
        foreach($albums as $album) {
            if ($album->text() == $this->testAlbumName) {
                $index = $count;
            }
            $count++;
        }
        $this->assertEquals($this->testAlbumName, $albums[$index]->text());

        $albumLinks = $this->elements($this->using('css selector')->value('.droppableAlbum'));
        $albumLink = $albumLinks[$index]->attribute('href');
        $albumId = substr($albumLink, strrpos($albumLinks[$index]->attribute('href'), '=') + 1);
        $this->removeTestAlbum($albumId);

        $this->byClassName('toggleArrow')->click();
        $albums = $this->elements($this->using('css selector')->value('.droppableAlbum span'));
        $count = 0;
        $index = 0;
        foreach($albums as $album) {
            if ($album->text() == $this->testAlbumName) {
                $index = $count;
            }
            $count++;
        }
        // The test album has been removed so $index will be 0 and the asserted album will be the ROOT album
        $this->assertEquals('ROOT', $albums[$index]->text());
    }
}
?>

