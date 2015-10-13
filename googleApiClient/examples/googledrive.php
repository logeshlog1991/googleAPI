<?php

class Googledrive {
    
   /* function googleDriveInitialization() {
        
        //we get the details of his studio from google drive
        //include_once "templates/base.php";
        require_once ('assets/plugins/googledriveapi/autoload.php');

        $client_id = '1041744839592-emj2h999fqeiv9unloqlcevl181prj2c.apps.googleusercontent.com';
        $client_secret = 'kh4c0JKtZMWGURmUAvBcA0Iw';
        $redirect_uri = 'http://localhost/googledriveapi/examples/simplefileupload.php';
        
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/drive");
        //$service = new Google_Service_Drive($client);
        
        $client->setAccessToken(file_get_contents('http://37.187.200.74/pandora/assets/plugins/googledriveapi/examples/accesstoken.json'));
        
        return $client;
        
    }  */
    function googleDriveInitialization() {
        
        //we get the details of his studio from google drive
        //include_once "templates/base.php";
        require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');

        $client_id = '736422639788-s7qhnti8e76723qvr6aq5i01c2jn0ssb.apps.googleusercontent.com';
        $client_secret = 'MylYL5Kq6j9aYpMPlW0-W2d3';
        $redirect_uri = 'http://localhost/googleApiClient/examples/fileupload.php';
        
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/drive");
        //$service = new Google_Service_Drive($client);
        
        $client->setAccessToken(file_get_contents('http://37.187.200.74/pandora/assets/plugins/googledriveapi/examples/accesstoken.json'));
        
        return $client;
        
    } 
    
    function createStudioFolder($keystudio,$parId = '0BwmbtFLQ2uT8NG85Z0tUeWxBSVk' ) {
        
        $client     = $this->googleDriveInitialization();
        $service    = new Google_Service_Drive($client);        
        //we create a folder studio and return the google folderId
        $file = new Google_Service_Drive_DriveFile();
        
        $title       = $keystudio;
        $description = 'Studio key folder';
        $parentId    = '';//'0BwmbtFLQ2uT8NG85Z0tUeWxBSVk'; //0BxIGfs7r1iMcR2k1anEyX05heEk
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
	
        return $createdFile->id;
    }
    
    function createFilesinFolder($folderId, $filecontentpath, $filename, $type) {
        
        $client     = $this->googleDriveInitialization();
        $service    = new Google_Service_Drive($client);
        
        //$client->setAccessToken($saved_token);
	$file = new Google_Service_Drive_DriveFile();
	
	$file->setTitle($filename.'.'.$type); //name of the folder
	
	$mime_types= array(
                    "xls" =>'application/vnd.ms-excel',
                    "xlsx" =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    "xml" =>'text/xml',
                    "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
                    "csv"=>'application/vnd.ms-excel',
                    "tmpl"=>'text/plain',
                    "pdf"=> 'application/pdf',
                    "php"=>'application/x-httpd-php',
                    "jpg"=>'application/octet-stream',
                    "jpeg"=>'application/octet-stream',
                    "png"=>'application/octet-stream',
                    "gif"=>'application/octet-stream',
                    "bmp"=>'application/octet-stream',
                    "txt"=>'text/plain',
                    "doc"=>'application/msword',
                    "js"=>'text/js',
                    "swf"=>'application/x-shockwave-flash',
                    "mp3"=>'audio/mpeg',
                    "zip"=>'application/zip',
                    "rar"=>'application/rar',
                    "tar"=>'application/tar',
                    "arj"=>'application/arj',
                    "cab"=>'application/cab',
                    "html"=>'text/html',
                    "htm"=>'text/html',
                    "eps"=> 'application/postscript',
                    "EPS"=> 'application/postscript',
                    "ai"=> 'application/postscript',
                    "psd"=>'application/octet-stream',
                    "default"=>'application/octet-stream',
                    "folder"=>'application/vnd.google-apps.folder'
        );
        
        // Set the parent folder.
        if ($folderId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($folderId);
            $file->setParents(array($parent));
        }
        
        $createdFile = $service->files->insert(
            $file,
            array(
                  'data' => file_get_contents($filename),
                  'mimeType' => $mime_types[$type],
                  'uploadType' => 'media'
            )
	);
        return $createdFile->id;
    }
    
    
    function deleteFile($fileId) {
        
        $client     = $this->googleDriveInitialization();
        $service    = new Google_Service_Drive($client);
        
        try {
          $service->files->delete($fileId);
          echo 1;
        } catch (Exception $e) {
          echo 0;
        }
    }
    
    function printFilesInFolder($folderId) {
        
        $client     = $this->googleDriveInitialization();
        $service    = new Google_Service_Drive($client);
        
        $pageToken = NULL;

        do {
          try {
            $parameters = array();
            if ($pageToken) {
              $parameters['pageToken'] = $pageToken;
            }
            $children = $service->children->listChildren($folderId, $parameters);

            /*foreach ($children->getItems() as $child) {
              print 'File Id: ' . $child->getId();
            }*/
            return $children;
            $pageToken = $children->getNextPageToken();
          } catch (Exception $e) {
            //print "An error occurred: " . $e->getMessage();
            $pageToken = NULL;
            return 'error';
          }
        } while ($pageToken);
    }
}
?>
