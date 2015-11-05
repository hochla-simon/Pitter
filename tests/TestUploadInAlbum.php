<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 5/11/15
 * Time: 10:36
 */
class TestUploadInAlbum extends PHPUnit_Extensions_SeleniumTestCase
{
public static $browsers = array(
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
);

protected function setUp()
{
    $this->setBrowserUrl('http://localhost//view/index.html?id=1/');
}

public function testTitle()
{
    $this->open('http://localhost//view/index.html?id=1/');
    $this->assertTitle('Photos | Pitter');

    $name = $this->byName( 'sender_name' );
}
}


