<?php
class TestAlbumTree extends PHPUnit_Extensions_Selenium2TestCase {
    public $email = ''; //Admin email here
    public $password = ''; //Admin password here
    public $projectURL;

    protected function setUp() {
        $this->setBrowser('chrome');
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $this->projectURL=$readedConfig['projectURL'];
        $this->setBrowserUrl($this->projectURL.'/view/index.html');
    }

    protected function login() {
        $this->byId('setting_email')->value($this->email);
        $this->byId('setting_password')->value($this->password);
        $this->byClassName('submit')->click();
    }

    public function testRootAlbum() {
        $this->url($this->projectURL.'/users/login.html');
        $this->login();
        $this->url($this->projectURL.'/view/index.html');

        $this->assertEquals('ROOT', $this->byCssSelector('.droppableAlbum.active span')->text());
        sleep(5);
    }
}
?>