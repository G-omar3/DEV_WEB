<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un professeur responsable
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.html");
    exit();
}

include '../config.php';

// Obtenir l'ID du module à supprimer
$module_id = $_GET['id'];

// Vérifier si le module appartient à la classe dont le professeur est responsable
$stmt = $pdo->prepare("DELETE FROM Module WHERE id = ? AND classe_id = (SELECT id FROM Classe WHERE professeur_responsable_id = ?)");
$stmt->execute([$module_id, $_SESSION['user_id']]);

header("Location: gestion_modules.php");
exit();
?>
