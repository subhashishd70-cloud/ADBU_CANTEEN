<?php
include 'db.php';
session_start();

if(isset($_POST['login'])){
    $user = mysqli_real_escape_string($conn, $_POST['username']); 
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $type = $_POST['user_type'];

    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass' AND user_type='$type'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $_SESSION['user'] = $user;
        $_SESSION['role'] = $type;
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Invalid Login Details');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADBU Canteen - Login</title>
    <style>
        :root { --adbu-blue: #2874f0; --bg-light: #f1f3f6; }
        body { background: var(--bg-light); font-family: 'Segoe UI', Arial, sans-serif; margin: 0; display: flex; flex-direction: column; height: 100vh; }
        
        /* Navbar with Logo */
        .navbar { 
            background: var(--adbu-blue); 
            color: white; 
            padding: 10px 5%; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            display: flex;
            align-items: center;
        }
        
        .navbar img {
            height: 45px;
            margin-right: 15px;
        }

        .navbar h2 { color: white; margin: 0; display: flex; align-items: center; }

        .auth-container { 
            background: white; width: 350px; margin: auto; padding: 40px; 
            border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); text-align: center; 
        }
        
        /* Auth card heading color */
        .auth-container h2 { color: #333; margin-bottom: 25px; }
        
        input, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        
        .btn-login { 
            background: var(--adbu-blue); color: white; border: none; padding: 12px; 
            width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .btn-login:hover { background: #1a56c1; }
        .register-link { margin-top: 20px; font-size: 14px; }
        .register-link a { color: var(--adbu-blue); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar">
    <img src="logo.png" alt="ADBU Logo">
    <h2 style="margin:0;">ADBU <span style="font-weight:300; margin-left:5px;">Canteen</span></h2>
</nav>

<div class="auth-container">
    <h2><b>Login</b> to Portal</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="user_type" required>
            <option value="" disabled selected>Select User Type</option>
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="login" class="btn-login">Login</button>
        <div class="register-link">
            New here? <a href="register.php">Create an account</a>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>