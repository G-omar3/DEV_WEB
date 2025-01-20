<?php
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\LinkedIn;

session_start();

$provider = new LinkedIn([
    'clientId'     => getenv('782btyvx13nwjg'),
    'clientSecret' => getenv('WPL_AP1.NuQaW1LdswBtJu8Z.cfsrlw=='),
    'redirectUri'  => getenv('http://localhost/web/linkedin-callback.php'),
]);

if (isset($_GET['state']) && $_GET['state'] === $_SESSION['oauth2state']) {
    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        $userData = $user->toArray();

        echo 'Name: ' . $userData['localizedFirstName'] . ' ' . $userData['localizedLastName'];
        echo 'Email: ' . $userData['emailAddress']; // Si l'e-mail est inclus
        
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'Invalid state';
}
?>
