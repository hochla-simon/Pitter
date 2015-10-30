<?php
      $site['title'] = 'Upload';
?>

<form action="./upload.html"
      class="dropzone"
      id="myDropzone"></form>

<script>

        Dropzone.options.myDropzone = {
            dictInvalidFileType : "only jpg, jpeg, png and gif are accepted",
            acceptedFiles: "image/jpeg,image/png,image/gif",
            init: function() {
                this.on("complete", function (file) {
                    if(file.accepted){
                        console.log(file)
                        this.removeFile(file);
                    }
                });
            }
        }

</script>