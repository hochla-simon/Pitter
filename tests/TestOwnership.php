<?php

/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 4. 12. 2015
 * Time: 20:12
 */
class TestOwnership extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass(){

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

        //create first user (owner)
        $_POST = array(
            'firstName' => 'Jude',
            'lastName' => 'Doe',
            'email' => 'jude.doe@example.org',
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $db->query("update users set enabled='1' where email = 'jude.doe@example.org'");

        //create second user (nonowner)
        $_POST = array(
            'firstName' => 'Alice',
            'lastName' => 'Doe',
            'email' => 'alice.doe@example.org',
            'password' => 'test1234',
            'retypepassword' => 'test1234',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $db->query("update users set enabled='1' where email = 'alice.doe@example.org'");
    }

    private function loginAsNonOwner(){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'alice.doe@example.org' order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        // Test empty POST
        $_POST = array(
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'email' => 'alice.doe@example.org',
            'password' => 'test1234',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
    }

    private function loginAsOwner(){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jude.doe@example.org' order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        // Test empty POST
        $_POST = array(
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'email' => 'jude.doe@example.org',
            'password' => 'test1234',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
    }

    private function loginAsAdministrator(){
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

        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        // Successful editing of user
        $_POST = array(
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'john@example.org',
            'password' => 'test',
            'password2' => 'test',
            'submit' => true
        );

        include(dirname(__FILE__) . '/../index.php');
        $user = mysql_fetch_assoc($db->query("select * from users order by id asc limit 0,1"));

        $_GET = array(
            'page' => 'users/login.php'
        );

        $_POST = array(
            'login' => true
        );

        $_POST = array(
            'email' => 'john@example.org',
            'password' => 'test',
            'login' => true
        );

        include(dirname(__FILE__).'/../index.php');
    }

    public function testAlbumCreation($albumId, $name, $denied){
        $_GET = array(
            'parentId' => $albumId
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'name' => $name,
            'parentAlbumId' => $albumId,
            'description' => ''
        );
        include(dirname(__FILE__).'/../pages/view/albumCreate.php');
        $this->assertEquals($denied, $accessDenied);
    }

    private function testAlbumCopy($albumId, $parentAlbumId, $denied){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $albumId
        );

        $_POST = array(
            'Save' => true,
            'albumId' => $albumId,
            'parentAlbumId' => $parentAlbumId
        );

        include(dirname(__FILE__).'/../pages/view/albumCopy.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testAlbumMove($albumId, $parentAlbumId, $denied){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $albumId
        );

        $_POST = array(
            'Save' => true,
            'albumId' => $albumId,
            'parentAlbumId' => $parentAlbumId
        );

        include(dirname(__FILE__).'/../pages/view/albumMove.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testAlbumDelete($albumId, $denied){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $albumId
        );

        $_POST = array(
            'Delete' => true,
            'albumId' => $albumId,
        );
        include(dirname(__FILE__).'/../pages/view/albumDelete.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testAlbumEdit($albumId, $newName, $denied){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $albumId
        );

        $_POST = array(
            'Save' => true,
            'name' => $newName,
            'albumId' => $albumId,
        );

        include(dirname(__FILE__).'/../pages/view/albumEdit.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testPhotoDelete($imageId, $albumId, $denied){
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $imageId
        );
        $_POST = array(
            'Delete' => true,
            'album' => array($albumId)
        );

        include(dirname(__FILE__).'/../pages/view/photoDelete.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testPhotoEdit($imageId, $denied){
        $_GET = array(
            'id' => $imageId
        );
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $_POST = array(
            'Save' => true,
            'imageId' => $imageId,
            'name' => 'newName',
            'description' => 'description'
        );

        include(dirname(__FILE__).'/../pages/view/photoEdit.php');
        $this->assertEquals($denied, $accessDenied);
    }

    private function uploadPhoto($albumId) {
        $this->loginAsOwner();

        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $db->query('INSERT INTO images (ownerId, name, filename, extension, created, description) VALUES (' . $_SESSION['id'] . ', "name", "flamingo","jpg", CURRENT_TIMESTAMP(),"")');
        $newImageId = mysql_insert_id();
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ( ' . $albumId . ', "'. $newImageId .'", "1")');

        $target = dirname(__FILE__).'/data/uploadTest/flamingos.jpg';
        $link = dirname(__FILE__).'/../data/images/'.$newImageId.'.jpg';
        @link($target, $link);

        return $newImageId;
    }


    public function testPhotoCopy($imageId, $albumId, $denied){
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'id' => $imageId
        );
        $_POST = array(
            'albumId' => $albumId,
            'imageId'=>$imageId,
            'Copy' => true
        );
        include(dirname(__FILE__).'/../pages/view/photoCopy.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testPhotoMoveRightClick($imageId, $albumId, $newAlbumId, $denied){
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'albumId' => $albumId,
            'id' => $imageId
        );
        $_POST = array(
            'newAlbumId' => $newAlbumId,
            'imageId'=> $imageId,
            'Move' => true
        );
        include(dirname(__FILE__).'/../pages/view/photoMoveRightClick.php');
        $this->assertEquals($denied, $accessDenied);
    }

    public function testPhotoMove($imageId, $albumId, $newAlbumId, $denied){
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $_GET = array(
            'albumId' => $albumId,
            'id' => $imageId
        );
        $_POST = array(
            'newAlbumId' => $newAlbumId,
            'albumId' => $albumId,
            'imageId'=> $imageId,
            'Move' => true
        );
        include(dirname(__FILE__).'/../pages/view/photoMoveRightClick.php');
        $this->assertEquals($denied, $accessDenied);
    }


    public function testOwnerAccess() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $this->loginAsOwner();

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jude.doe@example.org' order by id asc limit 0,1"));
        $userId = $user['id'];

        $this->testAlbumCreation('-1', 'ROOT_OWNER', false);

        $rootAlbum = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER' order by id asc limit 0,1"));
        $rootAlbumId = $rootAlbum['id'];

        /*TEST OF ALBUM CREATION*/
        $this->testAlbumCreation($rootAlbumId, '!test!', false);

        $album = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 0,1"));
        $albumId = $album['id'];

        /*TEST OF ALBUM COPY*/
        $this->testAlbumCopy($albumId, $rootAlbumId, false);

        $copiedAlbum = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 1,2"));
        $copiedAlbumId = $copiedAlbum['id'];

        /*TEST OF ALBUM MOVE*/
        $this->testAlbumMove($albumId, $copiedAlbumId, false);

        //move back to the ROOT_OWNER
        $this->testAlbumMove($albumId, $rootAlbumId, false);

        $this->testAlbumCreation($rootAlbumId, '!test2!', false);
        $this->testAlbumCreation($rootAlbumId, '!test3!', false);
        $this->testAlbumCreation($rootAlbumId, '!test4!', false);
        $albumToDelete = mysql_fetch_assoc($db->query("select * from albums where name = '!test2!' order by id asc limit 0,1"));
        $albumToDeleteId = $albumToDelete['id'];

        /*TEST OF ALBUM DELETE*/
        $this->testAlbumDelete($albumToDeleteId, false);

        /*TEST OF ALBUM EDIT*/
        $this->testAlbumEdit($rootAlbumId, "ROOT_OWNER_NEW_NAME", false);

        //change the name back to ROOT_OWNER
        $this->testAlbumEdit($rootAlbumId, "ROOT_OWNER", false);

        $newImageId = $this->uploadPhoto($rootAlbumId);

        /*TEST OF PHOTO DELETION*/
        $this->testPhotoDelete($newImageId, $rootAlbumId, false);

        $newImageId = $this->uploadPhoto($rootAlbumId);

        /*TEST OF PHOTO EDIT*/
        $this->testPhotoEdit($newImageId, false);

        /*TEST OF PHOTO COPY*/
        $this->testPhotoCopy($newImageId, $albumId, false);

        /*TEST OF PHOTO MOVE BY RIGHT CLICK*/
        $this->testPhotoMoveRightClick($newImageId, $rootAlbumId, $albumId, false);

        /*TEST OF PHOTO MOVE*/
        $this->testPhotoMove($newImageId, $albumId, $rootAlbumId, false);
    }

    public function testNonOwnerAccess() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $this->loginAsNonOwner();

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'alice.doe@example.org' order by id asc limit 0,1"));
        $userId = $user[id];

        $this->testAlbumCreation('-1', 'ROOT_NON_OWNER', false);

        $rootAlbumNonOwner = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_NON_OWNER' order by id asc limit 0,1"));
        $rootAlbumNonOwnerId = $rootAlbumNonOwner['id'];

        $rootAlbumOwner = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER' order by id asc limit 0,1"));
        $rootAlbumOwnerId = $rootAlbumOwner['id'];

        /*TEST OF ALBUM CREATION*/
        $this->testAlbumCreation($rootAlbumOwnerId, '!test!', true);

        $album = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 0,1"));
        $albumId = $album['id'];

        /*TEST OF ALBUM COPY*/
        $this->testAlbumCopy($albumId, $rootAlbumOwnerId, true);

        $copiedAlbum = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 1,2"));
        $copiedAlbumId = $copiedAlbum['id'];

        /*TEST OF ALBUM MOVE*/
        $this->testAlbumMove($albumId, $copiedAlbumId, true);

        $albumToDelete = mysql_fetch_assoc($db->query("select * from albums where name = '!test3!' order by id asc limit 0,1"));
        $albumToDeleteId = $albumToDelete['id'];

        /*TEST OF ALBUM DELETE*/
        $this->testAlbumDelete($albumToDeleteId, true);

        /*TEST OF ALBUM EDIT*/
        $this->testAlbumEdit($rootAlbumOwnerId, "ROOT_OWNER_NEW_NAME", true);

        $newImageId = $this->uploadPhoto($rootAlbumOwnerId);
        $this->loginAsNonOwner();

        /*TEST OF PHOTO DELETION*/
        $this->testPhotoDelete($newImageId, $rootAlbumOwnerId, true);

        /*TEST OF PHOTO EDIT*/
        $this->testPhotoEdit($newImageId, true);

        /*TEST OF PHOTO COPY*/
        $this->testPhotoCopy($newImageId, $albumId, true);

        /*TEST OF PHOTO MOVE BY RIGHT CLICK*/
        $this->testPhotoMoveRightClick($newImageId, $rootAlbumOwnerId, $albumId, true);

        /*TEST OF PHOTO MOVE*/
        $this->testPhotoMove($newImageId, $albumId, $rootAlbumOwnerId, true);

        $this->loginAsOwner();
        $this->testPhotoDelete($newImageId, $rootAlbumOwnerId, false);
        $this->loginAsAdministrator();
    }

