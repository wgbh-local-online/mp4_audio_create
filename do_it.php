<?php  

define('DL_FOLDER', 'downloads');
define('DL_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR. DL_FOLDER . DIRECTORY_SEPARATOR);
  
if (!is_dir(DL_PATH)) {
  exec("mkdir -p " . DL_PATH);
} 
     
function downloadable_files($f) {
  return preg_match("/\.(m4a|mp4|mp3)$/", $f);
}

function add_folder($f) {
  return array( 
    "filepath" => DL_FOLDER . DIRECTORY_SEPARATOR . $f,
    "filename" => $f,
  );
}

switch ($_GET['action']) {
  case 'get_downloadable_files':
  
    if (is_dir(DL_PATH)) {
      $mp4_files = array_values(array_filter(scandir(DL_PATH), "downloadable_files"));
    }
    $mp4_paths = array_map("add_folder", $mp4_files); 

    echo json_encode($mp4_paths);
    break;

  case 'upload_and_convert':
    if (!empty($_FILES)) {
    
      $tempFile = $_FILES['file']['tmp_name'];
      error_log("Tempfile: $tempFile\n", 3, "error.log");
      $fileName = pathinfo($_FILES['file']['name']);
      $targetFile =  DL_PATH . $fileName['filename']. ".m4a" ;
      error_log("Target file: $targetFile\n", 3, "error.log");

      # Convert file to mp4 using ffmpeg
      $output = shell_exec("ffmpeg -i $tempFile -c:a libfdk_aac $targetFile");
      error_log($output, 4, "error.log");       
    }
    break;
    
  case 'download_files':
    $folder = "downloads_" . date("Ymd-Hi");
    
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