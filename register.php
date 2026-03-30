<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; 
$msg = "";

if(isset($_POST['register'])){
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $type = $_POST['user_type'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    if(mysqli_num_rows($check) > 0){
        $msg = "Username already exists!";
    } else {
        $query = "INSERT INTO users (username, password, user_type) VALUES ('$user', '$pass', '$type')";
        if(mysqli_query($conn, $query)){
            echo "<script>alert('Account Created Successfully!'); window.location='login.php';</script>";
        } else {
            $msg = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADBU Canteen - Registration</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background-color: #f1f3f6; }
        
        /* Navbar with Logo */
        .navbar { 
            background: #2874f0; 
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

        .container { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 85vh; 
        }

        .form-card { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            width: 350px; 
            text-align: center;
        }

        /* Color fixed to white for Navbar text */
        .navbar h2 { color: white; margin: 0; display: flex; align-items: center; }
        
        /* Card Heading color */
        .form-card h2 { color: #333; margin-bottom: 20px; }
        
        input, select, button { 
            width: 100%; 
            margin: 10px 0; 
            padding: 12px; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            border-radius: 5px;
        }

        button { 
            background-color: #28a745; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold; 
            transition: 0.3s;
        }

        button:hover { background-color: #218838; }

        .footer-link { font-size: 14px; margin-top: 15px; color: #555; }
        .footer-link a { color: #2874f0; text-decoration: none; font-weight: bold; }
        .error { color: red; font-size: 13px; }
    </style>
</head>
<body>

<div class="navbar">
    <img src="logo.png" alt="ADBU Logo">
    <h2>ADBU <span style="font-weight:300; margin-left:5px;">Canteen</span></h2>
</div>

<div class="container">
    <div class="form-card">
        <h2>Create Account</h2>
        <?php if($msg != "") echo "<p class='error'>$msg</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <select name="user_type" required>
                <option value="">Select User type</option>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
            </select>
            
            <button type="submit" name="register">Register Now</button>
        </form>
        
        <div class="footer-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>