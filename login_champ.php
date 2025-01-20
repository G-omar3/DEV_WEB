<?php
session_start(); // Démarre la session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement de la connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs
   

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparation de la requête SQL pour éviter les injections
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // L'email n'existe pas
        header("Location: ../login.html?error=user_not_found");
        exit();
    }

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Stocker les données utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirection en fonction du rôle
        switch ($_SESSION['role']) {
            case 'admin':
                header('Location: administrateur/dashboardadministrateur.php');
                break;
            case 'teacher':
                header('Location: enseignant/dashboardenseignant.php');
                break;
            case 'student':
                header('Location: etudiant/dashboardetudiant.php');
                break;
            default:
                header("Location: ../login.html?error=role_not_recognized");
        }
        exit();
    } else {
        // Mot de passe incorrect
        echo("Invalide password");
        exit();
    }
}

$conn->close();
?>
