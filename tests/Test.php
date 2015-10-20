<?php

require 'MyTestCase.php';
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


}
