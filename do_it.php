<?php  

define('TMP_FOLDER', 'tmp');
define('TMP_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR. TMP_FOLDER . DIRECTORY_SEPARATOR);
if (!is_dir(TMP_PATH)) {
  exec("mkdir -p " . TMP_PATH);
}

define('DL_FOLDER', 'downloads');
define('DL_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR. DL_FOLDER . DIRECTORY_SEPARATOR);  
if (!is_dir(DL_PATH)) {
  exec("mkdir -p " . DL_PATH);
}

switch ($_GET['action']) {
  case 'get_downloadable_files':
  
    if (is_dir(DL_PATH)) {
      $downloadable = array();
      $raw_files = scandir(DL_PATH);
      foreach ($raw_files as $file) {
        if (preg_match("/\.(m4a|mp4|mp3)$/", $file)) {
          $file_path = DL_PATH . "/" . $file;
          $result = `fuser $file_path`;
          if (is_null($result)) {
            $downloadable[] = array('filename' => $file);
          }
        }
      }
      
    }
    
    echo json_encode($downloadable);
    break;


  case 'upload_and_convert':
    if ($_FILES['file']['error'] > 0) {
       $filevar = print_r($_FILES, true);
    }

    if (!empty($_FILES)) {

      $tempFile = $_FILES['file']['tmp_name'];
      $fileName = pathinfo($_FILES['file']['name']);
      $targetFile =  DL_PATH . $fileName['filename']. ".m4a" ;

      # Convert file to mp4 using ffmpeg
      # Options: no output, overwrite files (-y)
      $cmd = "/usr/local/bin/ffmpeg -i $tempFile -y -loglevel quiet -c:a libfdk_aac $targetFile 2>&1 &";
      $output = shell_exec($cmd);
    }
    break;
        
  case 'download_files':
    $folder = "m4a_audio_" . date("Ymd-Hi");
    
    $files = $_POST['files'];

    if (isset($files) && sizeof($files) > 0) {
      $file_list = '';
      foreach ($files as $file) {
        $file_list .= DL_FOLDER . "/$file ";
      }
      error_log($file_list);
      $tmp_folder = TMP_PATH . "/$folder";
      $cmd = "mkdir $tmp_folder";
      exec($cmd);
  
      $cmd = "cp $file_list $tmp_folder";
      exec($cmd);
  
      $cmd = "cd " . TMP_PATH . "; zip -r $folder $folder";
      exec($cmd);
      
      $zipfile = $folder .".zip";

      header('Content-Type: application/zip');
      header("Content-Disposition: attachment; filename=" . $zipfile);
      header("Content-Transfer-Encoding: binary");
 
      readfile(TMP_PATH . $zipfile);
    }
    break;
    
  case 'clear_files':
    shell_exec("rm " . DL_PATH . "/*.m4a");
    break;
}

?>