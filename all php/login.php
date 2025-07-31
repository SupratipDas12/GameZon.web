<?php
session_start();

$con = new mysqli("localhost", "root", "root", "web");

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
  echo "Please fill in all fields.";
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "Invalid email format.";
  exit;
}

// Fetch user from DB
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
  $_SESSION['email'] = $email;
  header("Location: dashboard.php");
  exit;
} else {
  echo "Incorrect email or password.";
}
?>