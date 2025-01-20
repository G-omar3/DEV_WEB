<?php
// Connexion à la base de données
session_start();  // Démarre la session

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement de l'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        echo "All fields are required!";
        exit();
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Crypter le mot de passe
    $role = $_POST['role'];  // Récupère le rôle sélectionné

    // Vérifier si l'email existe déjà dans la base de données
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "This email is already registered.";
        exit();
    }

    // Insertion dans la base de données
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        // Stocker l'ID et le rôle de l'utilisateur dans la session après l'inscription
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['role'] = $role;  // Stocke le rôle de l'utilisateur

        // Rediriger vers la page de profil en fonction du rôle
        if ($_SESSION['role'] == 'admin') {
            header('Location: administrateur/dashboardadministrateur.php');
        } elseif ($_SESSION['role'] == 'teacher') {
            header('Location: enseignant/dashboardenseignant.php');
        } elseif ($_SESSION['role'] == 'student') {
            header('Location: etudiant/dashboardetudiant.php');
        } else {
            echo "Role not recognized. Contact the administrator.";
            exit();
        }
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
