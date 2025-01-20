<?php
$message = ''; // Variable pour stocker les messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $db = new PDO("mysql:host=localhost;dbname=test", "root", "");

    // Vérifier si le token est valide
    $query = $db->prepare("SELECT email FROM users WHERE token = ? AND date_create >= NOW() - INTERVAL 1 HOUR");
    $query->execute([$token]);
    $email = $query->fetchColumn();

    if ($email) {
        // Mettre à jour le mot de passe dans la base de données
        $update = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$hashedPassword, $email]);

        // Supprimer le token après utilisation
        $delete = $db->prepare("UPDATE users SET token = NULL WHERE token = ?");
        $delete->execute([$token]);

        $message = '<p class="success">Votre mot de passe a été réinitialisé avec succès.</p>';
    } else {
        $message = '<p class="error">Le lien de réinitialisation est invalide ou a expiré.</p>';
    }
} else {
    $token = $_GET['token'] ?? '';
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
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label for="new_password">Entrez votre nouveau mot de passe :</label>
            <input type="password" name="new_password" id="new_password" required>
            <button type="submit">Réinitialiser le mot de passe</button>
            <?php if (!empty($message)) echo $message; ?>

        </form>
    </div>
</body>

</html>
