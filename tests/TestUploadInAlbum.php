<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 5/11/15
 * Time: 10:36
 */
class TestUploadInAlbum extends PHPUnit_Extensions_Selenium2TestCase
{
/*public static $browsers = array(
    array(
        'name'    => 'Linux Firefox',
        'browser' => '*firefox',
        'host'    => 'localhost',
        'port'    => 4444,
        'timeout' => 30000,
    ),
    array(
        'name'    => 'Linux Chrome',
        'browser' => '*chrome',
        'host'    => 'localhost',
        'port'    => 4444,
        'timeout' => 30000,
    )
);*/

protected function setUp()
{
    $this->setBrowser('firefox');
    $this->setBrowserUrl('http://localhost//view/index.html?id=1/');
}

public function testTitle()
{
    /*$this->open('http://localhost//view/index.html?id=1/');
    $this->assertTitle('Photos | Pitter');

    $name = $this->byName( 'input.dz-hidden-input' );
    echo $name;*/
    $this->url('http://localhost//view/index.html?id=1');

    // check the value
    $this->assertEquals( 'image/jpeg,image/png,image/gif', $this->byCssSelector('input.dz-hidden-input')->attribute('accept'));

    $this->hiddenInput = $this->byCssSelector('input.dz-hidden-input');
    //$elem=$this->byCssSelector('input.dz-hidden-input');


    $javaScriptCode = "var elemForm = $.find('input.dz-hidden-input')[0];elemForm.style.visibility='visible';elemForm.style.height=\"100px\"; elemForm.style.width=\"100px\";";

    $this->execute(    array(
        'script' => $javaScriptCode,
        'args'   => array()
    ));
    $this->hiddenInput =$this->byCssSelector('input.dz-hidden-input');
    $this->hiddenInput ->value('/home/daniel/Pictures/Pitufos/chevre.png');
    //$elem->value('/home/daniel/Pictures/Pitufos/chevre.png');
    /*$this->sendKeys($elem,"/path/to/file");*/
}
}


