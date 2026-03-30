<?php
include 'db.php';
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    echo "<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
            <h2 style='color:#e74c3c;'>⚠️ Access Denied!</h2>
            <p>Only authorized Canteen Administrators can access this portal.</p>
            <a href='login.php' style='color:#3498db; text-decoration:none; font-weight:bold;'>Go back to Login</a>
          </div>";
    exit();
}
if(isset($_POST['add_dish'])){
    $name = mysqli_real_escape_string($conn, $_POST['dish_name']);
    $price = $_POST['price'];
    $url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $cat = $_POST['category']; 
    mysqli_query($conn, "INSERT INTO menu (dish_name, price, image_url, category) VALUES ('$name', '$price', '$url', '$cat')");
    header("Location: admin.php?added=1");
    exit();
}
if(isset($_POST['update_full_dish'])){
    $id = $_POST['dish_id'];
    $name = mysqli_real_escape_string($conn, $_POST['u_name']);
    $price = $_POST['u_price'];
    $url = mysqli_real_escape_string($conn, $_POST['u_url']);
    $cat = $_POST['u_cat'];
    mysqli_query($conn, "UPDATE menu SET dish_name='$name', price='$price', image_url='$url', category='$cat' WHERE id='$id'");
    header("Location: admin.php?updated=1");
    exit();
}
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM menu WHERE id='$id'");
    header("Location: admin.php?deleted=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADBU Admin Panel | Canteen Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --adbu-blue: #2874f0; --success: #2ecc71; --danger: #e74c3c; 
            --bg: #f8f9fa; --sidebar: #ffffff; --text: #2c3e50; --card: #ffffff;
        }
        body.dark-mode { 
            --bg: #121212; --sidebar: #2d2d2d; --text: #ecf0f1; --card: #2d2d2d;
        }
        
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); color: var(--text); margin: 0; transition: 0.3s; }

        .navbar { 
            background: var(--adbu-blue); color: white; padding: 10px 4%; 
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2); position: sticky; top: 0; z-index: 1000;
        }
        .nav-right { display: flex; align-items: center; gap: 15px; }

        .theme-btn { background: rgba(255,255,255,0.2); border: none; color: white; padding: 8px 15px; border-radius: 20px; cursor: pointer; transition: 0.3s; font-weight: bold; }
        .theme-btn:hover { background: rgba(255,255,255,0.4); }

        .logout-btn { background: var(--danger); color: white !important; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; border: 1px solid rgba(255,255,255,0.3); }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        .card { background: var(--card); border-radius: 12px; padding: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); margin-bottom: 30px; }
        h3 { margin-top: 0; color: #3498db; display: flex; align-items: center; gap: 10px; font-size: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px; }

        /* Table Styling */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 850px; }
        th { background: #f1f3f6; color: #555; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; padding: 15px; text-align: left; }
        body.dark-mode th { background: #3d3d3d; color: #bbb; }
        td { padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); vertical-align: middle; }

        /* Input Controls */
        input, select { 
            padding: 10px; border: 1px solid #ddd; border-radius: 6px; 
            background: var(--bg); color: var(--text); font-size: 14px; width: 100%; box-sizing: border-box;
        }
        .btn-save { background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; transition: 0.2s; }
        .btn-save:hover { background: #2980b9; }
        .btn-delete { color: var(--danger); text-decoration: none; font-size: 12px; font-weight: bold; display: block; text-align: center; margin-top: 8px; }

        /* Add Form Grid */
        .add-form-row { display: grid; grid-template-columns: 2fr 1fr 2fr 1fr 1fr; gap: 15px; align-items: end; }
        
        .status-msg { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from {opacity: 0;} to {opacity: 1;} }
    </style>
</head>
<body id="body">

<nav class="navbar">
    <div style="display:flex; align-items:center; gap:12px;">
        <img src="logo.png" alt="Logo" style="height:45px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
        <h2 style="margin:0; font-size: 1.4rem;">ADBU <span style="font-weight:300;">Admin Panel</span></h2>
    </div>
    <div class="nav-right">
        <a href="index.php" style="color:white; text-decoration:none; font-size: 14px; margin-right: 10px;"><i class="fas fa-eye"></i> User View</a>
        <button class="theme-btn" onclick="toggleMode()"><i class="fas fa-adjust"></i> Mode</button>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
    </div>
</nav>

<div class="container">
    
    <?php if(isset($_GET['added'])): ?>
        <div class="status-msg" style="background: #d4edda; color: #155724;">✨ New item added to menu successfully!</div>
    <?php elseif(isset($_GET['updated'])): ?>
        <div class="status-msg" style="background: #cce5ff; color: #004085;">✅ Item details updated successfully!</div>
    <?php elseif(isset($_GET['deleted'])): ?>
        <div class="status-msg" style="background: #f8d7da; color: #721c24;">🗑️ Item removed from inventory.</div>
    <?php endif; ?>

    <div class="card">
        <h3><i class="fas fa-plus-circle"></i> Add New Canteen Item</h3>
        <form method="POST" class="add-form-row">
            <div><label style="font-size: 11px; font-weight: bold;">Dish Name</label><input type="text" name="dish_name" placeholder="e.g. Chicken Biryani" required></div>
            <div><label style="font-size: 11px; font-weight: bold;">Price (₹)</label><input type="number" name="price" placeholder="e.g. 150" required></div>
            <div><label style="font-size: 11px; font-weight: bold;">Image Link</label><input type="text" name="image_url" placeholder="Paste .jpg/.png URL" required></div>
            <div>
                <label style="font-size: 11px; font-weight: bold;">Category</label>
                <select name="category" required>
                    <option value="Veg">Veg 🍃</option>
                    <option value="Non-Veg">Non-Veg 🍗</option>
                </select>
            </div>
            <button type="submit" name="add_dish" class="btn-save" style="background: var(--success); height: 42px;">REGISTER ITEM</button>
        </form>
    </div>

    <div class="card">
        <h3><i class="fas fa-tasks"></i> Inventory & Menu Management</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="25%">Item Name & Price</th>
                        <th width="35%">Image URL (Source)</th>
                        <th width="15%">Category</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM menu ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($res)){
                    ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="dish_id" value="<?php echo $row['id']; ?>">
                            <td>
                                <input type="text" name="u_name" value="<?php echo $row['dish_name']; ?>" style="font-weight:bold; margin-bottom:5px;">
                                <input type="number" name="u_price" value="<?php echo $row['price']; ?>" style="color:#2874f0; font-weight:bold;">
                            </td>
                            <td>
                                <input type="text" name="u_url" value="<?php echo $row['image_url']; ?>" style="font-size: 11px; opacity: 0.8;">
                                <a href="<?php echo $row['image_url']; ?>" target="_blank" style="font-size: 10px; color: #3498db; margin-top: 5px; display: inline-block;">View Image</a>
                            </td>
                            <td>
                                <select name="u_cat" style="font-weight:bold;">
                                    <option value="Veg" <?php if($row['category']=='Veg') echo 'selected'; ?>>Veg 🍃</option>
                                    <option value="Non-Veg" <?php if($row['category']=='Non-Veg') echo 'selected'; ?>>Non-Veg 🍗</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="update_full_dish" class="btn-save">SAVE CHANGES</button>
                                <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to remove this item?')"><i class="fas fa-trash"></i> DELETE ITEM</a>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleMode() {
        document.getElementById('body').classList.toggle('dark-mode');
    }
</script>
<?php include 'footer.php'; ?>
</body>
</html>