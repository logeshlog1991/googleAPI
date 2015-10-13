<?php
class GoogleAuth{

	protected $db;

	protected $client;

	public function __construct(DB $db = null,Google_Client $googleClient = null){
		
		$this->client = $googleClient;

		if($this->client){
			$this->client->setClientId('760048098295-5irmr74nsn35vv1sonu16vvig5b5jo0i.apps.googleusercontent.com');
			$this->client->setClientSecret('wm2i6wLoTpfdF3HwgIPie-3M');
			$this->client->setRedirectUri('http://localhost/google_api_login/index.php');
			$this->client->setScopes('email');
		}

	}

	public function isLoggedIn(){

		return isset($_SESSION['access_token']);

	}

	public function getAuthUrl(){

		return $this->client->createAuthUrl();

	}

	public function checkRedirectCode(){

		if(isset($_GET['code']))
		{
			$this->client->authenticate($_GET['code']);
			$this->setToken($this->client->getAccessToken());

			/*$payload = $this->getPayLoad();
			echo '<pre>',print_r($payload),'</pre>';*/

			$this->storeUser($this->getPayLoad());

			return true;
		}

		return false;

	}

	public function setToken($token)
	{

		$_SESSION['access_token'] = $token;
		$this->client->setAccessToken($token);

	}

	public function logout()
	{

		unset($_SESSION['access_token']);
			
	}

	protected function getPayLoad()
	{

		$payload = $this->client->verifyIdToken()->getAttributes()['payload'];
		return $payload;

	}

	protected function storeUser($payload)
	{
		//echo $payload['email'];
		$email = $payload['email'];
		$id = $payload['sub'];
		$conn = new mysqli('localhost','root','','googleAPI');
		$insert = $conn->query("insert into google_users (google_id,email) values ('$id','$email')");
		return true;

	}
}