    public function testAdministratorAccess() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $this->loginAsAdministrator();

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'john@example.org' order by id asc limit 0,1"));
        $userId = $user[id];

        $rootAlbumAdministrator = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_ADMINISTRATOR' order by id asc limit 0,1"));
        $rootAlbumAdministratorId = $rootAlbumAdministrator['id'];

        $rootAlbumOwner = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER' order by id asc limit 0,1"));
        $rootAlbumOwnerId = $rootAlbumOwner['id'];

        /*TEST OF ALBUM CREATION*/
        $this->testAlbumCreation($rootAlbumOwnerId, '!test!', false);

        $album = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 0,1"));
        $albumId = $album['id'];

        /*TEST OF ALBUM COPY*/
        $this->testAlbumCopy($albumId, $rootAlbumOwnerId, true);

        $copiedAlbum = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 1,2"));
        $copiedAlbumId = $copiedAlbum['id'];

        /*TEST OF ALBUM MOVE*/
        $this->testAlbumMove($albumId, $copiedAlbumId, true);

        $albumToDelete = mysql_fetch_assoc($db->query("select * from albums where name = '!test4!' order by id asc limit 0,1"));
        $albumToDeleteId = $albumToDelete['id'];

        /*TEST OF ALBUM DELETE*/
        $this->testAlbumDelete($albumToDeleteId, true);

