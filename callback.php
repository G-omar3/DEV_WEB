<?php
require_once 'vendor/autoload.php';
require_once 'config.php'; // Fichier pour les informations de connexion à la base de données

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

        // Vérifier si l'email a été récupéré
        if (isset($user_info->email)) {
            $email = $user_info->email;
            $_SESSION['user_email'] = $email;

            // Connexion à la base de données
            $conn = new mysqli("localhost", "root", "", "test");

            // Vérifier la connexion à la base de données
            if ($conn->connect_error) {
                die("Erreur de connexion à la base de données : " . $conn->connect_error);
            }

            // Rechercher l'utilisateur dans la base de données à partir de son email
            $stmt = $conn->prepare("SELECT name, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Récupérer les informations utilisateur
                $user = $result->fetch_assoc();
                $name = $user['name']; // Récupération du nom de l'utilisateur depuis la base
                $role = $user['role']; // Récupération du rôle de l'utilisateur depuis la base
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = $role;

                // Rediriger en fonction du rôle
                if ($role === 'admin') {
                    header('Location: administrateur/dashboardadministrateur.php');
                } elseif ($role === 'teacher') {
                    header('Location: enseignant/dashboardenseignant.php');
                } elseif ($role === 'student') {
                    header('Location: etudiant/dashboardetudiant.php');
                }
                exit();
            } else {
                echo "L'utilisateur avec l'adresse e-mail $email n'est pas trouvé dans la base de données.";
            }
        } else {
            echo "Impossible de récupérer les informations utilisateur via l'API Google.";
        }
    } else {
        echo "Erreur lors de la récupération du token d'accès.";
    }
} else {
    echo "Code d'autorisation manquant.";
}
?>
