<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

include '../config.php';

// Gérer l'ajout d'un enseignant
// Gérer l'ajout d'un enseignant
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_enseignant'])) {
    if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['mot_de_passe'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        // Hacher le mot de passe
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Préparer la requête SQL avec le mot de passe haché
        $stmt = $pdo->prepare("INSERT INTO users (`name`, prenom, email, `password`, `role`) VALUES (?, ?, ?, ?, 'teacher')");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hache]);

        $message = "Enseignant ajouté avec succès.";
    } else {
        $message = "Tous les champs sont requis.";
    }
}





// Obtenir la liste des enseignants
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'teacher'");
$enseignants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Enseignants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .card-header {
            background: linear-gradient(45deg, #5e72e4, #825ee4);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: bold;
        }
        .btn-custom {
            background: linear-gradient(45deg, #5e72e4, #825ee4);
            border: none;
            color: white;
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #4a5cd1, #7046d1);
            color: white;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead {
            background-color: #5e72e4;
            color: white;
        }
        .module-form {
            background-color: #f8f9fe;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary"><i class="fas fa-user-tie me-3"></i>Gestion des Enseignants</h1>
            <a href="dashboardadministrateur.php" class="btn btn-custom me-3">
        <i class="fas fa-arrow-left me-2"></i>Retour au Tableau de Bord
    </a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card module-form">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter un Enseignant</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="gestion_enseignants.php">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="mot_de_passe" class="form-label">Mot de Passe</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                            </div>
                            <button type="submit" name="ajouter_enseignant" class="btn btn-custom w-100"><i class="fas fa-plus-circle me-2"></i>Ajouter Enseignant</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-list-alt me-2"></i>Liste des Enseignants</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($enseignants) > 0): ?>
                                        <?php foreach ($enseignants as $enseignant): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($enseignant['name']) ?></td>
                                                <td><?= htmlspecialchars($enseignant['prenom']) ?></td>
                                                <td><?= htmlspecialchars($enseignant['email']) ?></td>
                                                <td>
                                                    <a href="modifier_enseignant.php?id=<?= $enseignant['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <a href="supprimer_enseignant.php?id=<?= $enseignant['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ?');"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Aucun enseignant trouvé</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
