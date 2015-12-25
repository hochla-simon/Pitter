<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:

$target_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR;
$target_file = $target_dir . basename($_FILES["file"]["name"]);
#echo "will try to move to " . $target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$imageFileName = pathinfo($target_file,PATHINFO_FILENAME);
// Check if image file is a actual image or fake image

$response_code=200;
if(isset($_POST["albumId"])) {
    $sql = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($_POST["albumId"])."'";
    $albums = mysql_fetch_assoc($db->query($sql));
    if (!empty($albums)) {
        if($albums['ownerId']==$currentUser['id']) {
            $checkFile = getimagesize($_FILES["file"]["tmp_name"]);
            if ($checkFile !== false) {
                $uploadOk = 1;
            } else {
                $response_code = 400;
                $uploadOk = 0;
            }
        }else{
            echo $sql.'\n';
            echo 'owner: '.$albums['ownerId'].' user: '.$currentUser['id'];
            $response_code=401;
            $uploadOk=0;
        }
    }else{
        $response_code=401;
        $uploadOk=0;
    }
} else {
    $response_code=404;
    $uploadOk = 0;
}
if($uploadOk == 1){
    // Allow certain file formats
    $imageFileType = strtolower($imageFileType);
    if ($imageFileType === 'jpeg' ){
        $imageFileType = 'jpg';
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $response_code = 415;
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {

        $tmp_id = 'r'.rand();
        $tmpTarget_file_name = $target_dir . $tmp_id . '.' . $imageFileType;
        if (copy($_FILES["file"]["tmp_name"], $tmpTarget_file_name)) {
            $insert_sql_string = 'INSERT INTO images (ownerId,filename, extension, created) VALUES ('.$currentUser['id'].',\'' . $imageFileName . '\',\'' . $imageFileType . '\', CURRENT_TIMESTAMP());';


            $db->query($insert_sql_string);
            //$insert_query=$conn->prepare($insert_sql_string);
            //$insert_query->execute($data);
            $last_id = mysql_insert_id();

            $image_id = $last_id;
            if ($imageFileType === 'jpg') {
                $exif = exif_read_data($_FILES['file']['tmp_name'], 0, true);

                if ($exif != false) {
                    foreach ($exif as $key => $section) {
                        foreach ($section as $name => $val) {
                            $insert_sql_string = 'INSERT INTO metadata (imageId, name, value)
                            VALUES (' . $image_id . ',\'' . $key . $name . '\',' . $val . ')';
                            $db->query($insert_sql_string);
                        }
                    }
                }
            }

            $newTarget_file_name = $target_dir . $last_id . '.' . $imageFileType;


            rename($tmpTarget_file_name, $newTarget_file_name);

            $db->query('START TRANSACTION;');
            $db->query('SELECT @maxPositionInAlbum := IFNULL(MAX(positionInAlbum),0) FROM imagesToAlbums WHERE albumId=' . $_POST["albumId"] . ';');
            $db->query('INSERT INTO imagesToAlbums (albumId,imageId,positionInAlbum) VALUES (' . $_POST["albumId"] . ',' . $last_id . ', @maxPositionInAlbum + 1);');
            $db->query('COMMIT;');

            if(!$phpunit['isTest']) {
                header('Content-Type: application/json');
            }
            echo '{"lastId":' . $last_id . '}';

        } else {
            $response_code=500;
            $db->query($delete_sql_string);
            echo "Sorry, there was an error uploading your file.";
        }
    }
}else{
    echo "Sorry, there was an error uploading your file.";
    http_response_code($response_code);
}

if(!$phpunit['isTest']) {
    die();
}
endif;
?>