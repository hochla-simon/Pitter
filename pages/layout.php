<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
 <head>
  <title><?php echo $site['title'];?> | <?php echo $config[projectName];?></title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <link rel="icon" href="/images/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="/css/style.css" type="text/css" />
  <link rel="stylesheet" href="/css/jquery-ui.min.css" type="text/css" />
  <link rel="stylesheet" href="/css/dropzone.css" type="text/css" />
  <script src="/js/jquery.min.js" type="text/javascript"></script>
  <script src="/js/jquery-ui.min.js" type="text/javascript"></script>
  <script src="/js/scripts.js" type="text/javascript"></script>
  <script src="/js/dropzone.js"></script>
  <?php echo $site['script'];?>
 </head>
 <body>
  <?php
  include(dirname(__FILE__).'/header.php');
  
  include(dirname(__FILE__).'/navigation.php');
  
  include(dirname(__FILE__).'/content.php');
  
  include(dirname(__FILE__).'/footer.php');
  ?>
 </body>
</html>