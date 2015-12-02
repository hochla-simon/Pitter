<?php
    include('albumFunctions.php');
    $site['title'] = 'Delete photo';
    $imageId=$_GET['id'];

    $select_sql_string = 'SELECT albums.id, albums.name FROM imagesToAlbums, albums WHERE imagesToAlbums.imageId=' . mysql_real_escape_string($imageId) . ' AND imagesToAlbums.albumId=albums.id' ;
    $result = $db->query($select_sql_string);

    if (isset ($_POST["Delete"])) {
        $albums = $_POST['album'];
        if (!empty($albums)){
            foreach ($albums as $albumId){
                $delete_sql_string = 'DELETE FROM imagesToAlbums WHERE albumId="' . mysql_real_escape_string($albumId) . '" AND imageId ="'. $imageId . '"';
                $db->query($delete_sql_string);
            }
            deleteImage($db, $imageId);
        }
        header('Location: ./index.html');
        exit();
    }
?>


<form action="" method="POST">

	<input type="hidden" name="photoId" id="albumId" value="<?php echo $imageId; ?>" >

    <label>From which album do you want to delete this photo ? </label>
    <input type="checkbox" name="selectAll" id="selectAll" />
    <?php
        while ($row = mysql_fetch_array($result)){
            echo '<input type="checkbox" name="album[]" value="'. $row['id'] . '">' . $row['name'] . '<br>';
        }
    ?>

    <div class="row">
        <input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
        <input class="submit" type="submit" name="Delete" value="Delete">
    </div>
</form>

<script>
    $('#selectAll').click(function(event) {
        if(this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;
            });
        }
        else {
            $(':checkbox').each(function() {
                this.checked = false;
            });
        }
    });
</script>