<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

include '../config.php';

// Supprimer un étudiant et ses dépendances
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'supprimer' && isset($_POST['etudiant_id'])) {
    $etudiant_id = $_POST['etudiant_id'];

    try {
        // Démarrer une transaction
        $pdo->beginTransaction();

        // Supprimer les notifications associées
        $stmt = $pdo->prepare("DELETE FROM notification WHERE utilisateur_id = ?");
        $stmt->execute([$etudiant_id]);

        // Supprimer les enregistrements dans la table `inscription`
        $stmt = $pdo->prepare("DELETE FROM inscription WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);

        // Supprimer l'étudiant dans la table `users`
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$etudiant_id]);

        // Valider la transaction
        $pdo->commit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
}


// Obtenir la liste des étudiants inscrits
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'student'");
$etudiants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center my-4">Gestion des Étudiants</h2>
        <a href="dashboardadministrateur.php" class="btn btn-custom me-3">
            <i class="fas fa-arrow-left me-2"></i>Retour au Tableau de Bord
        </a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($etudiants) > 0): ?>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <tr>
                            <td><?= htmlspecialchars($etudiant['name']) ?></td>
                            <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                            <td><?= htmlspecialchars($etudiant['email']) ?></td>
                            <td>
                                <form method="POST" action="gestion_etudiants.php" style="display:inline;">
                                    <input type="hidden" name="etudiant_id" value="<?= $etudiant['id'] ?>">
                                    <button type="submit" name="action" value="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Aucun étudiant trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
