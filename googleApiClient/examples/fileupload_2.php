<?php
session_start();
/*session_destroy();
die();*/

ini_set('max_execution_time', 300);
include_once "templates/base.php";
require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');

/************************************************
  We'll setup an empty 20MB file to upload.
 ************************************************/

DEFINE("TESTFILE", 'testfile.txt');
if (!file_exists(TESTFILE)) {
  $fh = fopen(TESTFILE, 'w');
  fseek($fh, 1024*1024*20);
  fwrite($fh, "!", 1);
  fclose($fh);
}

/************************************************
  ATTENTION: Fill in these values! Make sure
  the redirect URI is to this page, e.g:
  http://localhost:8080/fileupload.php
 ************************************************/

$client_id = '543903903465-5uukpckfljnvmg54huvqrbfb8hvbrrcb.apps.googleusercontent.com';
$client_secret = 'iqF9eKuMkRSIOYmRWtsjAzR0';
$redirect_uri = 'http://localhost/googleApiClient/examples/fileupload_2.php';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/drive");
$service = new Google_Service_Drive($client);


if (isset($_REQUEST['logout'])) {
  unset($_SESSION['upload_token ']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['upload_token'] = $client->getAccessToken();  
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['upload_token']) && $_SESSION['upload_token']) {
  $client->setAccessToken($_SESSION['upload_token']);
  if ($client->isAccessTokenExpired()) {
    unset($_SESSION['upload_token']);
  }
} else {
  $authUrl = $client->createAuthUrl();
}

if($client->isAccessTokenExpired()) {    
    echo 'Access Token Expired'; // Debug
    //$client->refreshToken($refreshToken);
    /*$Client->authenticate();
    $NewAccessToken = json_decode($Client->getAccessToken());
    $Client->refreshToken($NewAccessToken->refresh_token);*/
}

/************************************************
  If we're signed in then lets try to upload our
  file.
 ************************************************/

if ($client->getAccessToken()) {  

          /*if ($client->getAccessToken()) {
              echo "Created = " . $token->created . '<br/>';
              $token_duration = date('H:i:s',$token->created);
              $current_date = date('H:i:s');
              $max_time = $current_date + $token_duration;

              if($current_date == $max_time){
                $client->setAccessType('offline');
              }
          }*/
          //die();
       
        
        $file = new Google_Service_Drive_DriveFile();
        $title       = 'pop corn movie';
        $description = 'my other folder';
        //$parentId    = '';//'0BwmbtFLQ2uT8NG85Z0tUeWxBSVk'; //0BxIGfs7r1iMcR2k1anEyX05heEk
        $mimeType    = 'application/vnd.google-apps.folder';

        $file->setTitle($title);
        $file->setDescription($description);
        $file->setMimeType($mimeType);

        // Set the parent folder.
       /* if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }*/

        try {
            $createdFile = $service->files->insert($file, array(
                'mimeType' => $mimeType
            ));
        } catch (Exception $e) {
            return 'error';
        }

        if($createdFile){

          $folderId = $createdFile->id;
          $file = new Google_Service_Drive_DriveFile();
          $file->setTitle(TESTFILE);
          $file->setDescription('A test document');
          $file->setMimeType('text/plain');
          $filename = 'testfile.txt';
          $chunkSizeBytes = 1 * 1024 * 1024;

          if ($folderId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($folderId);
            $file->setParents(array($parent));

            $data = file_get_contents(TESTFILE);
            $creatChild = $service->files->insert(
            $file,array(
                    'data' => $data,
                    'mimeType' => 'text/plain',
                    'uploadType' => 'media'
              )
            );

            if($creatChild){
              echo "done";
              die();
            }else{
              echo "no";
              die();
            }
          }

          echo "$folderId";
          echo "<br/>";
          echo TESTFILE;
          die();
          echo "file is created";
          echo $folderId;
          die();
        }else{
          echo "not created";
          die();
        }

  
  
  //$file->title = "Big_Files";
  
  /*$file->setDescription('A test document');
  $file->setMimeType('image/jpeg');
  $filename = 'testfile.txt';
  $chunkSizeBytes = 1 * 1024 * 1024;*/

  // Call the API with the media upload, defer so it doesn't immediately return.
  /*$client->setDefer(true);
  $request = $service->files->insert($file);*/
  
   if ($folderId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($folderId);
            $file->setParents(array($parent));
        }
        $createdFile = $service->files->insert(
            $file,
            array(
                  'data' => file_get_contents($filename),
                  'mimeType' => 'text/plain',
                  'uploadType' => 'media'
            )
	);
        $fileId = $createdFile->id;
        // Deleting a folder by using folder Id
       /* $service    = new Google_Service_Drive($client);
        $fileId = '0B0dYFig1ShbXRlVpNTM3U2d5TUk';
        try {
          $service->files->delete($fileId);
          echo 1;
        } catch (Exception $e) {
          echo 0;
        }*/
  /*// Create a media file upload to represent our upload process.
  $media = new Google_Http_MediaFileUpload(
      $client,
      $request,
      'text/plain',
      null,
      true,
      $chunkSizeBytes
  );
  $media->setFileSize(filesize(TESTFILE));

  // Upload the various chunks. $status will be false until the process is
  // complete.
  $status = false;
  $handle = fopen(TESTFILE, "rb");
  while (!$status && !feof($handle)) {
    // read until you get $chunkSizeBytes from TESTFILE
    // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
    // An example of a read buffered file is when reading from a URL
    $chunk = readVideoChunk($handle, $chunkSizeBytes);
    $status = $media->nextChunk($chunk);
  }

  // The final value of $status will be the data from the API for the object
  // that has been uploaded.
  $result = false;
  if ($status != false) {
    $result = $status;
  }

  fclose($handle);*/
}
echo pageHeader("File Upload - Uploading a large file");
/*if (strpos($client_id, "googleusercontent") == false) {
  echo missingClientSecretsWarning();
  exit;
}*/
/*function readVideoChunk ($handle, $chunkSize)
{
    $byteCount = 0;
    $giantChunk = "";
    while (!feof($handle)) {
        // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
        $chunk = fread($handle, 8192);
        $byteCount += strlen($chunk);
        $giantChunk .= $chunk;
        if ($byteCount >= $chunkSize)
        {
            return $giantChunk;
        }
    }
    return $giantChunk;
}
*/?>
<div class="box">
  <div class="request">
<?php
if (isset($authUrl)) {
  echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
}
?>
  </div>

    <div class="shortened">
<?php
if (isset($folderId) && $folderId) {
  var_dump($folderId);
}
if(isset($fileId) && $fileId){
    var_dump($fileId);
}
?>
    </div>
</div>
<?php
echo pageFooter(__FILE__);
