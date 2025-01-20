<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Charge PHPMailer via Composer

$message = ''; // Variable pour stocker le message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));

            // Connexion à la base de données
            $db = new PDO("mysql:host=localhost;dbname=test", "root", "");

            // Mettre à jour la table 'users' avec le token et la date de création
            $query = $db->prepare("UPDATE users SET token = ?, date_create = NOW() WHERE email = ?");
            $query->execute([$token, $email]);

            // Vérifier si l'e-mail existe dans la table 'users'
            if ($query->rowCount() > 0) {
                // Créer le lien de réinitialisation
                $resetLink = "http://localhost/web/reset_password.php?token=" . urlencode($token);

                // Configurer PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'omargaga121212@gmail.com';
                $mail->Password = 'fgpo uogc dsgh ojpr'; // Mot de passe ou mot de passe d'application
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('developementweb@gmail.com', 'User');
                $mail->addAddress($email);

                // Contenu de l'e-mail
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$resetLink'>$resetLink</a>";

                if ($mail->send()) {
                    $message = '<p class="success">Un e-mail de réinitialisation a été envoyé.</p>';
                } else {
                    $message = '<p class="error">Erreur lors de l\'envoi de l\'e-mail : ' . $mail->ErrorInfo . '</p>';
                }
            } else {
                $message = '<p class="error">Aucun utilisateur trouvé avec cet email.</p>';
            }

        } catch (Exception $e) {
            $message = '<p class="error">Erreur : ' . $e->getMessage() . '</p>';
        }
    } else {
        $message = '<p class="error">Adresse e-mail invalide.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <style>
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

        .form-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
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
        <h2>Réinitialisation de mot de passe</h2>
        <form method="POST">
            <label for="email">Entrez votre adresse e-mail :</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Envoyer</button>
            <?php if (!empty($message)) echo $message; ?>
        </form>
    </div>
</body>

</html>
