<?php
// Activer l'affichage des erreurs PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
session_start();

// Connexion à la base de données (adapter selon ta configuration)
$host = 'localhost';
$db = 'test';  // Nom de ta base de données
$user = 'root';  // Nom d'utilisateur de la base de données
$pass = '';  // Mot de passe de la base de données

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    exit();
}

// Vérifier si le code d'autorisation est dans l'URL
if (isset($_GET['code'])) {
    $client = new Google_Client();
    $client->setClientId('988630231897-efcpadv5v41v5ddr735ntjlgcf3p5eb3.apps.googleusercontent.com');
    $client->setClientSecret('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');
    $client->setRedirectUri('http://localhost/web/information.php');
    $client->addScope('email');
    $client->addScope('profile');

    // Échanger le code d'autorisation contre un token d'accès
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['access_token'])) {
        $url = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $token['access_token'];
        $response = file_get_contents($url);
        // Décoder la réponse JSON en tableau associatif
        $user_info = json_decode($response, true);

        if (isset($user_info['email'])) {
            // Stocker les informations utilisateur dans la session
            $_SESSION['user_info'] = $user_info;

            // Vérifier si l'email existe déjà dans la base de données
            $email = $user_info['email'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Si l'email existe, afficher le message d'erreur
                $error_message = "Cet email est déjà enregistré. Vous pouvez vous connecter directement.";
            }
        } else {
            echo "Impossible de récupérer les informations utilisateur.";
            exit();
        }
    } else {
        echo 'Erreur lors de la récupération des informations utilisateur.';
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];


    if ($password !== $password_confirm) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    }

    // Hasher le mot de passe avant de le stocker
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertion de l'email et du mot de passe dans la base de données
    $email = $_SESSION['user_info']['email'];
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$email, $hashed_password, $role]); // Ajout du rôle

    $_SESSION['password'] = $password;
    $_SESSION['user_info']['role'] = $role;

    if ($role == 'admin') {
        header('Location: admin_dashboard.php'); // Rediriger vers la page d'admin
    } elseif ($role == 'teacher') {
        header('Location: teacher_dashboard.php'); // Rediriger vers la page des enseignants
    } else {
        header('Location: student_dashboard.php');
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Connexion</title>
    <style>
        /* Style CSS pour la notification */
        .notification {
            padding: 10px;
            margin: 20px 0;
            background-color: #f44336;
            /* Rouge pour une erreur */
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        /* Style global pour le body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            width: 400px;
            max-width: 100%;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-container label {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .form-container button {
            background: linear-gradient(to right, #1d2b64, #046171);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .form-container button:hover {
            background: linear-gradient(to right, #046171, #1d2b64);
        }

        .success {
            color: #28a745;
            margin-bottom: 20px;
        }

        .error {
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">

        <?php if (isset($error_message)): ?>
            <div class="notification">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!isset($exists) || !$exists): ?>
            <!-- Affichage du formulaire de mot de passe et de sélection du rôle uniquement si l'email n'existe pas déjà -->
            <form action="information.php" method="post">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required><br>

                <label for="password_confirm">Confirmer le mot de passe :</label>
                <input type="password" id="password_confirm" name="password_confirm" required><br>

                <!-- Champ de sélection pour le rôle -->
                <label for="role">Choisir le rôle :</label>
                <select name="role" id="role" required style="background-color: #eee; border: none; padding: 12px 15px; margin: 8px 0; width: 100%;">
                    <option value="student">Étudiant</option>
                    <option value="teacher">Enseignant</option>
                    <option value="admin">Administrateur</option>
                </select><br>

                <input type="submit" value="Soumettre">
            </form>
        <?php endif; ?>

    </div>
</body>

</html>