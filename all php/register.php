<?php
session_start();

// Get and sanitize form data
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$mobile   = trim($_POST['mobile'] ?? '');
$password = trim($_POST['password'] ?? '');
//$confirm  = trim($_POST['confirm_password'] ?? '');
//$confirm  = trim($_POST['confirm_password'] ?? '');

// Validate required fields
//if (!$name || !$email || !$mobile || !$password || !$confirm) {
 // echo "Please fill in all fields.";
  //exit;
  if (!$name || !$email || !$mobile || !$password) {
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

//if ($password !== $confirm) {
//  echo "Passwords do not match.";
 // exit;
//}

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


<!--HTML-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 360px;
      margin: 80px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 30px;
      box-sizing: border-box;
      text-align: center;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      background: #28a745;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      margin-top: 10px;
    }

    input[type="submit"]:hover {
      background: #218838;
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 400px) {
      .container {
        margin: 40px 16px;
        padding: 16px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Create Account</h2>
  <form action="register.php" method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="mobile" placeholder="Mobile Number" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <input type="submit" value="Register">
  </form>
  <div class="login-link">
    Already registered? <a href="login.html">Login here</a>
  </div>
</div>

<script>
  const form = document.querySelector("form");

  form.addEventListener("submit", function (e) {
    const name = form.name.value.trim();
    const email = form.email.value.trim();
    const mobile = form.mobile.value.trim();
    const password = form.password.value.trim();
    const confirm = form.confirm_password.value.trim();

    if (!name || !email || !mobile || !password || !confirm) {
      alert("Please fill in all fields.");
      e.preventDefault();
      return;
    }

    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.match(emailPattern)) {
      alert("Invalid email address.");
      e.preventDefault();
      return;
    }

    if (!/^\d{10}$/.test(mobile)) {
      alert("Mobile number must be 10 digits.");
      e.preventDefault();
      return;
    }

    if (password.length < 6) {
      alert("Password must be at least 6 characters.");
      e.preventDefault();
      return;
    }

    if (password !== confirm) {
      alert("Passwords do not match.");
      e.preventDefault();
    }
  });
</script>

</body>
</html>