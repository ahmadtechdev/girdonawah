<?php
session_start();
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

$delete_success = "";
$delete_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_date'])) {
    $delete_date = $_POST['delete_date'];
    
    $image_sql = "SELECT image_path FROM newspaper_pages WHERE upload_date = ?";
    $image_stmt = mysqli_prepare($conn, $image_sql);
    mysqli_stmt_bind_param($image_stmt, "s", $delete_date);
    mysqli_stmt_execute($image_stmt);
    $image_result = mysqli_stmt_get_result($image_stmt);
    
    while ($row = mysqli_fetch_assoc($image_result)) {
        if (file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }

    $sql = "DELETE FROM newspaper_pages WHERE upload_date = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $delete_date);
    
    if (mysqli_stmt_execute($stmt)) {
        $delete_success = "Newspaper edition for " . $delete_date . " has been deleted.";
    } else {
        $delete_error = "Error deleting newspaper edition. Please try again.";
    }
    
    mysqli_stmt_close($stmt);
}

$dates_sql = "SELECT DISTINCT upload_date FROM newspaper_pages ORDER BY upload_date DESC";
$dates_result = mysqli_query($conn, $dates_sql);
$dates = mysqli_fetch_all($dates_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Newspaper Editions - Girdonawah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003D23;
            --secondary-color: #008000;
            --danger-color: #dc3545;
        }

        body {
            background: linear-gradient(135deg, #f0f4f0, #e6f0e6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }

        .delete-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,100,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            transition: all 0.3s ease;
        }

        .delete-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,100,0,0.15);
        }

        .delete-header {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            position: relative;
            padding-bottom: 15px;
        }

        .delete-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .form-select {
            border-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0,100,0,0.25);
        }

        .btn-delete {
            background-color: var(--danger-color);
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete:hover {
            background-color: darken(var(--danger-color), 10%);
            transform: scale(1.05);
        }

        .btn-primary, .btn-secondary {
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #565e64;
            transform: scale(1.05);
        }

        .btn i {
            margin-right: 8px;
        }

        @media (max-width: 576px) {
            .delete-container {
                margin: 20px;
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <h2 class="delete-header">حذف کریں</h2>

        <?php 
        if(!empty($delete_success)){
            echo '<div class="alert alert-success text-center">' . $delete_success . '</div>';
        }
        if(!empty($delete_error)){
            echo '<div class="alert alert-danger text-center">' . $delete_error . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="delete_date" class="form-label">تاریخ منتخب کریں</label>
                <select name="delete_date" class="form-select" required>
                    <?php foreach ($dates as $date): ?>
                        <option value="<?php echo $date['upload_date']; ?>">
                            <?php echo date('F j, Y', strtotime($date['upload_date'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-delete w-100" onclick="return confirm('کیا آپ واقعی اس ایڈیشن کو حذف کرنا چاہتے ہیں؟ یہ کارروائی واپس نہیں لی جا سکے گی۔');">
                    <i class="bi bi-trash"></i> ایڈیشن حذف کریں
                </button>
            </div>
        </form>
        
        <div class="row g-2">
            <div class="col-6">
                <a href="admin_newspaper.php" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-left"></i> اپ لوڈ پر واپس
                </a>
            </div>
            <div class="col-6">
                <a href="logout.php" class="btn btn-secondary w-100">
                    <i class="bi bi-box-arrow-right"></i> لاگ آؤٹ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>