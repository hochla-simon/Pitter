<?php
      $site['title'] = 'Upload';
?>

<form action="./upload.html"
      class="dropzone"
      id="myDropzone"></form>

<script>

        Dropzone.options.myDropzone = {
            dictInvalidFileType : "only jpg, jpeg, png and gif are accepted",
            acceptedFiles: "image/jpeg,image/png,image/gif"

        }

</script>