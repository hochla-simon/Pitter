<?php
if($currentUser['id'] == ''):
	$_POST['redirect'] = $_SERVER['REQUEST_URI'];
	include(dirname(__FILE__).'/../users/login.php');
else:
    $resultImages = mysql_query("SELECT COUNT(1) FROM images");
    $row = mysql_fetch_array($resultImages);
    $total_records = $row[0];
    $keywords = $_POST['keywords'];
    $albumId = $_POST['albumId'];
    $projectUrl =  $config['projectURL'];

    $site['title'] = 'Search';
    include_once(dirname(__FILE__).'/albumFunctions.php');
    if (!$phpunit['isTest']):?>
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
        endif;
            if(trim($_POST['keywords'] != '')):
                if (!$phpunit['isTest']):?>
                    <script type="text/javascript">
                        var track_load = 0; //total loaded record group(s)
                        var loading  = false; //to prevents multipal ajax loads
                        var album_id = <?php echo(json_encode($albumId)); ?>;
                        var projectUrl = <?php echo(json_encode($projectUrl));?>;
                        var keywords = <?php echo(json_encode($keywords));?>;
                        var albumId = <?php echo(json_encode($albumId));?>;
                        var current_user_id = <?php echo(json_encode($currentUser['id']));?>;


                        var images_per_group = 0;

                        function loadFirstElements() {
                            track_load = 0;
                            loading  = false;
                            $('#photos').load(projectUrl + "view/autoloadProcessSearch.html", {'currentUserId':current_user_id, 'images_per_group':images_per_group, 'group_no':track_load, 'album_id':albumId, 'keywords': keywords}, function() {track_load++;}); //load first group
                        }
                        function countImagesNumberPerPage() {
                            var elmnt = document.getElementById("wrapper");
                            var height =  $(window).height() - elmnt.scrollHeight;
                            var width = elmnt.scrollWidth;
                            var imageSize = 125;
                            var imagesInRow = Math.floor(width / imageSize);
                            var imagesInColumn = Math.ceil(height / imageSize);
                            var imagesToLoad = imagesInRow * imagesInColumn;
                            return imagesToLoad;
                        }
                        $(document).ready(function() {

                            var total_records = <?php echo $total_records; ?>;
                            images_per_group = countImagesNumberPerPage();
                            var total_groups = total_records / images_per_group;
                            loadFirstElements();

                            $(window).scroll(function() { //detect page scroll

                                if($(window).scrollTop() + $(window).height() == $(document).height())  //user scrolled to bottom of the page?
                                {

                                    if(track_load <= total_groups && loading==false) //there's more data to load
                                    {
                                        loading = true; //prevent further ajax loading
                                        $('.animation_image').show(); //show loading image

                                        $.post(projectUrl + "view/autoloadProcessSearch.html",{'currentUserId':current_user_id, 'images_per_group':images_per_group, 'group_no':track_load, 'album_id':albumId, 'keywords': keywords}, function(data){

                                            $("#photos").append(data); //append received data into the element
                                            //hide loading image
                                            $('.animation_image').hide(); //hide loading image once data is received

                                            track_load++; //loaded group increment
                                            loading = false;

                                        }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
            //                            getOutput(track_load);

                                            alert(thrownError); //alert with HTTP error
                                            $('.animation_image').hide(); //hide loading image
                                            loading = false;

                                        });
                                    }
                                }
                            });
                        });
                    </script>
                    <div data-albumid="6" class="images ui-sortable" id="photos">
                </div>
                <?php
            endif;
            ?>
            <?php
        endif;
    ?>
    <?php
endif;
?>