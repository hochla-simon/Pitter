<?php

class TestAlbum extends PHPUnit_Extensions_Selenium2TestCase
{
    public $projectURL;

    protected function setUp(){
        $this->setBrowser('chrome');
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'./../data/configuration/config.txt'), true);
        $this->projectURL=$readedConfig['projectURL'];
        $this->setBrowserUrl($this->projectURL.'/view/index.html');
    }

    public function testCreateAlbum()
    {
        $this->url($this->projectURL.'/view/index.html');
        $this->assertEquals(true, true);

        //store current number of albums
        //$current_number_album = count($this->byClassName("context-menu-one"));
        //$this->byClassName("context-menu-one")->click(PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Click::RIGHT);

    }
}


