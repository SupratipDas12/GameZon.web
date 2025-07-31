<?php
session_start();

// Hardcoded valid credentials
$validEmail = "user@example.com";
$validPassword = "1234";

// Get data from form
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Basic validation
if ($email === '' || $password === '') {
  echo "Please fill in all fields.";
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "Invalid email format.";
  exit;
}

// Check credentials
if ($email === $validEmail && $password === $validPassword) {
  $_SESSION['email'] = $email;
  header("Location: dashboard.php");
  exit;
} else {
  echo "Incorrect email or password.";
}
?>