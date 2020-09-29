<?php

$target_dir = "uploads/";
$target_file = $target_dir . "test_".basename($_FILES["file"]["name"]);
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$name =  basename($_FILES["file"]["name"]);
$uploadOk = 1;
if (hasParam('submit'))
{
   // Check if file already exists
   if (file_exists($target_file)) {
       $uploadOk = 1;
   }

   // Check file size
   if ($_FILES["fileToUpload"]["size"] > 500000) {
          echo "Sorry, your file is too large.";
          $uploadOk = 0;
    }


   if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
   // if everything is ok, try to upload file
       } else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"],$target_file)) {
       
     $test_zip = "uploads/testzip.zip";
     createZip($test_zip,"uploads");

     $filesize = filesize($test_zip);
     $fp = fopen($test_zip, 'rb');
     $fileBinary = fread($fp, $filesize);
     fclose($fp);

     $value = unpack('H*', $name);
     $valbin = base_convert($value[1], 16, 2); 
 
     $newName = replaceLast($fileType,"jpg",$name);
     $newvalue = unpack('H*', $newName);
     $newvalbin = base_convert($value[1], 16, 2); 
    
 
     $newbin = replaceLast($name, $newName, $fileBinary);
   
     $newFile = fopen("uploads/testnew.zip", 'w');
     $fwrite = fwrite($newFile, $newbin);
     if ($fwrite === false) {
            // write fail
        }
        else {
             // write ok
           dl("uploads/testnew.zip");
        }
    



      } else {
       echo "Sorry, there was an error uploading your file.";
     }
  }
}
else
{


echo '<!DOCTYPE html>
<html>
<body>

<form method="post" enctype="multipart/form-data" action="">
  Select file to send to worker script:
  <input type="file" name="file" id="file">
  <input type="submit" value="WORK" name="submit">
</form>

</body>
</html>';

}











function createZip($zipPath,$dir){
$rootPath = realpath($dir);
// Initialize archive object
$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    if (!$file->isDir())
    {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath)+1);
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();
return $zipPath;
}

function dl($file){
  if (file_exists($file)) {
     header('Content-Type: application/zip');
     header('Content-Disposition: attachment; filename="'.basename($file).'"');
     header('Content-Length: ' . filesize($file));
     flush();
     readfile($file);
     // delete file
     delete($file);
     deleteAll();
   }
}


function replaceLast($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);
    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function delete($file)
{
      if (file_exists($file)) {
        unlink($file);
        return true;
    } else {
        return false;
    }
}
function deleteAll()
{
$files = glob('uploads/*');
foreach($files as $file){ 
  if(is_file($file))
    unlink($file);
}
}
function hasParam($param) 
{
   if (array_key_exists($param, $_REQUEST))
    {
       return array_key_exists($param, $_REQUEST);
    } 
}
?>
