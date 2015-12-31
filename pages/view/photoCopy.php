<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:
include_once(dirname(__FILE__).'/albumFunctions.php');

$site['title'] = 'Copy photo';
$site['script'] = '<script  src="' . $config['projectURL'] . '/js/form.js" type="text/javascript"> </script>';
$photoId=$_GET['id'];
$accessDenied = false;

    if (isset ($_POST["Copy"])) {
    if ($_POST["imageId"] != '') {
        $query_for_album = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($_POST["albumId"]) . "'";
        $album_data = mysql_fetch_array($db->query($query_for_album));
        if (!empty($album_data)) {
            if ($album_data['ownerId'] == $currentUser['id']) {

                $db->query('START TRANSACTION;');
                $db->query('SELECT @maxPositionInAlbum := IFNULL(MAX(positionInAlbum),0) FROM imagesToAlbums WHERE albumId=' . $_POST["albumId"] . ';');
                $db->query('INSERT INTO imagesToAlbums (albumId,imageId,positionInAlbum) VALUES (\'' . $_POST["albumId"] . '\',\'' . $_POST["imageId"] . '\', @maxPositionInAlbum + 1);');
                $db->query('COMMIT;');

                if (!$phpunit['isTest']) {
                    header('Location: ./index.html?id='.$_POST['albumId']);
                    exit();
                }
            } else {
                if(!$phpunit['isTest']) {
                    include(dirname(__FILE__) . '/../common/error401.php');
                    exit();
                }
                $accessDenied = true;
            }
        }
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        $message = createMessage("Sorry, there was an error copying your photo.");
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
            if(!$phpunit['isTest']) {
                include(dirname(__FILE__) . '/../common/error401.php');
                exit();
            }
            $accessDenied = true;
        }
    }
} else {
    $message = createMessage("Photo id is blank.");
}
if(!$denied) {
    print $message;
    if (!$phpunit['isTest']) {
    ?>

    <h2><?php echo $site['title']; ?> to...</h2>

    <form action="" method="POST">

        <input type="hidden" name="imageId" id="imageId" value="<?php echo $image['id']; ?>">

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

            <select name="albumId" id="albumId">
                <?php
                echo obtainSelectAlbum($db, $currentUser['id']);
                ?>
            </select>
        </div>

        <div class="row">
            <input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html?id=<?echo $_GET['albumId'];?>';">
            <input class="submit" type="submit" name="Copy" value="Copy">
        </div>

    </form>

    <?php
    }
}
endif;
?>