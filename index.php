<?php
include 'db.php'; 
session_start();
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit(); }

if(isset($_POST['submit_review'])){
    $user_name = $_SESSION['user']; 
    $dish_id = $_POST['dish_id'];
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
    
    $review_query = "INSERT INTO reviews (user_id, dish_id, rating, comment) VALUES ('$user_name', '$dish_id', '$rating', '$comment')";
    
    if(mysqli_query($conn, $review_query)){
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADBU Professional Canteen Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --primary: #fb641b; --success: #388e3c; --adbu-blue: #2874f0; 
            --logout: #e74c3c; --bg: #f1f3f6; --card: #ffffff; --text: #212121;
        }
        body.dark-mode { --bg: #121212; --card: #1e1e1e; --text: #ffffff; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', Arial, sans-serif; margin: 0; transition: 0.3s; }
        
        .navbar { background: var(--adbu-blue); color: white; padding: 12px 5%; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .nav-left { display: flex; align-items: center; gap: 15px; }
        .nav-right { display: flex; align-items: center; gap: 15px; }
        
        .logout-btn { background: var(--logout); color: white; padding: 8px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .theme-btn { background: rgba(255,255,255,0.2); border: none; color: white; padding: 8px 15px; border-radius: 20px; cursor: pointer; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 30px 5%; }
        .dish-card { background: var(--card); border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .rating-badge { background: var(--success); color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; }
        .btn-post { background: var(--primary); color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        
        .review-box { margin-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 10px; }
        .review-item { font-size: 13px; padding: 10px 0; border-bottom: 1px dashed rgba(0,0,0,0.1); }
        .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .review-user { font-weight: bold; color: var(--adbu-blue); }
        .review-date { color: #878787; font-size: 11px; }
        .filter-nav { text-align: center; margin: 25px 0; }
        .filter-link {
            text-decoration: none; padding: 10px 25px; border-radius: 30px;
            margin: 5px; font-weight: bold; display: inline-block;
            border: 2px solid #2874f0; color: #2874f0; transition: 0.3s;
            background: white;
        }
        .filter-link:hover, .filter-link.active { background: #2874f0; color: white; }
        .type-badge {
            position: absolute; top: 12px; left: 12px; padding: 3px 10px;
            border-radius: 5px; color: white; font-size: 11px; font-weight: bold; z-index: 10;
        }
        .bg-veg { background: #388e3c; }
        .bg-non { background: #e74c3c; }
    </style>
</head>
<body id="body">

<nav class="navbar">
    <div class="nav-left">
        <img src="logo.png" alt="ADBU Logo" style="height: 40px; margin-right: 10px;">
        <h2 style="margin:0;">ADBU <span style="font-weight:300;">Canteen</span></h2>
    </div>

    <div class="nav-right">
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="admin.php" style="background: #ff9f00; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px;">
               ⚙️ Admin Panel
            </a>
        <?php endif; ?>

        <button class="theme-btn" onclick="toggleMode()"><i class="fas fa-moon"></i> Mode</button>
        <span>Welcome, <b><?php echo htmlspecialchars($_SESSION['user']); ?></b></span>
        <a href="logout.php" class="logout-btn">LOGOUT</a>
    </div>
</nav>

<div class="filter-nav">
    <a href="index.php" class="filter-link">All Items</a>
    <a href="index.php?cat=Veg" class="filter-link"><i class="fas fa-leaf"></i> Veg Only</a>
    <a href="index.php?cat=Non-Veg" class="filter-link"><i class="fas fa-drumstick-bite"></i> Non-Veg</a>
</div>

<div class="menu-grid">
    <?php
    $selected_cat = isset($_GET['cat']) ? $_GET['cat'] : '';
    if($selected_cat != '') {
        $menu_items = mysqli_query($conn, "SELECT * FROM menu WHERE category = '$selected_cat'");
    } else {
        $menu_items = mysqli_query($conn, "SELECT * FROM menu");
    }

    while($item = mysqli_fetch_assoc($menu_items)) {
        $id = $item['id'];
        $rating_res = mysqli_query($conn, "SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE dish_id = $id");
        $r_data = mysqli_fetch_assoc($rating_res);
        $avg = $r_data['avg'] ? round($r_data['avg'], 1) : "0.0";
        $badge_class = ($item['category'] == 'Non-Veg') ? 'bg-non' : 'bg-veg';
    ?>
    <div class="dish-card" style="position: relative;">
        <span class="type-badge <?php echo $badge_class; ?>">
            <?php echo $item['category']; ?>
        </span>

        <img src="<?php echo $item['image_url']; ?>" style="width:100%; height:200px; object-fit:cover;">
        <div style="padding: 20px;">
            <div style="display:flex; justify-content: space-between; align-items:center;">
                <h3 style="margin:0;"><?php echo htmlspecialchars($item['dish_name']); ?></h3>
                <span style="font-size: 1.3rem; font-weight: bold; color: var(--primary);">₹<?php echo $item['price']; ?></span>
            </div>
            
            <div style="margin: 12px 0;">
                <span class="rating-badge"><?php echo $avg; ?> <i class="fas fa-star" style="font-size: 11px;"></i></span>
                <span style="color:#878787; font-size: 13px; margin-left: 8px;">(<?php echo $r_data['total']; ?> Ratings)</span>
            </div>

            <form method="POST" style="background:rgba(0,0,0,0.04); padding:15px; border-radius:10px;">
                <input type="hidden" name="dish_id" value="<?php echo $id; ?>">
                <label style="font-size: 11px; font-weight: bold; opacity: 0.7;">RATE THIS DISH</label>
                <select name="rating" style="width:100%; padding:10px; margin:8px 0; border:1px solid #ddd; border-radius:4px; color: #ffc107; font-weight: bold;">
                    <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                    <option value="4">⭐⭐⭐⭐ (Good)</option>
                    <option value="3">⭐⭐⭐ (Average)</option>
                    <option value="2">⭐⭐ (Poor)</option>
                    <option value="1">⭐ (Bad)</option>
                </select>
                <textarea name="comment" style="width:100%; padding:10px; height:60px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box;" placeholder="Write your experience..."></textarea>
                <button type="submit" name="submit_review" class="btn-post">POST REVIEW</button>
            </form>

            <div class="review-box">
                <h4 style="font-size: 14px; margin: 0 0 10px 0;">Student Feedback</h4>
                <?php
                $revs = mysqli_query($conn, "SELECT * FROM reviews WHERE dish_id = $id AND comment != '' AND comment IS NOT NULL ORDER BY id DESC LIMIT 2");
                if(mysqli_num_rows($revs) > 0) {
                    while($r = mysqli_fetch_assoc($revs)) {
                        $rev_user = htmlspecialchars($r['user_id']);
                        $rev_date = isset($r['submitted_at']) ? date('d M, Y', strtotime($r['submitted_at'])) : "Recent";
                ?>
                <div class="review-item">
                    <div class="review-header">
                        <div>
                            <span class="rating-badge" style="font-size:9px; padding:1px 5px;"><?php echo $r['rating']; ?>★</span>
                            <span class="review-user"><?php echo $rev_user; ?></span>
                        </div>
                        <span class="review-date"><?php echo $rev_date; ?></span>
                    </div>
                    <p style="margin: 5px 0 0 0; color: #555;"><?php echo htmlspecialchars($r['comment']); ?></p>
                </div>
                <?php } } else { echo "<p style='font-size:12px; color:#999;'>No written reviews yet.</p>"; } ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
    function toggleMode() { document.getElementById('body').classList.toggle('dark-mode'); }
</script>
<?php include 'footer.php'; ?>
</body>
</html>