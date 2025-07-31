<?php
session_start();

// Get and sanitize form data
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$mobile   = trim($_POST['mobile'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm  = trim($_POST['confirm_password'] ?? '');

// Validate required fields
if (!$name || !$email || !$mobile || !$password || !$confirm) {
  echo "Please fill in all fields.";
  exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "Invalid email format.";
  exit;
}

// Validate mobile
if (!preg_match('/^\d{10}$/', $mobile)) {
  echo "Mobile number must be 10 digits.";
  exit;
}

// Validate password
if (strlen($password) < 6) {
  echo "Password must be at least 6 characters.";
  exit;
}

if ($password !== $confirm) {
  echo "Passwords do not match.";
  exit;
}

// Hash the password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// FILE: Load or create users.json
$usersFile = 'users.json';
$users = [];

if (file_exists($usersFile)) {
  $json = file_get_contents($usersFile);
  $users = json_decode($json, true) ?? [];
}

// Check for duplicate email
foreach ($users as $user) {
  if (strtolower($user['email']) === strtolower($email)) {
    echo "Email already registered.";
    exit;
  }
}

// Add new user to array
$users[] = [
  'name'     => $name,
  'email'    => $email,
  'mobile'   => $mobile,
  'password' => $hashedPassword,
];

// Save users to JSON file
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

// DATABASE: Insert into MySQL
$con = new mysqli("0.0.0.0", "root", "root", "web");

// Check connection
if ($con->connect_error) {
  die("Database connection failed: " . $con->connect_error);
}

// Use prepared statement to insert name + email into MySQL table
$stmt = $con->prepare("INSERT INTO xxx (name, email) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $email);

if ($stmt->execute()) {
  // Success: both JSON and MySQL
  header("Location: login.html");
  exit;
} else {
  echo "MySQL error: " . $stmt->error;
}

$stmt->close();
$con->close();
?>