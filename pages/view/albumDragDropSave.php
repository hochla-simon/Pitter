<?php
    if (isset($_POST["albumId"]) && isset($_POST["parentAlbumId"]) ) {
        $update_sql_string = 'UPDATE albums SET parentAlbumId="' . $_POST["parentAlbumId"] . '",modified=CURRENT_TIMESTAMP() WHERE id="' . $_POST["albumId"] . '" ';
        $db->query($update_sql_string);
    } else {
        http_response_code(500);
        $db->query($delete_sql_string);
        $message = createMessage("Sorry, there was an error moving your album.");
    }

?>