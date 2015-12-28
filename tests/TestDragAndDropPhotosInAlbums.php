<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3. 12. 2015
 * Time: 14:49
 */
class TestDragAndDropPhotosInAlbums extends PHPUnit_Extensions_Selenium2TestCase
{
    public $email = ''; //Admin email here
    public $password = ''; //Admin password here
    public $projectURL;
    public $testAlbumName = '#%$¤testNewAlbum¤$%';
    public $testChildAlbumName = '%$¤testChildNewAlbum¤$%';


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

    protected function waitUntilNoProgressBar() {
        while(count($this->elements($this->using('css selector')->value('#myDropzone > div.dz-preview.dz-file-preview')))!=0){
            sleep(1);
        }
    }

    protected function addTestPhotos($albumId) {
        $_POST = array(
            'albumId' => $albumId
        );
        //Transforming the hidden field in something that can be seen in order to be able interact with it with Selenium
        $javaScriptCode = "var elemForm = $.find('input.dz-hidden-input')[0];elemForm.style.visibility='visible';elemForm.style.height=\"100px\"; elemForm.style.width=\"100px\";";
        $this->execute(    array(
            'script' => $javaScriptCode,
            'args'   => array()
        ));


        $hiddenInput =$this->byCssSelector('input.dz-hidden-input');
        //Sending the file path, this trigers the same mehtod as dropping a file on the dropzone
        $pathFile1 = dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'uploadTest'.DIRECTORY_SEPARATOR.'flamingos.jpg';
        $pathFile2 = dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'uploadTest'.DIRECTORY_SEPARATOR.'hypo.jpg';
        $pathFile3 = dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'uploadTest'.DIRECTORY_SEPARATOR.'flamingos.jpg';
        $hiddenInput ->value($pathFile1."\n".$pathFile2."\n".$pathFile3);


        sleep(2);
        $this->waitUntilNoProgressBar();
        sleep(2);
        $this->url($this->projectURL.'/view/index.html?id=' . $albumId);
    }

    protected function addTestAlbum() {

        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');
        /*
        $_POST = array(
            'Save' => true,
            'name' => 'test',
            'parentAlbumId' => '1',
            'description' => ''
        );

        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        */

        $this->url($this->projectURL.'/view/albumCreate.html?parentId=1');
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


    public function testDragAndDropPhotoIntoRootAlbum() {
        $_SESSION['id'] = 1;
        $_GET = array(
            'parentId' => ''
        );
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $this->url($this->projectURL.'/users/login.html');
        $this->login();
        $this->url($this->projectURL.'/view/index.html');

        $testAlbumId = $this->addTestAlbum();

        /*
        sleep(2);
        $this->byCssSelector('#albumsContainer > ul > li > img')->click();
        sleep(2);
        $this->byXPath('//*[@id="albumsContainer"]/ul/li/ul/li[1]/a/span')->click();
        sleep(3);
        */

        $this->url($this->projectURL.'/view/index.html?id=' . $testAlbumId);
        $this->addTestPhotos($testAlbumId);

        $photos = $this->elements($this->using('css selector')->value('#photos > a'));

        $album = $db->query('SELECT * FROM imagesToAlbums WHERE albumId="' . $testAlbumId . '"');
        $old_number_photos = mysql_num_rows($album);

        $result = $db->query('SELECT * FROM imagesToAlbums WHERE albumId="1"');
        $old_root_number_photos = mysql_num_rows($result);

        $photos = $this->elements($this->using('css selector')->value('.draggablePhoto'));
        $numberOfPhotos = count($photos);

        // The photo to move is third from the end? last?
        $index = $numberOfPhotos - 1;
        $photo = $photos[$index];
        $target = $this->byCssSelector('#albumsContainer > ul > li:nth-child(2) > a');
        sleep(3);

        $this->moveto($photo);
        sleep(2);

        $this->buttondown();
        $this->moveto($target);
        sleep(3);
        $this->buttonup();

        sleep(2);

        $album = $db->query('SELECT * FROM imagesToAlbums WHERE albumId="' . $testAlbumId . '"');
        $new_number_photos = mysql_num_rows($album);

        $result = $db->query('SELECT * FROM imagesToAlbums WHERE albumId="1"');
        $new_root_number_photos = mysql_num_rows($result);

        $this->assertEquals($new_number_photos,$old_number_photos - 1);
        $this->assertEquals($new_root_number_photos,$old_root_number_photos + 1);


        sleep(2);

//        $photo = end($this->elements($this->using('css selector')->value('.draggablePhoto')));
//        $photoId = $photo->attribute('data-id');
//        $this->url($this->projectURL.'/view/photoDelete.html?id=' . $photoId);;
//        $this->byId('selectAll')->click();
//        $this->byClassName('submit')->click();

        $this->removeTestAlbum($testAlbumId);
    }
}
