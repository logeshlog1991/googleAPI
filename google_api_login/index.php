<?php

require_once 'app/init.php';

$googleClient = new Google_Client;
$db = new DB;

$auth = new GoogleAuth($db,$googleClient);

if($auth->checkRedirectCode()){

	echo "working";

}

/*$payload = $auth->getPayLoad();

echo "<pre>".$payload."</pre>";*/

?>

<!doctype html>
<html>
	<body>
		<?php if(!$auth->isLoggedIn()):	?>
			<a href="<?php echo $auth->getAuthUrl(); ?>">Sign In With Google</a>
		<?php else: ?>
			<a href="logout.php">Sign Out</a>
		<?php endif; ?>
	</body>					
</html>