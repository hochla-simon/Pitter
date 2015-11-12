<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 11. 11. 2015
 * Time: 13:24
 */
include("config.php"); //include config file

if($_POST)
{
    //sanitize post value
    $group_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
    $albumId = filter_var($_POST["album_id"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

    //throw HTTP error if group number is not valid
    if(!is_numeric($group_number)){
        header('HTTP/1.1 500 Invalid number!');
        exit();
    }

    //throw HTTP error if album_id is not valid
    if(!is_numeric($albumId)){
        header('HTTP/1.1 500 Invalid number!');
        exit();
    }

    //get current starting point of records
    $position = ($group_number * $items_per_group);

    //Limit our results within a specified range.
    $sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId =
    " . mysql_real_escape_string($albumId) . " ORDER BY imagesToAlbums.positionInAlbum ASC LIMIT $position, $items_per_group";

    $images = $db->query($sql);
    if (!empty($images)) {
        while($row = mysql_fetch_array($images)) {
            if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
                echo '<a href="photoView.html?id=' . $row['id'] . '"><div class="thumbnail" title="' . $row['filename'] .
                    '.' . $row['extension'] . '"><span class="center_img"></span><img src="image.html?id=' . $row['id'] . '&max_size=100"/></div></a>';
            }
        }
    } else {
        echo '<h2>No photos!</h2>';
    }
}
die();
?>