<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.html");
    exit();
}

include '../config.php';

// Obtenir l'ID de l'étudiant à partir de la session
$etudiant_id = $_SESSION['user_id'];

// Obtenir les informations de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();

// Mettre à jour les informations de l'étudiant
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    // Vérifier si un nouveau mot de passe a été fourni
    if (!empty($_POST['mot_de_passe'])) {
        $mot_de_passe = $_POST['mot_de_passe'];

        // Hachage du mot de passe avant de l'enregistrer
        $mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    } else {
        // Si le mot de passe n'est pas modifié, garder l'ancien mot de passe
        $mot_de_passe = $etudiant['password'];  // Assurez-vous que la clé 'password' existe et correspond dans la base
    }

    // Mettre à jour les informations de l'utilisateur
    $stmt = $pdo->prepare("UPDATE users SET `name` = ?, prenom = ?, email = ?, `password` = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $etudiant_id]);

    $message = "Profil mis à jour avec succès.";
    header("Location: dashboardetudiant.php");
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer le Profil</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center my-4">Gérer le Profil</h2>
        <a href="dashboardetudiant.php" class="btn btn-primary"><i class="fas fa-tachometer-alt me-2"></i>Retour au Dashboard</a>


        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="gerer_profil.php">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($etudiant['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($etudiant['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" >
                </div>
            <button type="submit" name="update_profile" class="btn btn-primary btn-block">Mettre à jour</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
