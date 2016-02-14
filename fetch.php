<?php
define("DOWNLOAD_FOLDER", 'downloads');

$folder = "downloads_" . date("Ymd");

if (isset($_GET['files']) && sizeof($_GET['files']) > 0) {
  $df = DOWNLOAD_FOLDER;
  $file_list = '';
  foreach ($_GET["files"] as $file) {
    $file_list .= "$df/$file ";
  }
  $tmp_folder = "/tmp/$folder";
  $cmd = "mkdir $tmp_folder";
  exec($cmd);
  
  $cmd = "cp $file_list $tmp_folder";
  exec($cmd);
  
  $cmd = "cd /tmp; zip -r $folder $folder";
  exec($cmd);

  header('Content-Type: application/zip');
  header("Content-Disposition: attachment; filename=" . $folder .".zip");
  header("Content-Transfer-Encoding: binary");
 
  readfile("/tmp/$zipfile");
}
?>
