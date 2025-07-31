<?php
// Start session if needed
session_start();

// Get data from POST
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$mobile   = trim($_POST['mobile'] ?? '');
$password = trim($_POST['password'] ?? '');
//$confirm  = trim($_POST['confirm_password'] ?? '');

// Basic validation
//if (!$name || !$email || !$mobile || !$password || !$confirm) {
  //echo "Please fill in all fields.";
  //exit;

if (!$name || !$email || !$mobile || !$password ) {
  echo "Please fill in all fields.";
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "Invalid email format.";
  exit;
}

if (!preg_match('/^\d{10}$/', $mobile)) {
  echo "Mobile number must be 10 digits.";
  exit;
}

if (strlen($password) < 6) {
  echo "Password must be at least 6 characters.";
  exit;
}

//if ($password !== $confirm) {
  //echo "Passwords do not match.";
  //exit;
//}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// File to store users
$usersFile = 'users.json';
$users = [];

// Load existing users if file exists
if (file_exists($usersFile)) {
  $json = file_get_contents($usersFile);
  $users = json_decode($json, true) ?? [];
}

// Check for existing email
foreach ($users as $user) {
  if (strtolower($user['email']) === strtolower($email)) {
    echo "Email already registered.";
    exit;
  }
}

// Add new user
$users[] = [
  'name'     => $name,
  'email'    => $email,
  'mobile'   => $mobile,
  'password' => $hashedPassword,
];

// Save to file
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

// Redirect to login page
header("Location: login.html");
exit;
?>