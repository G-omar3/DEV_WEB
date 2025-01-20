<?php
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\LinkedIn;


$provider = new LinkedIn([
    'clientId'     => '782btyvx13nwjg',  
    'clientSecret' => 'WPL_AP1.NuQaW1LdswBtJu8Z.cfsrlw==',  
    'redirectUri'  => 'http://localhost/web/linkedin-callback.php',  
]);

$authUrl = $provider->getAuthorizationUrl();

session_start();
$_SESSION['oauth2state'] = $provider->getState();

header('Location: ' . $authUrl);
exit();  
?>
