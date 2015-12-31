<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:

    include_once(dirname(__FILE__).'/albumFunctions.php');

    $site['title'] = 'Delete photo';
    $imageId=$_GET['id'];
    $accessDenied = false;

    $select_sql_string = 'SELECT * FROM images WHERE id=' . $imageId ;
    $result = $db->query($select_sql_string);
    $row = mysql_fetch_array($result);

    $error = false;
    if(!empty($row)) {
        if ($row['ownerId'] != $currentUser['id']) {
            $message = createMessage("Access denied");
            $error = true;
            http_response_code(401);
            $accessDenied = true;
        } else {
            $select_sql_string = 'SELECT albums.id, albums.name FROM imagesToAlbums, albums WHERE imagesToAlbums.imageId=' . mysql_real_escape_string($imageId) . ' AND imagesToAlbums.albumId=albums.id' ;
            $result = $db->query($select_sql_string);

            if (isset ($_POST["Delete"])) {
                $albums = $_POST['album'];
                if (!empty($albums)){
                    foreach ($albums as $albumId){
                        $query_for_album = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($albumId) . "'";
                        $album_data = mysql_fetch_array($db->query($query_for_album));
                        if (!empty($album_data)) {
                            if ($album_data['ownerId'] == $currentUser['id']) {
                                $delete_sql_string = 'DELETE FROM imagesToAlbums WHERE albumId="' . mysql_real_escape_string($albumId) . '" AND imageId ="' . $imageId . '"';
                                $db->query($delete_sql_string);
                            }
                        }
                    }
                    deleteImage($currentUser['id'], $db, $imageId);
                }
                if (!$phpunit['isTest']) {
                    header('Location: ./index.html?id='.$_GET['albumId']);
                    exit();
                }
            }
        }
        if (!$phpunit['isTest']) {
            if ($error){
                print ($message);
            } else {
                $select_sql_string = 'SELECT albums.id, albums.name FROM imagesToAlbums, albums WHERE imagesToAlbums.imageId=' . mysql_real_escape_string($imageId) . ' AND imagesToAlbums.albumId=albums.id';
                $result = $db->query($select_sql_string);
                ?>


<form action="" method="POST">

	<input type="hidden" name="photoId" id="albumId" value="<?php echo $imageId; ?>" >
    <div class="row">
    From which album do you want to delete this photo ? <br /><br />
    <label><input type="checkbox" name="selectAll" id="selectAll" /> Select all</label><br />
    </div>
    <div class="row">
        <?php
        while ($row = mysql_fetch_array($result)){
            echo '<label><input type="checkbox" name="album[]" value="'. $row['id'] . '">' . $row['name'] . '</label><br>';
        }
    ?>
    </div>
    <div class="row">
        <input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html?id=<?php echo $_GET['albumId'];?>';">
        <input class="submit" type="submit" name="Delete" value="Delete">
    </div>
</form>

                <script>
                    $('#selectAll').change(function (event) {
                        if (this.checked) {
                            // Iterate each checkbox
                            $(':checkbox').each(function () {
                                this.checked = true;
                            });
                        }
                        else {
                            $(':checkbox').each(function () {
                                this.checked = false;
                            });
                        }
                    });
                </script>

                <?php
            }
        }
    }
endif;
?>