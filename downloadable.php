<?php
  define("DOWNLOAD_FOLDER", 'downloads');
  
  function downloadable_files($f) {
    return preg_match("/\.(m4a|mp4)$/", $f);
  }
  function add_folder($f) {
    return array( 
      "filepath" => DOWNLOAD_FOLDER . DIRECTORY_SEPARATOR . $f,
      "filename" => $f,
    );
  }
  
  $download_folder = dirname( __FILE__ ) . DIRECTORY_SEPARATOR. DOWNLOAD_FOLDER;

  if (is_dir($download_folder)) {
    $mp4_files = array_values(array_filter(scandir($download_folder), "downloadable_files"));
  }
  $mp4_paths = array_map("add_folder", $mp4_files); 

  echo json_encode($mp4_paths);
  
?>