<?php
class TestUser extends PHPUnit_Framework_TestCase {

    public function testEditProfile(){

        // Initialization
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));

        $_SESSION = array(
            'id' => $user['id']
        );
        $_GET = array(
            'page' => 'users/profile.php'
        );

        // Test empty POST
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), 3);


        // Successful editing of user
        $_POST = array(
            'firstName' => 'Jane2',
            'lastName' => 'Doe2',
            'email' => 'john@example.org',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully saved", $message);
        $this->assertEquals(count($errors), 0);
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'john@example.org'"));
        $this->assertEquals($user['firstName'], 'Jane2');
        $this->assertEquals($user['lastName'], 'Doe2');

        // Test not matching passwords
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'john@example.org',
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
            'email' => 'john@example.org',
            'password' => 'test',
            'password2' => 'test',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("successfully saved", $message);
        $this->assertEquals(count($errors), 0);
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'john@example.org'"));
        $this->assertEquals($user['firstName'], 'Jane');
        $this->assertEquals($user['lastName'], 'Doe');
    }

    public function testLogin(){

        // Initialization
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        // Test empty POST
        $_POST = array(
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), count($fields));

        // Test Login with wrong email
        $_POST = array(
            'email' => 'wrong@example.org',
            'password' => 'test',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("No user having these credentials", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Test Login with wrong password
        $_POST = array(
            'email' => 'john@example.org',
            'password' => 'wrong',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("No user having these credentials", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Successful login
        $_POST = array(
            'email' => 'john@example.org',
            'password' => 'test',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(count($errors), 0);
        $this->assertEquals($_SESSION['id'], $user['id']);
        $this->assertContains('users/profile.html', $_POST['redirect']);

        // Successful login width redirect
        $_POST = array(
            'email' => 'john@example.org',
            'password' => 'test',
            'redirect' => 'administration/index.html',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
        $this->assertEquals(count($errors), 0);
        $this->assertEquals($_SESSION['id'], $user['id']);
        $this->assertEquals('administration/index.html', $_POST['redirect']);
    }

    public function testRecoverPassword()
    {

        // Initialization
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/recoverPassword.php'
        );

        // Test empty POST
        $_POST = array(
            'submit' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $this->assertContains("Please provide", $errors[0]);
        $this->assertEquals(count($errors), count($fields));

        // Test password recovery with wrong email
        $_POST = array(
            'email' => 'wrong@example.org',
            'submit' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $this->assertContains("No user having this email address", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Test successful password recovery
        $_POST = array(
            'email' => 'john@example.org',
            'submit' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $this->assertContains("successfully sent via email", $message);
        $this->assertEquals(count($errors), 0);
    }

    public function testRegister(){

        // Initialization
        $_SESSION['id'] = 1;
        $_GET = array(
            'page' => 'users/register.php'
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

        // Test creation with short password
        $existingUser = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));
        $_POST = array(
            'firstName' => 'Jim',
            'lastName' => 'Doe',
            'email' => $existingUser['email'],
            'password' => 'test',
            'retypepassword' => '1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("at least 6 characters", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Test creation with not matching passwords
        $_POST = array(
            'firstName' => 'Jim',
            'lastName' => 'Doe',
            'email' => $existingUser['email'],
            'password' => 'test1234',
            'retypepassword' => '1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("Passwords don't match", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Test creation of already used email address
        $_POST = array(
            'firstName' => 'Jim',
            'lastName' => 'Doe',
            'email' => $existingUser['email'],
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("user having this email address", $errors[0]);
        $this->assertEquals(count($errors), 1);

        // Successful creation of user
        $_POST = array(
            'firstName' => 'Jim',
            'lastName' => 'Doe',
            'email' => 'jim.doe@example.org',
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');
        $this->assertContains("succesfully registered", $message);
        $this->assertEquals(count($errors), 0);
        $newUser = mysql_fetch_assoc($db->query("select * from users where email = 'jim.doe@example.org'"));
        $this->assertNotEmpty($newUser['id']);
    }
}
