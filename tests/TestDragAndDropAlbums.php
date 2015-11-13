<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 9. 11. 2015
 * Time: 20:07
 */
class TestDragAndDropAlbums extends PHPUnit_Extensions_Selenium2TestCase
{
    protected function setUp()
    {
        $this->setBrowser('chrome');
        $this->setBrowserUrl('http://localhost/view/index.html?id=1/');
    }

    public function testMakeSiblingAlbumFromChildAlbum() {
        $this->url('http://localhost//view/index.html');

        $siblingAlbumsCount = count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li')));
        $childAlbumsCount = count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul')));


        $srcDrag=$this->byCssSelector('#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li');
        $targetDrop=$this->byCssSelector('#albumsContainer > ul > li > ul');

        $this->moveto($srcDrag);
        $this->buttondown();
        $this->moveto($targetDrop);
        $this->buttonup();

        sleep(2);

        $newChildAlbumsCount = $childAlbumsCount - 1;
        $newSiblingAlbumsCount = $siblingAlbumsCount + 1;

//        $this->assertEquals($newSiblingAlbumsCount, count($this->elements($this->using(
//            'css selector')->value('#albumsContainer > ul > li > ul > li'))));
//        $this->assertEquals($newChildAlbumsCount, count($this->elements($this->using('css selector')->value(
//            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul'))));
    }

    public function testMakeChildAlbumFromSiblingAlbum() {
        $this->url('http://localhost//view/index.html');

        $siblingAlbumsCount = count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li')));
        $childAlbumsCount = count($this->elements($this->using('css selector')->value(
            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li')));

        $srcDrag=$this->byCssSelector('#albumsContainer > ul > li > ul > li:nth-child(2)');
        $targetDrop=$this->byCssSelector('#albumsContainer > ul > li > ul > li:nth-child(1) > ul');

        $this->moveto($srcDrag);
        $this->buttondown();
        $this->moveto($targetDrop);
        $this->buttonup();

        sleep(2);

        $newSiblingAlbumsCount = $siblingAlbumsCount - 1;
        $newChildAlbumsCount = $childAlbumsCount + 1;

//        $this->assertEquals($newSiblingAlbumsCount, count($this->elements($this->using('css selector')->value(
//            '#albumsContainer > ul > li > ul > li'))));
//        $this->assertEquals($newChildAlbumsCount, count($this->elements($this->using('css selector')->value(
//            '#albumsContainer > ul > li > ul > li:nth-child(1) > ul > li'))));
    }
}