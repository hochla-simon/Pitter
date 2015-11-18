<?php

$sql_ordering_field = " images.created ";
$sql_ordering_function = " ASC ";
$sql_ordering_instruction = " ORDER BY  ".$sql_ordering_field." ".$sql_ordering_function;
$sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId =
    " . mysql_real_escape_string(1) . $sql_ordering_instruction;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head class="slideshow">
    <title><?php echo $site['title'];?> | <?php echo $config[projectName];?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="icon" href="<?php echo $config['projectURL'];?>images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="<?php echo $config['projectURL'];?>css/style.css" type="text/css" />
    <script src="<?php echo $config['projectURL'];?>js/jquery.min.js" type="text/javascript"></script>
    <?php echo $site['script'];?>
</head>
<body class="slideshow">

<?php
$images = $db->query($sql);
if (!empty($images)) {
    $count=0;
    while($row = mysql_fetch_array($images)) {
        if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
            echo '<div class="slideshow"> <img class="slideshow hidden" id="'.$count.'" src="'.$config['projectURL'].'view/image.html?id=' . $row['id'] . '"/></div>';
            $count = $count+1;
        }
    }
    if($count!=0){
        echo '
        <script>
        $( "img#0" ).load(function() {
            $(this).removeClass("hidden");
        });
        </script>';

    }
} else {
    echo '<h2>No photos!</h2>';
}
?>

</body>
</html>

<?php
die();