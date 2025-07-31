<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
  header("Location: login.html");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>
  <p>You are now logged in.</p>
  <a href="logout.php">Logout</a>
</body>
</html>