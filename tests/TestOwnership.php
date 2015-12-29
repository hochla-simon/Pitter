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

    public function testAlbumCreation($albumId, $name){
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
    }

    private function testAlbumCopy($albumId, $parentAlbumId){
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
    }

    public function testAlbumMove($albumId, $parentAlbumId){
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
    }

    public function testAlbumDelete($albumId){
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
    }

    public function testAlbumEdit($albumId, $newName){
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
    }

    public function testPhotoDelete($imageId, $albumId){
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
    }

    public function testPhotoEdit($imageId){
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
            'name' => 'test',
            'description' => 'description'
        );

        include(dirname(__FILE__).'/../pages/view/photoEdit.php');
    }

    private function uploadPhoto($albumId) {
        $this->loginAsOwner();

        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $db->query('INSERT INTO images (ownerId, name, filename, extension, created, description) VALUES (' . $_SESSION['id'] . ', "", "flamingo","jpg", CURRENT_TIMESTAMP(),"")');
        $newImageId = mysql_insert_id();
        $db->query('INSERT INTO imagesToAlbums (albumId, imageId, positionInAlbum) VALUES ( ' . $albumId . ', "'. $newImageId .'", "1")');

        $target = dirname(__FILE__).'/data/uploadTest/flamingos.jpg';
        $link = dirname(__FILE__).'/../data/images/'.$newImageId.'.jpg';
        @link($target, $link);

        return $newImageId;
    }


    public function testPhotoCopy($imageId, $albumId){
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
    }

    public function testPhotoMoveRightClick($imageId, $albumId, $newAlbumId){
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
    }

    public function testPhotoMove($imageId, $albumId, $newAlbumId){
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
    }


    public function testOwnerAccess() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $this->loginAsOwner();

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'jude.doe@example.org' order by id asc limit 0,1"));
        $userId = $user['id'];

        $this->testAlbumCreation('-1', 'ROOT_OWNER');

        $rootAlbum = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER' order by id asc limit 0,1"));
        $rootAlbumId = $rootAlbum['id'];

        /*TEST OF ALBUM CREATION*/
        $this->testAlbumCreation($rootAlbumId, '!test!');

        $album = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 0,1"));
        $albumId = $album['id'];

        /*TEST OF ALBUM COPY*/
        $this->testAlbumCopy($albumId, $rootAlbumId);

        $copiedAlbum = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 1,2"));
        $copiedAlbumId = $copiedAlbum['id'];

        /*TEST OF ALBUM MOVE*/
        $this->testAlbumMove($albumId, $copiedAlbumId);

        //move back to the ROOT_OWNER
        $this->testAlbumMove($albumId, $rootAlbumId);

        $this->testAlbumCreation($rootAlbumId, '!test2!');
        $this->testAlbumCreation($rootAlbumId, '!test3!');
        $this->testAlbumCreation($rootAlbumId, '!test4!');
        $albumToDelete = mysql_fetch_assoc($db->query("select * from albums where name = '!test2!' order by id asc limit 0,1"));
        $albumToDeleteId = $albumToDelete['id'];

        /*TEST OF ALBUM DELETE*/
        $this->testAlbumDelete($albumToDeleteId);

        /*TEST OF ALBUM EDIT*/
        $this->testAlbumEdit($rootAlbumId, "ROOT_OWNER_NEW_NAME");

        //change the name back to ROOT_OWNER
        $this->testAlbumEdit($rootAlbumId, "ROOT_OWNER");

        $newImageId = $this->uploadPhoto($rootAlbumId);

        /*TEST OF PHOTO DELETION*/
        $this->testPhotoDelete($newImageId, $rootAlbumId);

        $newImageId = $this->uploadPhoto($rootAlbumId);

        /*TEST OF PHOTO EDIT*/
        $this->testPhotoEdit($newImageId);

        /*TEST OF PHOTO COPY*/
        $this->testPhotoCopy($newImageId, $albumId);

        /*TEST OF PHOTO MOVE BY RIGHT CLICK*/
        $this->testPhotoMoveRightClick($newImageId, $rootAlbumId, $albumId);

        /*TEST OF PHOTO MOVE*/
        $this->testPhotoMove($newImageId, $albumId, $rootAlbumId);
    }

    public function testNonOwnerAccess() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $this->loginAsNonOwner();

        $user = mysql_fetch_assoc($db->query("select * from users where email = 'alice.doe@example.org' order by id asc limit 0,1"));
        $userId = $user[id];

        $this->testAlbumCreation('-1', 'ROOT_NON_OWNER');

        $rootAlbumNonOwner = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_NON_OWNER' order by id asc limit 0,1"));
        $rootAlbumNonOwnerId = $rootAlbumNonOwner['id'];

        $rootAlbumOwner = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER' order by id asc limit 0,1"));
        $rootAlbumOwnerId = $rootAlbumOwner['id'];

        set_exit_overload(function() { return FALSE; });
        /*TEST OF ALBUM CREATION*/
        $this->testAlbumCreation($rootAlbumOwnerId, '!test!');

        $album = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 0,1"));
        $albumId = $album['id'];

        /*TEST OF ALBUM COPY*/
        $this->testAlbumCopy($albumId, $rootAlbumOwnerId);

        $copiedAlbum = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 1,2"));
        $copiedAlbumId = $copiedAlbum['id'];

        /*TEST OF ALBUM MOVE*/
        $this->testAlbumMove($albumId, $copiedAlbumId);

        $albumToDelete = mysql_fetch_assoc($db->query("select * from albums where name = '!test3!' order by id asc limit 0,1"));
        $albumToDeleteId = $albumToDelete['id'];

        /*TEST OF ALBUM DELETE*/
        $this->testAlbumDelete($albumToDeleteId);

        /*TEST OF ALBUM EDIT*/
        $this->testAlbumEdit($rootAlbumOwnerId, "ROOT_OWNER_NEW_NAME");

        $newImageId = $this->uploadPhoto($rootAlbumOwnerId);
        $this->loginAsNonOwner();

        /*TEST OF PHOTO DELETION*/
        $this->testPhotoDelete($newImageId, $rootAlbumOwnerId);

        $newImageId = $this->uploadPhoto($rootAlbumOwnerId);
        $this->loginAsNonOwner();

        /*TEST OF PHOTO EDIT*/
        $this->testPhotoEdit($newImageId);

        /*TEST OF PHOTO COPY*/
        $this->testPhotoCopy($newImageId, $albumId);

        /*TEST OF PHOTO MOVE BY RIGHT CLICK*/
        $this->testPhotoMoveRightClick($newImageId, $rootAlbumOwnerId, $albumId);

        /*TEST OF PHOTO MOVE*/
        $this->testPhotoMove($newImageId, $albumId, $rootAlbumOwnerId);
    }

    public function testAdministratorAccess() {
        $phpunit = array(
            'isTest' => true
        );

        include(dirname(__FILE__).'/../index.php');

        $this->loginAsAdministrator();

        set_exit_overload(function() { return FALSE; });
        $user = mysql_fetch_assoc($db->query("select * from users where email = 'john@example.org' order by id asc limit 0,1"));
        $userId = $user[id];

        $rootAlbumAdministrator = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_ADMINISTRATOR' order by id asc limit 0,1"));
        $rootAlbumAdministratorId = $rootAlbumAdministrator['id'];

        $rootAlbumOwner = mysql_fetch_assoc($db->query("select * from albums where name = 'ROOT_OWNER' order by id asc limit 0,1"));
        $rootAlbumOwnerId = $rootAlbumOwner['id'];

        /*TEST OF ALBUM CREATION*/
        $this->testAlbumCreation($rootAlbumOwnerId, '!test!');

        $album = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 0,1"));
        $albumId = $album['id'];

        /*TEST OF ALBUM COPY*/
        $this->testAlbumCopy($albumId, $rootAlbumOwnerId);

        $copiedAlbum = mysql_fetch_assoc($db->query("select * from albums where name = '!test!' order by id asc limit 1,2"));
        $copiedAlbumId = $copiedAlbum['id'];

        /*TEST OF ALBUM MOVE*/
        $this->testAlbumMove($albumId, $copiedAlbumId);

        $albumToDelete = mysql_fetch_assoc($db->query("select * from albums where name = '!test4!' order by id asc limit 0,1"));
        $albumToDeleteId = $albumToDelete['id'];

        /*TEST OF ALBUM DELETE*/
        $this->testAlbumDelete($albumToDeleteId);

        /*TEST OF ALBUM EDIT*/
        $this->testAlbumEdit($rootAlbumOwnerId, "ROOT_OWNER_NEW_NAME");

        $newImageId = $this->uploadPhoto($rootAlbumOwnerId);
        $this->loginAsAdministrator();

        /*TEST OF PHOTO DELETION*/
        $this->testPhotoDelete($newImageId, $rootAlbumOwnerId);

        $newImageId = $this->uploadPhoto($rootAlbumOwnerId);
        $this->loginAsAdministrator();

        /*TEST OF PHOTO EDIT*/
        $this->testPhotoEdit($newImageId);

        /*TEST OF PHOTO COPY*/
        $this->testPhotoCopy($newImageId, $albumId);

        //*TEST OF PHOTO MOVE BY RIGHT CLICK*/
        $this->testPhotoMoveRightClick($newImageId, $rootAlbumOwnerId, $albumId);

        /*TEST OF PHOTO MOVE*/
        $this->testPhotoMove($newImageId, $albumId, $rootAlbumOwnerId);
    }
}