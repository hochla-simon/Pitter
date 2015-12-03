<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:
if(!isset($_GET['album'])){
    http_response_code(404);
}else {
    if(!is_numeric($_GET['album'])){
        http_response_code(404);
    }else {
        $sql = "SELECT parentAlbumId, id, name FROM albums WHERE id='" . mysql_real_escape_string($_GET["album"]) . "'";
        $albums = mysql_fetch_assoc($db->query($sql));
        if (empty($albums)) {
            http_response_code(404);
        } else {
            $album_id = $_GET['album'];

            $sql_ordering_field = " images.created ";
            $sql_ordering_function = " ASC ";
            $sql_ordering_instruction = " ORDER BY  " . $sql_ordering_field . " " . $sql_ordering_function;
            $sql = "SELECT id, filename, extension FROM images, imagesToAlbums WHERE images.id = imagesToAlbums.imageId AND albumId =
    " . mysql_real_escape_string($_GET["album"]) . $sql_ordering_instruction;
            ?>

            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
            <head class="slideshow">
                <title><?php echo $site['title']; ?> | <?php echo $config[projectName]; ?></title>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
                <link rel="icon" href="<?php echo $config['projectURL']; ?>images/favicon.ico" type="image/x-icon"/>
                <link rel="stylesheet" href="<?php echo $config['projectURL']; ?>css/style.css" type="text/css"/>
                <script src="<?php echo $config['projectURL']; ?>js/jquery.min.js" type="text/javascript"></script>
                <?php echo $site['script']; ?>
            </head>
            <body>
            <div class="container">
                <?php
                $images = $db->query($sql);
                if (!empty($images)) {
                    echo '<div class="slideshow">';
                    $count = 0;
                    while ($row = mysql_fetch_array($images)) {
                        if (file_exists(dirname(__FILE__) . '/../../data/images/' . $row['id'] . '.' . $row['extension'])) {
                            echo '<img class="slideshow hidden" id="' . $count . '" src="' . $config['projectURL'] . 'view/image.html?id=' . $row['id'] . '"/>';
                            $count = $count + 1;
                        }
                    }
                    echo '</div>';
                    if ($count != 0) {
                        ?>
                        <script>
                            var currentImage = 0;
                            var totalNumberImages = <?php echo $count?>;
                            var reduced=true;
                            $("img#" + currentImage).load(function () {
                                $(this).removeClass("hidden");
                            });
                            var tid = setInterval(transition_right, 2000);
                            function transition_right() {
                                $("img.slideshow").addClass("hidden");
                                currentImage = currentImage + 1;
                                if (currentImage >= totalNumberImages - 1) {
                                    currentImage = totalNumberImages - 1;
                                    $("#slideshow_next").addClass("hidden");
                                }
                                $("#slideshow_previous").removeClass("hidden");
                                $("img#" + currentImage).removeClass("hidden");
                            }
                            function transition_left() {
                                $("img.slideshow").addClass("hidden");
                                currentImage = currentImage - 1;
                                if (currentImage < 0) {
                                    currentImage = 0;
                                    $("#slideshow_previous").addClass("hidden");
                                }
                                $("#slideshow_next").removeClass("hidden");
                                $("img#" + currentImage).removeClass("hidden");
                            }
                            function pauseTimer() { // to be called when you want to stop the timer
                                clearInterval(tid);
                                delete tid;
                            }
                            function playTimer() {
                                tid = setInterval(transition_right, 2000);
                            }
                        </script>
                        <div class="slideshow_commands hidden">
                            <a href="<?php echo $config['projectURL'] ?>view/index.html?id=<?php echo $album_id?>">
                                <img id="slideshow_exit" class="command_icon"
                                     src="<?php echo $config['projectURL'] ?>images/icons/exit.svg" alt="exit icon">
                            </a>
                            <img id="slideshow_previous" class="command_icon hidden"
                                 src="<?php echo $config['projectURL'] ?>images/icons/caret-left.svg" alt="previous icon">
                            <img id="slideshow_pause" class="command_icon"
                                 src="<?php echo $config['projectURL'] ?>images/icons/media-pause.svg" alt="pause icon">
                            <img id="slideshow_play" class="command_icon hidden"
                                 src="<?php echo $config['projectURL'] ?>images/icons/media-play.svg" alt="play icon">
                            <img id="slideshow_next" class="command_icon"
                                 src="<?php echo $config['projectURL'] ?>images/icons/caret-right.svg" alt="next icon">
                            <img id="fullscreen_enter" class="command_icon"
                                 src="<?php echo $config['projectURL'] ?>images/icons/fullscreen-enter.svg" alt="enter fullscreen">
                            <img id="fullscreen_exit" class="command_icon hidden"
                                 src="<?php echo $config['projectURL'] ?>images/icons/fullscreen-exit.svg" alt="exit fullscreen">
                        </div>

                        <script>
                            $("#slideshow_pause").click(function () {
                                pauseTimer();
                                $("#slideshow_play").removeClass("hidden");
                                $(this).addClass("hidden");
                            });
                            $("#slideshow_play").click(function () {
                                playTimer();
                                $("#slideshow_pause").removeClass("hidden");
                                $(this).addClass("hidden");
                            });
                            $("#slideshow_next").click(function () {
                                pauseTimer();
                                $("#slideshow_play").removeClass("hidden");
                                $('#slideshow_pause').addClass("hidden");
                                transition_right();
                            });
                            $("#slideshow_previous").click(function () {
                                pauseTimer();
                                $("#slideshow_play").removeClass("hidden");
                                $('#slideshow_pause').addClass("hidden");
                                transition_left();
                            });
                            function launchIntoFullscreen(element) {
                                if(element.requestFullscreen) {
                                    element.requestFullscreen();
                                } else if(element.mozRequestFullScreen) {
                                    element.mozRequestFullScreen();
                                } else if(element.webkitRequestFullscreen) {
                                    element.webkitRequestFullscreen();
                                } else if(element.msRequestFullscreen) {
                                    element.msRequestFullscreen();
                                }
                            }
                            $("#fullscreen_enter").click(function () {
                                $("#fullscreen_exit").removeClass("hidden");
                                $('#fullscreen_enter').addClass("hidden");
                                launchIntoFullscreen(document.documentElement)
                            });
                            function exitFullscreen() {
                                if(document.exitFullscreen) {
                                    document.exitFullscreen();
                                } else if(document.mozCancelFullScreen) {
                                    document.mozCancelFullScreen();
                                } else if(document.webkitExitFullscreen) {
                                    document.webkitExitFullscreen();
                                }
                            }
                            $("#fullscreen_exit").click(function () {
                                $("#fullscreen_enter").removeClass("hidden");
                                $('#fullscreen_exit').addClass("hidden");
                                exitFullscreen();
                            });

                            var reduceTimer;
                            function showCommands(){
                                if(reduced) {
                                    $('.slideshow_commands').removeClass("hidden");
                                    reduced = false;
                                    reduceTimer = setTimeout(function(){
                                        $('.slideshow_commands').addClass("hidden");
                                        reduced = true;
                                    },2000)
                                }else{
                                    clearTimeout(reduceTimer);
                                    reduceTimer = setTimeout(function(){
                                        $('.slideshow_commands').addClass("hidden");
                                        reduced = true;
                                    },2000)
                                }
                            }
                            $('.slideshow').mousemove(function( event ) {
                                showCommands();
                            });
                            $('.slideshow_commands').hover(function( event ) {
                                showCommands();
                            });
                            $('.slideshow').on("tap",function(){
                                showCommands();
                            });
                        </script>
                        <?php

                    }
                } else {
                    echo '<h2>No photos!</h2>';
                }
                ?>
            </div>
            </body>
            </html>

            <?php
            die();
        }
    }
}
endif;
?>