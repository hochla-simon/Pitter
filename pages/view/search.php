<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:
    $site['title'] = 'Search';
    include_once(dirname(__FILE__).'/albumFunctions.php');
    ?>
    <h2>Search</h2>
    <form class="searchForm searchPage" action="" method="post">
        <input type="text" name="keywords" placeholder="Search for..." value="<?php echo $_POST['keywords'];?>">
        <select name="albumId" id="albumId">
            <option value="">All albums</option>
            <?php
            echo obtainSelectAlbum($db, $currentUser['id'], '', $_POST['albumId']);
            ?>
        </select>
        <input type="submit" name="submit" value="search">
    </form>
    <?php
    if(trim($_POST['keywords'] != '')):?>
    <div data-albumid="6" class="images ui-sortable" id="photos">
        <?php
        $result = $db->query("select distinct images.id, images.filename, images.extension from imagesToAlbums, albums, images left join metadata on (images.id = metadata.imageId) where images.id = imagesToAlbums.imageId and albums.id = imagesToAlbums.albumId and albums.ownerId = '".$currentUser['id']."' ".(($_POST['albumId'] != '') ? " and albums.id = '".mysql_real_escape_string($_POST['albumId'])."'" : "")." and (".get_search_cols($_POST['keywords'], array('albums', 'images', 'metadata'), false).") order by id desc");
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
        ?>
    </div>
    <?php
    endif;
    ?>
    <?php
endif;
?>
