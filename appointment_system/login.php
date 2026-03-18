<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: admin.php");
    } else {
        echo "Invalid login!";
    }
    
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 col-md-4 mx-auto">
        <h3>Admin Login</h3>
        <form method="POST">
            <input class="form-control mb-2" type="text" name="username" placeholder="Username"><br>
            <input class="form-control mb-2" type="password" name="password" placeholder="Password"><br>
            <button class="btn btn-primary w-100">Login</button>
        
        </form>
    </div>
</div>
</body>
</html>