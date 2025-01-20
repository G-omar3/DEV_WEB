<?php
require_once 'vendor/autoload.php';

session_start();

// Vérifier si le code d'autorisation est dans l'URL
if (isset($_GET['code'])) {
    // Créer une instance du client Google
    $client = new Google_Client();
    $client->setClientId('988630231897-6pkkn46q5lnithmatggnfm4lrubddikv.apps.googleusercontent.com'); // Remplacez par votre Client ID
    $client->setClientSecret('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'); // Remplacez par votre Client Secret
    $client->setRedirectUri('http://localhost/web/callback.php'); // URI de redirection
    $client->addScope('email');
    $client->addScope('profile');

    // Échanger le code d'autorisation contre un token d'accès
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Vérifier si le token d'accès a été récupéré avec succès
    if (isset($token['access_token'])) {
        // Stocker le token dans la session
        $_SESSION['access_token'] = $token['access_token'];

        // Récupérer les informations de l'utilisateur à partir de l'API Google
        $url = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $token['access_token'];
        $response = file_get_contents($url);
        $user_info = json_decode($response);

        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_info'] = $user_info;

        // Rediriger vers la page 'welcome.php'
        header('Location: profile_google.php');
        exit();
    } else {
        echo 'Erreur lors de la récupération du token d\'accès.';
    }
} else {
    echo 'Code d\'autorisation manquant.';
}
?>
