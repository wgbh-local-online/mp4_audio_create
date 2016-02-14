<?php
$ds          = DIRECTORY_SEPARATOR;  //1
 
$storeFolder = 'downloads';   //2
 
if (!empty($_FILES)) {
     
    $tempFile = $_FILES['file']['tmp_name'];
    $fileName = pathinfo($_FILES['file']['name']);
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
    $targetFile =  $targetPath. $fileName['filename']. ".m4a" ;

    # Convert file to mp4 using ffmpeg
    exec("ffmpeg -i $tempFile -c:a libfdk_aac $targetFile");       
}
?>     
