<?php
require_once 'vendor/autoload.php';

session_start();



// Créer une instance du client Google
$client = new Google_Client();
$client->setClientId('988630231897-6pkkn46q5lnithmatggnfm4lrubddikv.apps.googleusercontent.com'); // Remplacez par votre Client ID
$client->setClientSecret('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'); // Remplacez par votre Client Secret
$client->setRedirectUri('http://localhost/web/callback.php'); // URI de redirection
$client->addScope('email');
$client->addScope('profile');

// Générer l'URL de connexion Google
$login_url = $client->createAuthUrl();

// Rediriger l'utilisateur vers l'URL de connexion Google
header('Location: ' . $login_url);



?>




