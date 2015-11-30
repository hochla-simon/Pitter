<?php
class TestUserAdministration extends PHPUnit_Framework_TestCase {
    public function testCreateUser(){

        // Initialization
        $_GET = array(
            'page' => 'administration/createUser.php'
        );
        $phpunit = array(
            'isTest' => true
        );

        // Test empty installation POST
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), count($fields));

        // Test creation of already used email address
        $existingUser = mysql_fetch_assoc($db->query("select * from users limit 0,1"));
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => $existingUser['email'],
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("user having this email address", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Successful creation of user
        $_POST['email'] = 'jane.doe@example.org';
        include(dirname(__FILE__).'/../index.php');
        var_dump($site);
        $this->assertContains("successfully created", $message);
        $this->assertEquals(count($errors), 0);
    }
}
