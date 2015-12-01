<?php
include('albumFunctions.php');

$site['title'] = 'Copy photo';
$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
$photoId=$_GET['id'];

if (isset ($_POST["Copy"])) {
    if ($_POST["imageId"] != '') {
        $insert_sql_string = 'INSERT INTO images (ownerId, name, filename, extension, created, description)
                              VALUES (\'' . $_POST["imageOwnerId"] . '\', \'' . $_POST["imageName"] . '\',
                                  \'' . $_POST["imageFilename"] . '\', \'' . $_POST["imageExtension"] . '\',
                                   CURRENT_TIMESTAMP(), \'' . $_POST["imageDescription"] . '\');';
            $db->query($insert_sql_string);


            $db->query('START TRANSACTION;');
            $db->query('SELECT @maxPositionInAlbum := IFNULL(MAX(positionInAlbum),0) FROM imagesToAlbums WHERE albumId=' . $_POST["albumId"] . ';');
            $db->query('INSERT INTO imagesToAlbums (albumId,imageId,positionInAlbum) VALUES (\'' . $_POST["albumId"] . '\',\'' .  $_POST["imageId"] . '\', @maxPositionInAlbum + 1);');
            $db->query('COMMIT;');

            header('Location: ./index.html');
            exit();
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        $message = createMessage("Sorry, there was an error copying your photo.");
    }
}
if($photoId != ''){
    $select_sql_string = "SELECT id, ownerId, name, filename, extension, created, description FROM images WHERE id=" . mysql_real_escape_string($photoId);
    $result = $db->query($select_sql_string);
    if (!empty($result)){
        $image = mysql_fetch_array($result);
    }
}
print $message;
?>
<h2><?php echo $site['title'];?> to...</h2>

<form action="" method="POST">

    <input type="hidden" name="imageId" id="imageId" value="<?php echo $image['id']; ?>" >
    <input type="hidden" name="imageOwnerId" id="imageOwnerId" value="<?php echo $image['ownerId']; ?>" >
    <input type="hidden" name="imageName" id="imageName" value="<?php echo $image['name']; ?>" >
    <input type="hidden" name="imageFilename" id="imageFilename" value="<?php echo $image['filename']; ?>" >
    <input type="hidden" name="imageExtension" id="imageExtension" value="<?php echo $image['extension']; ?>" >
    <input type="hidden" name="imageDescription" id="imageDescription" value="<?php echo $image['description']; ?>" >



    <div class="row">
        <label>Photo to move:</label>
        <p><?php
            if ($image['name'] != '') {
                echo $image['name'];
            } else {
                echo $image['filename'] . $image['name'] . "." . $image['extension'];
            }?>
        </p>
    </div>

    <div class="row">
        <label for="albumId">Destination:</label>

        <select name="albumId" id="albumId">
            <?php
            echo obtainSelectAlbum ($db);
            ?>
        </select>
    </div>

    <div class="row">
        <input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
        <input class="submit" type="submit" name="Copy" value="Copy">
    </div>

</form>