        /*TEST OF ALBUM EDIT*/
        $this->testAlbumEdit($rootAlbumOwnerId, "ROOT_OWNER_NEW_NAME", false);

        $newImageId = $this->uploadPhoto($rootAlbumOwnerId);
        $this->loginAsAdministrator();

        /*TEST OF PHOTO DELETION*/
        $this->testPhotoDelete($newImageId, $rootAlbumOwnerId, true);

        /*TEST OF PHOTO EDIT*/
        $this->testPhotoEdit($newImageId, true);

        /*TEST OF PHOTO COPY*/
        $this->testPhotoCopy($newImageId, $albumId, true);

        //*TEST OF PHOTO MOVE BY RIGHT CLICK*/
        $this->testPhotoMoveRightClick($newImageId, $rootAlbumOwnerId, $albumId, true);

        /*TEST OF PHOTO MOVE*/
        $this->testPhotoMove($newImageId, $albumId, $rootAlbumOwnerId, true);

        $this->loginAsOwner();
        $this->testPhotoDelete($newImageId, $rootAlbumOwnerId, false);
        $this->loginAsAdministrator();
    }

    public static function tearDownAfterClass() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        //deletion of the owner root album
        $rootAlbum = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER_NEW_NAME' order by id asc limit 0,1"));
        $rootAlbumId = $rootAlbum['id'];

        $_GET = array(
            'id' => $rootAlbumId
        );

        $_POST = array(
            'Delete' => true,
            'albumId' => $rootAlbumId,
        );
        include(dirname(__FILE__).'/../pages/view/albumDelete.php');

        //deletion of the albums with the name '!test!'
        $albums = $db->query("select * from albums where name = '!test!'");
        if (!empty($albums)) {
            while ($album = mysql_fetch_array($albums)) {
                $_GET = array(
                    'id' => $album['id']
                );

                $_POST = array(
                    'Delete' => true,
                    'albumId' => $album['id'],
                );
                include(dirname(__FILE__) . '/../pages/view/albumDelete.php');
            }
        }

        //deletion of the non owner root album
        $rootAlbum = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_NON_OWNER' order by id asc limit 0,1"));
        $rootAlbumId = $rootAlbum['id'];

        $_GET = array(
            'id' => $rootAlbumId
        );

        $_POST = array(
            'Delete' => true,
            'albumId' => $rootAlbumId,
        );
        include(dirname(__FILE__).'/../pages/view/albumDelete.php');
    }
}