<?php
//Defining
require_once __DIR__.'/vendor/src/Google/autoload.php';

const CLIENT_ID = '760048098295-5irmr74nsn35vv1sonu16vvig5b5jo0i.apps.googleusercontent.com';
const CLIENT_SECRET = 'wm2i6wLoTpfdF3HwgIPie-3M';
const REDIRECT_URI = 'http://localhost/gplus-quickstart-php-master/logout.php';

session_start();

$client = new Google_Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);
$client->setScopes('email');
$plus = new Google_Service_Plus($client);

if (isset($_REQUEST['logout'])) {
   session_unset();
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  $me = $plus->people->get('me');


  // Get User data
  $id = $me['id'];
  $name =  $me['displayName'];
  $email =  $me['emails'][0]['value'];
  $profile_image_url = $me['image']['url'];
  $cover_image_url = $me['cover']['coverPhoto']['url'];
  $profile_url = $me['url'];
} else {
  // get the login url   
  $authUrl = $client->createAuthUrl();
}
?>

<!-- HTML CODE with Embeded PHP-->
<div>
    <?php
    /*
     * If login url is there then display login button
     * else print the retieved data
    */
    if (isset($authUrl)) {
        echo "<a class='login' href='" . $authUrl . "'><img src='signin_button.png' height='50px'/></a>";
    } else {
        print "ID: {$id} <br>";
        print "Name: {$name} <br>";
        print "Email: {$email } <br>";
        print "Image : {$profile_image_url} <br>";
        print "Cover  :{$cover_image_url} <br>";
        print "Url: {$profile_url} <br><br>";
        echo "<a class='logout' href='?logout'><button>Logout</button></a>";
    }
    ?>
</div>
