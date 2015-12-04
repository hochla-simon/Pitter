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
    public $email = ''; //Admin email here
    public $password = ''; //Admin password here
    public $projectURL;

    protected function setUp()
    {
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

    protected function waitUntilFullyLoad(){

        $isdone = $this->execute(array('script' => "return document.readyState", 'args'   => array()));
        var_dump($isdone);
        while(!$isdone){
            usleep(500);
            $isdone = $this->execute(array('script' => "return document.readyState", 'args'   => array()));
            var_dump($isdone);
        }
    }
    protected function waitUntilNoProgressBar()
    {
        while(count($this->elements($this->using('css selector')->value('#myDropzone > div.dz-preview.dz-file-preview')))!=0){
            sleep(1);
        }

    }

    public function testSingleFileUpload()
    {

        $this->login();
        $this->url($this->projectURL.'/view/index.html');
        $webdriver = $this;


        //store current number of images
        $current_number_photos = count($this->elements($this->using('css selector')->value('#photos > a')));

        // check the value
        $this->assertEquals( 'image/jpeg,image/png,image/gif', $this->byCssSelector('input.dz-hidden-input')->attribute('accept'));

        /*Transforming the hidden field in something that can be seen in order to be able interact with it with Selenium*/
        $javaScriptCode = "var elemForm = $.find('input.dz-hidden-input')[0];elemForm.style.visibility='visible';elemForm.style.height=\"100px\"; elemForm.style.width=\"100px\";";
        $this->execute(    array(
            'script' => $javaScriptCode,
            'args'   => array()
        ));


        $hiddenInput =$this->byCssSelector('input.dz-hidden-input');
        /*Sending the file path, this trigers the same mehtod as dropping a file on the dropzone*/
        $hiddenInput ->value(dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'uploadTest'.DIRECTORY_SEPARATOR.'flamingos.jpg');

        sleep(2);
        $this->waitUntilNoProgressBar();
        sleep(2);

        //getting new number of tags
        $new_number_photos = count($this->elements($this->using('css selector')->value('#photos > a')));
        $this->assertEquals($new_number_photos,$current_number_photos+1);

        //checking that there is nothing left in dropzone
        $this->assertEquals(0,count($this->elements($this->using('css selector')->value('#myDropzone > div.dz-preview.dz-file-preview'))));
    }

    public function testMultipleFileUpload()
    {

        $this->login();
        $this->url($this->projectURL.'/view/index.html');


//store current number of images
        $current_number_photos = count($this->elements($this->using('css selector')->value('#photos > a')));
        echo "current number: ".$current_number_photos;

// check the value
        $this->assertEquals( 'image/jpeg,image/png,image/gif', $this->byCssSelector('input.dz-hidden-input')->attribute('accept'));

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
        $hiddenInput ->value($pathFile1."\n".$pathFile2);


        sleep(2);
        $this->waitUntilNoProgressBar();
        sleep(2);

//getting new number of tags
        $new_number_photos = count($this->elements($this->using('css selector')->value('#photos > a')));
        $this->assertEquals($current_number_photos+2, $new_number_photos);

//checking that there is nothing left in dropzone
        $this->assertEquals(0,count($this->elements($this->using('css selector')->value('#myDropzone > div.dz-preview.dz-file-preview'))));
    }

    public function testWrongFileType()
    {
        $this->login();
        $this->url($this->projectURL.'/view/index.html');


// check the value
        $this->assertEquals( 'image/jpeg,image/png,image/gif', $this->byCssSelector('input.dz-hidden-input')->attribute('accept'));

        //Transforming the hidden field in something that can be seen in order to be able interact with it with Selenium
        $javaScriptCode = "var elemForm = $.find('input.dz-hidden-input')[0];elemForm.style.visibility='visible';elemForm.style.height=\"100px\"; elemForm.style.width=\"100px\";";
        $this->execute(    array(
            'script' => $javaScriptCode,
            'args'   => array()
        ));




        $hiddenInput =$this->byCssSelector('input.dz-hidden-input');
        //Sending the file path, this trigers the same mehtod as dropping a file on the dropzone
        $hiddenInput ->value(dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'uploadTest'.DIRECTORY_SEPARATOR.'flamingos.NEF');


        $this->moveto($this->byCssSelector('#myDropzone > div.dz-preview.dz-file-preview.dz-error.dz-complete > div.dz-details'));

        sleep(3);

        $messageError = $this->byCssSelector("#myDropzone > div.dz-preview.dz-file-preview.dz-error.dz-complete > div.dz-error-message > span")->text();
        $this->assertEquals("only jpg, jpeg, png and gif are accepted",$messageError );
    }
}


