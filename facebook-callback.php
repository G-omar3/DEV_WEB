<?php
// Inclure le SDK de Facebook
require_once 'vendor/autoload.php'; // Assurez-vous que le chemin est correct

// Configuration de l'application
$fb = new \Facebook\Facebook([
    'app_id' => '1159172902553267', // Remplacez par votre App ID
    'app_secret' => '30c7c01e91b99c95b40ff27b7c5dc11b', // Remplacez par votre App Secret
    'default_graph_version' => 'v16.0',
]);

// URL de redirection après l'authentification
$helper = $fb->getRedirectLoginHelper();

// Vérifiez si un code est présent dans l'URL
try {
    // Obtenez l'access token
    $accessToken = $helper->getAccessToken();

    // Si l'utilisateur a autorisé l'application
    if (!isset($accessToken)) {
        echo 'Erreur lors de l\'authentification, aucun token d\'accès trouvé !';
        exit;
    }

    // Si nous avons un access token, nous pouvons récupérer les informations de l'utilisateur
    $response = $fb->get('/me?fields=id,name,email', $accessToken); // Demander l'email et le nom de l'utilisateur
    $user = $response->getGraphUser();

    echo 'Nom : ' . $user['name'] . '<br>';
    echo 'Email : ' . $user['email'] . '<br>';
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Erreur de réponse de l\'API Facebook : ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Erreur de SDK Facebook : ' . $e->getMessage();
    exit;
}
