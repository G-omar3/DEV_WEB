<?php
// Inclure le SDK de Facebook
require_once 'vendor/autoload.php'; // Assurez-vous que le chemin est correct

// Configuration de l'application
$fb = new \Facebook\Facebook([
    'app_id' => '1159172902553267', // Remplacez par votre App ID
    'app_secret' => '30c7c01e91b99c95b40ff27b7c5dc11b', // Remplacez par votre App Secret
    'default_graph_version' => 'v16.0', // Vous pouvez utiliser la dernière version de l'API Graph
]);

// URL de redirection après l'authentification
$helper = $fb->getRedirectLoginHelper();

// Permissions demandées
$permissions = ['email']; // Ajouter d'autres permissions si nécessaire

// L'URL de redirection pour votre callback
$callbackUrl = 'http://localhost/web/facebook-callback.php'; // Remplacez avec l'URL correcte de votre page callback
$loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

// Redirigez l'utilisateur vers Facebook pour qu'il puisse se connecter
header('Location: ' . $loginUrl );
?>
