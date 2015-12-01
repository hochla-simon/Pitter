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
    $album_id = filter_var($_POST["album_id"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

    //throw HTTP error if group number is not valid
    if(!is_numeric($group_number)){
        header('HTTP/1.1 500 Invalid number!');
        exit();
    }

    //throw HTTP error if album_id is not valid
    if(!is_numeric($album_id)){
        header('HTTP/1.1 500 Invalid number!');
        exit();
    }

    //get current starting point of records
    $position = ($group_number * $items_per_group);

    $sql_ordering_field = " imagesToAlbums.positionInAlbum ";
    if(isset($_POST['ord_field'])){
        if($_POST['ord_field']=='date'){
            $sql_ordering_function=" images.created ";
        } elseif ($_POST['ord_field']=='position'){
            $sql_ordering_field = " imagesToAlbums.positionInAlbum ";
        } elseif ($_POST['ord_field']=='fileName'){
            $sql_ordering_field = " images.filename ";
        }
    }

    $sql_ordering_function = " ASC ";
    if(isset($_POST['ordering'])){
        if($_POST['ordering']=='DESC'){
            $sql_ordering_function=" DESC ";
        }
    }

    $sql_ordering_instruction = " ORDER BY  ".$sql_ordering_field." ".$sql_ordering_function;
    //Limit our results within a specified range.
    $sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId =
    " . mysql_real_escape_string($album_id) . $sql_ordering_instruction. " LIMIT $position, $items_per_group";

    $images = $db->query($sql);
    if (!empty($images)) {
        while($row = mysql_fetch_array($images)) {
            if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
                echo '<a class="draggablePhoto" data-id="' . $row['id'] . '" href="photoView.html?id=' . $row['id'] . '" id="image_'. $row['id'] . '"><div class="thumbnail" title="' . $row['filename'] .
                    '.' . $row['extension'] . '"><span class="center_img"></span><img src="image.html?id=' . $row['id'] . '&max_size=100"/></div></a>';
            }
        }
		echo '<script>
			$(".draggablePhoto").draggable({
				connectToSortable: "#photos",
				revert: "invalid",
				start: function( event, ui ) {
					$(".childAlbums").css("display", "");
					$("img.toggleArrow").each(function(){
						var originalImgSrc = $(this).attr("src");
						var lastSlashIndex = originalImgSrc.lastIndexOf("/") + 1;
						var newImgSrc = originalImgSrc.substring(0, lastSlashIndex) + arrow_down_image;
						$(this).attr("src", newImgSrc);
					});
				}
			});';
    } else {
        echo '<h2>No photos!</h2>';
    }
}
die();
?>