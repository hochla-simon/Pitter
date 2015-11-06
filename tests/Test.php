<?php

require 'MyTestCase.php';
require 'TestConfiguration.php';
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/10/15
 * Time: 21:28
 */
class Test extends PHPUnit_Framework_TestCase
{
    public function testMyTestCase()
    {
        $mytestcase = new MyTestCase();
        $mytestcase->testReadDatabase();
    }

    public function testConfiguration() {
        $test = new TestConfiguration();
        $test->testInstallation();
        $test->testSettings();
    }

    public function testDeleteTestConfiguration() {
        unlink(dirname(__FILE__).'/../data/configuration/config.txt');
    }
}
