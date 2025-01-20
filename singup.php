<?php
require_once 'vendor/autoload.php';

session_start();

// Créer une instance du client Google
$client = new Google_Client();
$client->setClientId('988630231897-efcpadv5v41v5ddr735ntjlgcf3p5eb3.apps.googleusercontent.com'); // Remplacez par votre Client ID
$client->setClientSecret('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'); // Remplacez par votre Client Secret
$client->setRedirectUri('http://localhost/web/information.php'); // URI de redirection
$client->addScope('email');
$client->addScope('profile');

// Générer l'URL de connexion Google
$login_url = $client->createAuthUrl();

// Rediriger l'utilisateur vers l'URL de connexion Google
header('Location: ' . $login_url);
exit();
?>
