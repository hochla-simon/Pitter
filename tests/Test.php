<?php
require 'DataBaseTesting.php';
require 'TestConfiguration.php';
require 'TestImageUpload.php';
require 'TestUserAdministration.php';
require 'TestAlbum.php';

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/10/15
 * Time: 21:28
 */
class Test extends PHPUnit_Framework_TestCase
{
    /*public function testAll(){
        @unlink(dirname(__FILE__).'/../data/configuration/config.txt');
        $test = new DataBaseTesting();
        $test->testReadDatabase();
        $test = new TestConfiguration();
        $test->testInstallation();
        $test->testSettings();
        $test = new TestImageUpload();
        $test->testUpload();
        @unlink(dirname(__FILE__).'/../data/configuration/config.txt');
    }*/

    public function testInitialDeletionOfConfiguration() {
        @unlink(dirname(__FILE__).'/../data/configuration/config.txt');
    }

    public function testDataBase()
    {
        $mytestcase = new DataBaseTesting();
        $mytestcase->testReadDatabase();
    }

    public function testConfiguration() {
        @unlink(dirname(__FILE__).'/../data/configuration/config.txt');
        $test = new TestConfiguration();
        $test->testInstallation();
        $test->testSettings();
    }

    public function testUpload() {
        $test = new TestImageUpload();
        $test->testUpload();
    }

    public function testUserAdministration() {
        $test = new TestUserAdministration();
        $test->testCreateUser();
        $test->testEditUser();
        $test->testEnableUser();
        $test->testDeleteUser();
    }

    public function testAlbum() {
        $test = new TestAlbum();
        $test->testCreation();
        $test->testEdit();
    }

    public function testFinalDeletionOfConfiguration() {
        @unlink(dirname(__FILE__).'/../data/configuration/config.txt');
    }
}
