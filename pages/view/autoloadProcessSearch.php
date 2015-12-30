<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 30. 12. 2015
 * Time: 13:09
 */

$currentUser['id'] = filter_var($_POST["currentUserId"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
if($currentUser['id'] == ''):
    echo "Unauthorized.";
else:
    if($_POST)
    {
        //sanitize post value
        $group_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
        $album_id = filter_var($_POST["album_id"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
        $items_per_group = filter_var($_POST["images_per_group"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

        //throw HTTP error if group number is not valid
        if(!is_numeric($group_number)){
            if(!$phpunit['isTest']){
                header('HTTP/1.1 400 Bad Request');
                die();
            }
        }
        //get current starting point of records
        $position = ($group_number * $items_per_group);

        $result = $db->query("select distinct images.id, images.filename, images.extension from imagesToAlbums, albums,
             images left join metadata on (images.id = metadata.imageId) where images.id = imagesToAlbums.imageId and
             albums.id = imagesToAlbums.albumId and albums.ownerId = '".$currentUser['id']."' ".((mysql_real_escape_string($album_id) != '') ? "
              and albums.id = '". mysql_real_escape_string($album_id) ."'" : "")." and (".get_search_cols($_POST["keywords"],
                array('albums', 'images', 'metadata'), false).") order by id desc LIMIT $position, $items_per_group");


        $i = 0;
        while($row = mysql_fetch_assoc($result)) {
            ?>
            <a id="image_<?php echo $row['id'];?>" href="photoView.html?id=<?php echo $row['id'];?>" data-id="<?php echo $row['id'];?>" class="draggablePhoto ui-draggable ui-draggable-handle" style="position: relative;"><div title="<?php echo $row['filename'];?>.<?php echo $row['extension'];?>" class="thumbnail"><span class="center_img"></span><img src="image.html?id=<?php echo $row['id'];?>&amp;max_size=100"></div></a>
            <?php
            $i++;
        }
        if($i == 0){
            echo '<br /><br />'.createMessage('No images could be found matching your request.');
        }
    }
endif;
if(!$phpunit['isTest']) {
    die();
}
?>