<?php
class TestUserAdministration extends PHPUnit_Framework_TestCase {
    public function testCreateUser(){

        // Initialization
        $_SESSION['id'] = 1;
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
        $existingUser = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));
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
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.org',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully created", $message);
        $this->assertEquals(count($errors), 0);
        $newUser = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $this->assertNotEmpty($newUser['id']);
    }

    public function testEditUser(){

        // Initialization
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));

        $_GET = array(
            'page' => 'administration/editUser.php',
            'id' => $user['id']
        );

        // Test empty installation POST
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), 3);

        // Test editing of already used email address
        $existingUser = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => $existingUser['email'],
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("user having this email address", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Successful editing of user
        $_POST = array(
            'firstName' => 'Jane2',
            'lastName' => 'Doe2',
            'email' => 'jane.doe@example.org',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully saved", $message);
        $this->assertEquals(count($errors), 0);
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $this->assertEquals($user['firstName'], 'Jane2');
        $this->assertEquals($user['lastName'], 'Doe2');

        // Test not matching passwords
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.org',
            'password' => 'test',
            'password2' => 'test2',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("do not match", $errors[0]);

        // Successful editing of user
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.org',
            'password' => 'test',
            'password2' => 'test',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully saved", $message);
        $this->assertEquals(count($errors), 0);
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $this->assertEquals($user['firstName'], 'Jane');
        $this->assertEquals($user['lastName'], 'Doe');
    }

    public function testEnableUser(){

        // Initialization
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $db->query("update users set enabled = '0' where email = 'jane.doe@example.org'");

        $_GET = array(
            'page' => 'administration/users.php',
            'action' => 'enable'
        );

        // Test without specifying the user
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Test enabling of already enabled user (administrator)
        $_GET['id'] = 1;
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Test enabling of not existing user
        $_GET['id'] = 10000;
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Successful enabling
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $_GET['id'] = $user['id'];
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully enabled", $site['content']);
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $this->assertEquals($user['enabled'], 1);
    }

    public function testLogin(){

        // Initialization
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $db->query("update users set enabled = '0' where email = 'jane.doe@example.org'");

        $_GET = array(
            'page' => 'administration/users.php',
            'action' => 'login'
        );

        // Test without specifying the user
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Test login of not existing user
        $_GET['id'] = 10000;
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Successful login
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $_GET['id'] = $user['id'];
        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals($_SESSION['id'], $user['id']);
    }

    public function testDeleteUser(){

        // Initialization
        $_GET = array(
            'page' => 'administration/users.php',
            'action' => 'delete'
        );
        $phpunit = array(
            'isTest' => true
        );

        // Test without specifying the user
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Test deletion of administrator
        $_GET['id'] = 1;
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Test deletion of not existing user
        $_GET['id'] = 10000;
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("An error occurred", $site['content']);

        // Successful deletion
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $_GET['id'] = $user['id'];
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully deleted", $site['content']);
        $deletedUser = mysql_fetch_assoc($db->query("select * from users where email = 'jane.doe@example.org'"));
        $this->assertEquals($deletedUser['id'], null);
    }
}
