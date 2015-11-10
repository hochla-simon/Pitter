<?php
class TestConfiguration extends PHPUnit_Framework_TestCase {
    public function testInstallation(){

        // Initialization
        $_GET = array(
            'page' => 'administration/settings.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        // Test empty installation POST
        $config['installed'] = false;
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), count($fields));

        // Test wrong database settings
        $config['installed'] = false;
        $_POST = array(
            'databaseHost' => 'localhost',
            'databaseUser' => 'pitter',
            'databasePassword' => 'wrongPassword',
            'databaseName' => 'pitter',
            'projectName' => 'Example',
            'projectURL' => 'http://www.example.org',
            'slogan' => 'Example slogan',
            'copyright' => 'Example copyright',
            'homeContent' => 'Example home content',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(count($errors), 1);
        $this->assertEquals($errors[0], 'Could not connect to database.');
        $this->assertNotEquals($config['installed'], true);

        // Test correct installation
        $config['installed'] = false;
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $dataToPost = array('submit' => true);

        $_POST = array_merge($readedConfig, $dataToPost);

        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(count($errors), 0);
        $this->assertContains('Installation successful.', $message);
        $this->assertEquals($config['installed'], true);
        foreach($fields as $key => $field) {
            $this->assertEquals($config[$key], $_POST[$key]);
        }
    }

    public function testSettings(){

        // Initialization
        $_GET = array(
            'page' => 'administration/settings.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        // Test empty POST of settings
        $config['installed'] = true;
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), count($fields));

        // Test wrong database settings
        $config['installed'] = true;
        $_POST = array(
            'databaseHost' => 'localhost',
            'databaseUser' => 'pitter',
            'databasePassword' => 'wrongPassword',
            'databaseName' => 'pitter',
            'projectName' => 'Example3',
            'projectURL' => 'http://www.example.org3',
            'slogan' => 'Example slogan3',
            'copyright' => 'Example copyright3',
            'homeContent' => 'Example home content3',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(count($errors), 1);
        $this->assertEquals($errors[0], 'Could not connect to database.');
        $this->assertEquals($config['installed'], true);

        // Test correct settings successful
        $config['installed'] = true;
        $readedConfig = json_decode(@file_get_contents(dirname(__FILE__).'/data/confForTests.txt'), true);
        $dataToPost = array('submit' => true);

        $_POST = array_merge($readedConfig, $dataToPost);

        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(count($errors), 0);
        $this->assertContains('Changes successfully saved.', $message);
        $this->assertEquals($config['installed'], true);
        foreach($fields as $key => $field) {
            $this->assertEquals($config[$key], $_POST[$key]);
        }
    }
}
