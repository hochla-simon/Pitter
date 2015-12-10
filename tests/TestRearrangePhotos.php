<?php
class TestRearrangePhotos extends PHPUnit_Extensions_Selenium2TestCase {
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
        $this->url($this->projectURL.'/users/login.html');
        $this->byId('setting_email')->value($this->email);
        $this->byId('setting_password')->value($this->password);
        $this->byClassName('submit')->click();
    }

    protected function waitUntilNoProgressBar() {
        while(count($this->elements($this->using('css selector')->value('#myDropzone > div.dz-preview.dz-file-preview')))!=0){
            sleep(1);
        }
    }

    protected function addTestPhotos() {
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
        $this->url($this->projectURL.'/view/index.html');
    }

    protected function removeTestPhotos() {
        for ($x = 0; $x < 3; $x++) {
            $photo = end($this->elements($this->using('css selector')->value('.draggablePhoto')));
            $photoId = $photo->attribute('data-id');
            $this->url($this->projectURL.'/view/photoDelete.html?id=' . $photoId);;
            $this->byId('selectAll')->click();
            $this->byClassName('submit')->click();
        }
    }

    public function testMovingOutsideTheArea() {
        $this->login();
        $this->url($this->projectURL.'/view/index.html');

        $this->addTestPhotos();

        $photos = $this->elements($this->using('css selector')->value('.draggablePhoto'));
        $numberOfPhotos = count($photos);
        // The photo to move is second from the end
        $index = $numberOfPhotos - 2;
        $photo = $photos[$index];
        $target = $this->byId('footer');
        $photoId = $photo->attribute('data-id');

        $this->moveto($photo);
        $this->buttondown();
        $this->moveto($target);
        $this->buttonup();

        $photos2 = $this->elements($this->using('css selector')->value('.draggablePhoto'));
        $numberOfPhotos2 = count($photos2);
        // The moved photo remains second from the end
        $index2 = $numberOfPhotos2 - 2;
        $photo2 = $photos2[$index2];
        $photoId2 = $photo2->attribute('data-id');

        sleep(2);

        $this->assertEquals($numberOfPhotos, $numberOfPhotos2);
        $this->assertEquals($photoId, $photoId2);

        $this->removeTestPhotos();
    }

    public function testMovingInsideTheArea() {
        $this->login();
        $this->url($this->projectURL.'/view/index.html');

        $this->addTestPhotos();

        $photos = $this->elements($this->using('css selector')->value('.draggablePhoto'));
        $numberOfPhotos = count($photos);
        // The photo to move is third from the end
        $index = $numberOfPhotos - 3;
        $photo = $photos[$index];
        $target = $photos[$numberOfPhotos - 1];
        $photoId = $photo->attribute('data-id');

        $this->moveto($photo);
        $this->buttondown();
        $this->moveto($target);
        $this->buttonup();

        $photos2 = $this->elements($this->using('css selector')->value('.draggablePhoto'));
        $numberOfPhotos2 = count($photos2);
        // The moved photo is now the last
        $index2 = $numberOfPhotos2 - 1;
        $photo2 = $photos2[$index2];
        $photoId2 = $photo2->attribute('data-id');

        sleep(2);

        $this->assertEquals($numberOfPhotos, $numberOfPhotos2);
        $this->assertEquals($photoId, $photoId2);

        $this->removeTestPhotos();
    }
}
?>
