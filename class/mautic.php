<?php
// constants
CONST URL_MAUTIC   = 'https://plataforma.fuerzateoma.com';
CONST URL_REDIRECT = "http://localhost/mauticLib/pageCallback.php";

/**
* API MAUTIC
* El token se genera con base en el refresh_token que esta en el archivo de json.
* Toca irlo reemplazando cada vez que se necesite.
* En caso de que el token falle se debe generar de nuevo un code con el archivo
* tokenRequest, colocarlo en el 2 servicio access token, tomar el nuevo refresh_token
* generado y guardarlo en el jsonToken.json para que lo ome como base para la obtención
* del siguiente token 
*/
class ApiMautic {

	// class Var
	protected $fileToken;

	/**
	* __Construct
	*/
	public function __Construct() {
		// define Plugin Path
		$plgPath         = plugin_dir_path(__FILE__);
		// define File Path
		$this->fileToken = $plgPath.'jsonToken.json';
	}

	/**
	* request Post
	*/
	function requestPost($urlPath, $jsonData, $accToken = "") {
		//Initiate cURL.
		$ch = curl_init(URL_MAUTIC.$urlPath);
		//Encode the array into JSON.
		$jsonDataEncoded = json_encode($jsonData);
		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);
		// return Transfer As String
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		// validate Access Token
		if ($accToken != "") {
			// set Authorization Header
	        $authorization = "Authorization: Bearer ".$accToken; // Prepare the authorisation token
			//Execute the request
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        }
		//Execute the request
		$crlResponse = curl_exec($ch);
		// curl Close
		curl_close($ch);
		// default Return
		return json_decode($crlResponse);
	}

	/**
	* request Get
	*/
	function requestGET($urlPath, $arrParams = array(), $accToken = "") {
		//Initiate cURL.
		$ch      = curl_init();
		// add Url Params
		$urlPath = (count($arrParams) > 0) ? $urlPath.'?'.http_build_query($arrParams) : $urlPath;
		// set URL
		curl_setopt($ch, CURLOPT_URL, URL_MAUTIC.$urlPath);
		// return Transfer AS Strig
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// validate Access Token
		if ($accToken != "") {
			// set Authorization Header
	        $authorization = "Authorization: Bearer ".$accToken; // Prepare the authorisation token
			//Execute the request
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        }
		//Execute the request
		$reqResult = curl_exec($ch);
		// curl Close
		curl_close($ch);
		// default Return
		return $reqResult;
	}

	/**
	* create Json Refresh Access
	*/
	function createJsonRefreshAccess($refToken) {
		// define Refresh Token
		$json_string = json_encode(array('refresh_token' => $refToken));
		// put Contents
		file_put_contents($this->fileToken, $json_string);
	}

	/**
	* read Json Refresh Access
	*/
	function readJsonRefreshAccess() {
		// create Default Var
		$refToken = "";
		// file Get Contents
		$datJson  = file_get_contents($this->fileToken);
		$objJson  = json_decode($datJson);
		$refToken = $objJson->refresh_token;
		// default Return
		return $refToken;
	}

	/**
	* generate Access Token
	*/
	function generateAccessToken() {
		// create Default Var
		$accToken = "";
		// load Refresh Token
		$refToken = $this->readJsonRefreshAccess();
		// echo "Ref token: ".$refToken;die;
		// JSON Refresh Token.
		$jsonRefreshToken = array(
			'client_id'     => '3_2065lf6lcayscocw08cows4os0os4g0c844kkc8w0gk0og8g08',
			'client_secret' => '4j9vvqnglaqsc00c08ogckk04occwswgg80skgswo0w0kosg84',
			'grant_type'    => 'refresh_token',
			'redirect_uri'  => URL_REDIRECT,
			'refresh_token' => $refToken
		);
		// request Post Token
		$reqAccess = $this->requestPost('/oauth/v2/token', $jsonRefreshToken);
		// validate Object
		if (is_object($reqAccess)) {
			// save New Refresh Token
			$this->createJsonRefreshAccess($reqAccess->refresh_token);
			// define Access Token
			$accToken = $reqAccess->access_token;
		}
		// default Return
		return $accToken;
	}

	/**
	* generate Mautic Contact
	*/
	function generateMauticContact($cstName, $cstEmail, $arrReferer) {
		// create Default Var
		$crtContact = null;
		// generate Access Token
		$accToken = $this->generateAccessToken();
		// validate Access Token
		if ($accToken != "") {
			// define Array Contact
			$arrContact = array_merge (
				$arrReferer,
				array(
					'firstname' => $cstName,
					'email'     => $cstEmail,
					'ipAddress' => $_SERVER['REMOTE_ADDR']
				)
			);
			// request Create Contact
			$crtContact = $this->requestPost('/api/contacts/new', $arrContact, $accToken);
		}
		// default Return
		return $crtContact;
	}
}