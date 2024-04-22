<?php
session_start();

// Database connection
include 'connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Blog</title>
</head>
<body>

<div id="menu">
    <a href="index.php">Beranda</a> |
    <a href="post.php">Post</a> |
    <a href="account.php">Akun</a> |
    <?php if(isset($_SESSION['username'])): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

<div id="content">
    <h1>Selamat datang di Simple Blog</h1>
    <p>Ini adalah blog sederhana dengan CRUD untuk posting dan akun.</p>
</div>

</body>
</html>
