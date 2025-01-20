<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

include '../config.php';

// Vérifier si l'ID de l'enseignant est fourni
if (isset($_GET['id'])) {
    $enseignant_id = $_GET['id'];

    // Supprimer l'enseignant de la table Utilisateur
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'teacher'");
    $stmt->execute([$enseignant_id]);

    $message = "Enseignant supprimé avec succès.";

    // Rediriger vers la page de gestion des enseignants avec un message de succès
    header("Location: gestion_enseignants.php?message=" . urlencode($message));
    exit();
} else {
    // Rediriger avec un message d'erreur si l'ID n'est pas fourni
    $error = "Aucun enseignant sélectionné pour la suppression.";
    header("Location: gestion_enseignants.php?error=" . urlencode($error));
    exit();
}
