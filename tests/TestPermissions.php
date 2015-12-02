<?php
class TestPermissions extends PHPUnit_Framework_TestCase {
    public function testPermissions(){

        // Initialization
        // Level 3 = Administrator, 1 = Logged in, 0 = Anonymous
        $pages = array(
            'administration/createUser.html' => 3,
            'administration/editUser.html' => 3,
            'administration/index.html' => 3,
            'administration/settings.html' => 3,
            'administration/users.html' => 3,
            'common/error403.html' => 0,
            'common/error404.html' => 0,
            'common/home.html' => 0,
            'slideshow/index.html' => 1,
            'upload/upload.html' => 1,
            'users/logout.html' => 0,
            'users/profile.html' => 1,
            'users/recoverPassword.html' => 0,
            'users/register.html' => 0,
            'view/albumCopy.html' => 1,
            'view/albumCreate.html' => 1,
            'view/albumDelete.html' => 1,
            'view/albumDragDropSave.html' => 1,
            'view/albumEdit.html' => 1,
            'view/albumMove.html' => 1,
            'view/autoloadProcess.html' => 1,
            'view/image.html' => 1,
            'view/index.html' => 1,
            'view/photoCopy.html' => 1,
            'view/photoEdit.html' => 1,
            'view/photoMove.html' => 1,
            'view/photoMoveRightClick.html' => 1,
            'view/photoOrderSave.html' => 1,
            'view/photoView.html' => 1
        );

        $phpunit = array(
            'isTest' => true
        );

        // Testing as 0 = Anonymous
        $_SESSION['id'] = '';
        foreach($pages as $url => $level){
            $_GET['page'] = str_replace('.html', '.php', $url);
            include(dirname(__FILE__).'/../index.php');
            if($level > 0){
                $this->assertTrue(preg_match('!(<h2>Login</h2>|Unauthorized)!i', $site['content']) == 1);
            }
            else {
                $this->assertFalse(preg_match('!(<h2>Login</h2>|Unauthorized)!i', $site['content']) == 1);
            }
        }

        // Testing as 3 = Administrator
        $_SESSION['id'] = 1;
        foreach($pages as $url => $level){
            $_GET['page'] = str_replace('.html', '.php', $url);
            include(dirname(__FILE__).'/../index.php');
            $this->assertFalse(preg_match('!(<h2>Login</h2>|Unauthorized)!i', $site['content']) == 1);
        }

    }
}
