<?php
require 'DataBaseTesting.php';
require 'TestConfiguration.php';
require 'TestImageUpload.php';
require 'TestUser.php';
require 'TestUserAdministration.php';
require 'TestPermissions.php';
require 'TestAlbum.php';
require 'TestImage.php';
require 'TestMovePhoto.php';
require 'TestCopyPhoto.php';
require 'TestUserBasedSharing.php';

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/10/15
 * Time: 21:28
 */
class Test extends PHPUnit_Framework_TestCase
{
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
        $test->testThumbnails();
        $test->cleanup();
    }

    public function testUser() {
        $test = new TestUser();
        $test->testEditProfile();
        $test->testLogin();
        $test->testRecoverPassword();
        $test->testRegister();
    }

    public function testUserAdministration() {
        $test = new TestUserAdministration();
        $test->testCreateUser();
        $test->testEditUser();
        $test->testEnableUser();
        $test->testLogin();
        $test->testDeleteUser();
    }

    public function testPermissions() {
        $test = new TestPermissions();
        $test->testPermissions();
    }

    public function testAlbum() {
        $test = new TestAlbum();
        $test->testCreation();
        $test->testEdit();
        $test->testMove();
        $test->testCopy();
        $test->testDelete();
    }

    public function testImage() {
        $test = new testImage();
        $test->testEdit();
        $test->testDelete();
    }

    public function testImageCopy() {
        $test = new TestCopyPhoto();
        $test->setUpBeforeClass();
        $test->testCopy();
        $test->tearDownAfterClass();
    }

    public function testImageMove() {
        $test = new TestMovePhoto();
        $test->setUpBeforeClass();
        $test->testMove();
        $test->tearDownAfterClass();
    }

    public function testUserBasedSharing() {
        $test = new TestUserBasedSharing();
        $test->testSuccesfulSharing();
        $test->testSharingSameAlbumToSameUser();
        $test->testSharingAlbumThatUserDoesNotOwn();
        $test->testSharingToOwner();
        $test->testSharingToUserThatDoesNotExist();
        $test->testSharingAlbumThatDoesNotExist();
    }

    public function testFinalDeletionOfConfiguration() {
        @unlink(dirname(__FILE__).'/../data/configuration/config.txt');
    }
}
