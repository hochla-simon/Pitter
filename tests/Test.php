<?php

require 'DataBaseTesting.php';
require 'TestConfiguration.php';
require 'TestImageUpload.php';
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/10/15
 * Time: 21:28
 */
class Test extends PHPUnit_Framework_TestCase
{
    public function testDataBase()
    {
        $mytestcase = new DataBaseTesting();
        $mytestcase->testReadDatabase();
    }

    public function testConfiguration() {
        $test = new TestConfiguration();
        $test->testInstallation();
        $test->testSettings();
    }

    public function testUpload() {
        $test = new TestImageUpload();
        $test->testUpload();
    }

    public function testDeleteTestConfiguration() {
        unlink(dirname(__FILE__).'/../data/configuration/config.txt');
    }
}
