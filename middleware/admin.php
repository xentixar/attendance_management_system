<?php
require_once __DIR__ . '/../vendor/autoload.php';

if (isset($_SESSION['user']['username'])) {
    $username = $_SESSION['user']['username'];
    $database = new Database();
    $conn = $database->connect();

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();
    $conn->close();
    if (!$user || $user['role'] !== 'admin') {
        echo header('Location:../auth/login.php?msg=unauthorized');
    }
}
