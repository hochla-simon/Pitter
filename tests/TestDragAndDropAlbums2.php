<?php
class TestDragAndDropAlbums2 extends PHPUnit_Extensions_Selenium2TestCase {
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

    protected function addTestAlbums() {
        for ($x = 1; $x < 2; $x++) {
            $this->url($this->projectURL.'/view/albumCreate.html?parentId=1');
            $this->byId('name')->value($this->testAlbumName . $x);
            $this->byClassName('submit')->click();
        }
        $this->url($this->projectURL.'/view/albumCreate.html?parentId=48');
        $this->byId('name')->value($this->testAlbumName . $x);
        $this->byClassName('submit')->click();
    }

    protected function removeTestAlbums($albumId1, $albumId2) {
        $this->url($this->projectURL.'/view/albumDelete.html?id=' . $albumId1);
        $this->byClassName('submit')->click();

        $this->url($this->projectURL.'/view/albumDelete.html?id=' . $albumId2);
        $this->byClassName('submit')->click();
    }

    public function testMoveAlbumIn() {
        $this->login();
        $this->addTestAlbums();
        $this->url($this->projectURL.'/view/index.html');

        if ($this->byClassName('toggleArrow')->displayed()) {
            $this->byClassName('toggleArrow')->click();
        }

        $albumTexts = $this->elements($this->using('css selector')->value('.ui-sortable-handle span'));
        $count = 0;
        $index1 = 0;
        $index2 = 0;
        foreach($albumTexts as $albumText) {
            if ($albumText->text() == $this->testAlbumName . '1') {
                $index1 = $count;
            } else if ($albumText->text() == $this->testAlbumName . '2') {
                $index2 = $count;
            }
            $count++;
        }

        $movableAlbums = $this->elements($this->using('css selector')->value('.ui-sortable-handle'));
        $movableAlbum1 = $movableAlbums[$index1];
        $movableAlbum2 = $movableAlbums[$index2];
        $albumId1 = $movableAlbum1->attribute('data-id');
        $albumId2 = $movableAlbum2->attribute('data-id');
        $target = $this->byId('logo');

        $this->moveto($movableAlbum1);
        $this->buttondown();
        $this->moveto($target);
        $this->moveto($this->elements($this->using('css selector')->value('.ui-state-highlight'))[0]);
        $this->buttonup();

        sleep(2);
    }

    public function testMoveAlbumOut() {
        $this->login();
        $this->addTestAlbums();
        $this->url($this->projectURL.'/view/index.html');

        $toggleArrows = $this->elements($this->using('css selector')->value('.toggleArrow'));
        foreach($toggleArrows as $arrow) {
            if ($arrow->displayed()) {
                $arrow->click();
            }
        }

        $albumTexts = $this->elements($this->using('css selector')->value('.ui-sortable-handle span'));
        $count = 0;
        $index1 = 0;
        $index2 = 0;
        foreach($albumTexts as $albumText) {
            if ($albumText->text() == $this->testAlbumName . '1') {
                $index1 = $count;
            } else if ($albumText->text() == $this->testAlbumName . '2') {
                $index2 = $count;
            }
            $count++;
        }

        $movableAlbums = $this->elements($this->using('css selector')->value('.ui-sortable-handle'));
        $movableAlbum1 = $movableAlbums[$index1];
        $movableAlbum2 = $movableAlbums[$index2];
        $albumId1 = $movableAlbum1->attribute('data-id');
        $albumId2 = $movableAlbum2->attribute('data-id');
        $target = $this->byId('logo');

        $this->moveto($movableAlbum2);
        $this->buttondown();
        $this->moveto($target);
        $this->moveto($this->elements($this->using('css selector')->value('.ui-state-highlight'))[0]);
        $this->buttonup();

        sleep(2);
    }

    public function testMoveAlbumToFooter() {
        $this->login();
        $this->url($this->projectURL.'/view/index.html');

        if ($this->byClassName('toggleArrow')->displayed()) {
            $this->byClassName('toggleArrow')->click();
        }

        $albumTexts = $this->elements($this->using('css selector')->value('.ui-sortable-handle span'));
        $count = 0;
        $index1 = 0;
        $index2 = 0;
        foreach($albumTexts as $albumText) {
            if ($albumText->text() == $this->testAlbumName . '1') {
                $index1 = $count;
            } else if ($albumText->text() == $this->testAlbumName . '2') {
                $index2 = $count;
            }
            $count++;
        }

        $movableAlbums = $this->elements($this->using('css selector')->value('.ui-sortable-handle'));
        $movableAlbum1 = $movableAlbums[$index1];
        $movableAlbum2 = $movableAlbums[$index2];
        $albumId1 = $movableAlbum1->attribute('data-id');
        $albumId2 = $movableAlbum2->attribute('data-id');
        $target = $this->byId('footer');

        $this->moveto($movableAlbum1);
        $this->buttondown();
        $this->moveto($target);
        $this->buttonup();

        sleep(2);

        $movableAlbums2 = $this->elements($this->using('css selector')->value('.ui-sortable-handle'));
        $albumId12 = $movableAlbums2[$index1]->attribute('data-id');

        $this->assertEquals($albumId1, $albumId12);

        $this->removeTestAlbums($albumId1, $albumId2);
    }
}
?>

