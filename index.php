<?php

if(isset($_REQUEST)) {
include 'HttpTranslator.php';
include 'AccessTokenAuthentication.php';

try {
//Client ID of the application.
$clientID       = "**Bing client ID**";
//Client Secret key of the application.
$clientSecret = "**Bing client secret**";
//OAuth Url.
$authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
//Application Scope Url
$scopeUrl     = "http://api.microsofttranslator.com";
//Application grant type
$grantType    = "client_credentials";

$tlUser = "**Textlocal account email address**";
$tlPass = "**Textlocal Password**";

//Create the AccessTokenAuthentication object.
$authObj      = new AccessTokenAuthentication();
//Get the Access token.
$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
//Create the authorization Header string.
$authHeader = "Authorization: Bearer ". $accessToken;

//Set the params.//
$fromLanguage = "en";
$toLanguage   = strtolower(substr($_REQUEST['content'],0,2));
$inputStr     = trim(substr($_REQUEST["content"],3));
$contentType  = 'text/plain';
$category     = 'general';

$params = "text=".urlencode($inputStr)."&to=".$toLanguage."&from=".$fromLanguage;
$translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/Translate?$params";
echo "--TRANSLATE--";
echo $params.'<br>';
echo $inputStr;
echo "--------------";
//Create the Translator Object.
$translatorObj = new HTTPTranslator();

//Get the curlResponse.
$curlResponse = $translatorObj->curlRequest($translateUrl, $authHeader);

//Interprets a string of XML into an object.
$xmlObj = simplexml_load_string($curlResponse);
foreach((array)$xmlObj[0] as $val){
$translatedStr = $val;
}
$data = "uname=".$tlUser."&pword=".$tlPass."&message=".urlencode($translatedStr)."&from=".$_REQUEST['inNumber']."&selectednums=".$_REQUEST['sender']."&info=1";
// Send the POST request with cURL
$ch = curl_init('http://www.txtlocal.com/sendsmspost.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
echo "--REQUEST--";
print_r($_REQUEST);
echo "--SMS URL--";
echo $data;

} catch (Exception $e) {
// Handle exception somehow
}

}
