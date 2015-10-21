<?php
$target_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR;
echo $target_dir;
$target_file = $target_dir . basename($_FILES["file"]["name"]);
#echo "will try to move to " . $target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$imageFileName = pathinfo($target_file,PATHINFO_FILENAME);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "JPG" && $imageFileType != "png" && $imageFileType != "PNG"  && $imageFileType != "jpeg" &&
    $imageFileType != "JPEG" && $imageFileType != "gif" && $imageFileType != "GIF") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    http_response_code(415);
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {

    $conn = new PDO('mysql:host=localhost;dbname=pitter', 'root', '');

    $insert_sql_string = 'INSERT INTO Pictures (name, imageFormat, dateCreated, dateAdded, dateSeen)
            VALUES (?, ?, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())';
    $data = array($imageFileName,$imageFileType);

    $insert_query=$conn->prepare($insert_sql_string);
    $insert_query->execute($data);
    $last_id = $conn->lastInsertId();

    $newTarget_file_name = $target_dir. $last_id.'.'.$imageFileType;
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $newTarget_file_name)) {
        echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
    } else {
        http_response_code(500);
        echo "Sorry, there was an error uploading your file.";
    }
}
die();