<?php
// actions/auth_action.php
require_once '../config/db.php';
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'signup') {
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';

        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, gender, role) VALUES (?, ?, ?, ?, 'customer')");
            if ($stmt->execute([$name, $email, $password, $gender])) {
                $_SESSION['success'] = "Account created successfully! Please login.";
                header("Location: ../index.php?page=auth_form");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Registration failed! Email might already be in use.";
            header("Location: ../index.php?page=auth_form#signup");
        }
    } 
    elseif ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['profile_pic'] = $user['profile_pic'];
                header("Location: ../index.php");
            } else {
                $_SESSION['error'] = "Invalid email or password!";
                header("Location: ../index.php?page=auth_form");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Login error! Please try again.";
            header("Location: ../index.php?page=auth_form");
        }
    }
}
?>
