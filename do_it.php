<?php  

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
          error_log("fuser result: $result\n", 3, "error.log");
          if (is_null($result)) {
            $downloadable[] = array('filename' => $file);
          }
        }
      }
      
    }
    
    error_log("Downloadable: " . json_encode($downloadable) . "\n", 3, "error.log");

    echo json_encode($downloadable);
    break;


  case 'upload_and_convert':
    if ($_FILES['file']['error'] > 0) {
       $filevar = print_r($_FILES, true);
       // error_log("Upload files: $filevar\n", 3, "error.log");
    }

    if (!empty($_FILES)) {

      $tempFile = $_FILES['file']['tmp_name'];
      $fileName = pathinfo($_FILES['file']['name']);
      $targetFile =  DL_PATH . $fileName['filename']. ".m4a" ;

      # Convert file to mp4 using ffmpeg
      # Options: no output, overwrite files (-y)
      $cmd = "/usr/local/bin/ffmpeg -i $tempFile -y -loglevel quiet -c:a libfdk_aac $targetFile 2>&1 &";
//       error_log("Executing: $cmd\n", 3, "error.log");
      $output = shell_exec($cmd);
//       error_log($output . "\n", 3, "error.log");
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
      $tmp_folder = "/tmp/$folder";
      $cmd = "mkdir $tmp_folder";
      exec($cmd);
  
      $cmd = "cp $file_list $tmp_folder";
      exec($cmd);
  
      $cmd = "cd /tmp; zip -r $folder $folder";
      exec($cmd);
      
      $zipfile = $folder .".zip";

      header('Content-Type: application/zip');
      header("Content-Disposition: attachment; filename=" . $zipfile);
      header("Content-Transfer-Encoding: binary");
 
      readfile("/tmp/$zipfile");
    }
    break;
    
  case 'clear_files':
    shell_exec("rm " . DL_PATH . "/*.m4a");
    break;
}

?>