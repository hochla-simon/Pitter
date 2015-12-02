<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 1. 12. 2015
 * Time: 18:51
 */
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:

include('albumFunctions.php');

$site['title'] = 'Move photo';
$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
$photoId=$_GET['id'];
$albumId=$_GET['albumId'];

if (isset ($_POST["Move"])) {
    if ($_POST["imageId"] != '') {
        $query_for_album = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($_POST["newAlbumId"]) . "'";
        $album_data = mysql_fetch_array($db->query($query_for_album));
        if (!empty($album_data)) {
            if ($album_data['ownerId'] == $currentUser['id']) {

                $insert_sql_string = 'INSERT INTO images (ownerId, name, filename, extension, created, description)
                              VALUES (\'' . $_POST["imageOwnerId"] . '\', \'' . $_POST["imageName"] . '\',
                                  \'' . $_POST["imageFilename"] . '\', \'' . $_POST["imageExtension"] . '\',
                                   CURRENT_TIMESTAMP(), \'' . $_POST["imageDescription"] . '\');';
                $db->query($insert_sql_string);


                $db->query('START TRANSACTION;');
                $db->query('SELECT @maxPositionInAlbum := IFNULL(MAX(positionInAlbum),0) FROM imagesToAlbums WHERE albumId=' . $_POST["newAlbumId"] . ';');
                $db->query('INSERT INTO imagesToAlbums (albumId,imageId,positionInAlbum) VALUES (\'' . $_POST["newAlbumId"] . '\',\'' . $_POST["imageId"] . '\', @maxPositionInAlbum + 1);');
                $db->query('COMMIT;');

                $delete_sql_string = 'DELETE FROM imagesToAlbums WHERE albumId="' . mysql_real_escape_string($albumId) . '" AND imageId ="' . $_POST["imageId"] . '"';
                $db->query($delete_sql_string);

                header('Location: ./index.html');
                exit();
            } else {
                include(dirname(__FILE__) . '/../common/error401.php');
                exit();
            }
        }
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        $message = createMessage("Sorry, there was an error moving your photo.");
    }
}
$denied = false;
if($photoId != ''){
    $select_sql_string = "SELECT id, ownerId, name, filename, extension, created, description FROM images WHERE id=" . mysql_real_escape_string($photoId);
    $result = $db->query($select_sql_string);
    if (!empty($result)){
        $image = mysql_fetch_array($result);
        if($image['ownerId']!=$currentUser['id']) {
            $denied = true;
            include(dirname(__FILE__) . '/../common/error401.php');
            exit();
        }
    }
}
if(!$denied) {
    print $message;
    ?>
    <h2><?php echo $site['title']; ?> to...</h2>

    <form action="" method="POST">

        <input type="hidden" name="imageId" id="imageId" value="<?php echo $image['id']; ?>">
        <input type="hidden" name="imageOwnerId" id="imageOwnerId" value="<?php echo $image['ownerId']; ?>">
        <input type="hidden" name="imageName" id="imageName" value="<?php echo $image['name']; ?>">
        <input type="hidden" name="imageFilename" id="imageFilename" value="<?php echo $image['filename']; ?>">
        <input type="hidden" name="imageExtension" id="imageExtension" value="<?php echo $image['extension']; ?>">
        <input type="hidden" name="imageDescription" id="imageDescription" value="<?php echo $image['description']; ?>">


        <div class="row">
            <label>Photo to move:</label>

            <p><?php
                if ($image['name'] != '') {
                    echo $image['name'];
                } else {
                    echo $image['filename'] . $image['name'] . "." . $image['extension'];
                } ?>
            </p>
        </div>

        <div class="row">
            <label for="albumId">Destination:</label>

            <select name="newAlbumId" id="newAlbumId">
                <?php
                echo obtainSelectAlbum($db, $currentUser['id']);
                ?>
            </select>
        </div>

        <div class="row">
            <input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
            <input class="submit" type="submit" name="Move" value="Move">
        </div>

    </form>
    <?php
}
endif;
?>