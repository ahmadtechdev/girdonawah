
<?php
session_start();
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

$upload_err = "";
$upload_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Keep existing upload logic unchanged
    $position = $_POST['position'];
    $target_dir = "uploads/ads/";
    $imageFileType = strtolower(pathinfo($_FILES["ad_image"]["name"], PATHINFO_EXTENSION));
    $image_name = time() . "." . $imageFileType;
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["ad_image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO advertisements (position, image_path) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $position, $target_file);
        if (mysqli_stmt_execute($stmt)) {
            $upload_success = "Advertisement uploaded successfully!";
        } else {
            $upload_err = "Error uploading the advertisement.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $upload_err = "Error uploading image.";
    }
}

$ads_sql = "SELECT * FROM advertisements ORDER BY id DESC";
$ads_result = mysqli_query($conn, $ads_sql);
$ads = mysqli_fetch_all($ads_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Advertisement - Girdonawah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .ad-upload-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 100, 0, 0.1);
            padding: 40px;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #003D23;
            border-color: #004d00;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #004d00;
            transform: translateY(-3px);
            box-shadow: 0 4px 17px rgba(0, 100, 0, 0.35);
        }
        .ad-card {
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .ad-card:hover {
            transform: scale(1.05);
        }
        .ad-card img {
            height: 250px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .file-upload-wrapper {
            position: relative;
            border: 2px dashed #003D23;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .file-upload-wrapper:hover {
            background-color: rgba(0, 100, 0, 0.05);
        }
        .form-label {
            color: #003D23;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #003D23;
            box-shadow: 0 0 0 0.2rem rgba(0, 100, 0, 0.25);
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 ad-upload-container">
                <h2 class="text-center mb-4" style="color: #003D23;">
                    <i class="bi bi-image me-2"></i>Upload Advertisement
                </h2>
                
                <!-- Success and Error Messages -->
                <?php if (!empty($upload_err)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $upload_err; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($upload_success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $upload_success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Upload Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Ad Position</label>
                            <select name="position" class="form-select" required>
                                <option value="1">Ad 1 (Top Right)</option>
                                <option value="2">Ad 2 (Bottom Right)</option>
                                <option value="3">Ad 3 (Top Left)</option>
                                <option value="4">Ad 4 (Bottom Left)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ad_image" class="form-label">Ad Image</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="ad_image" class="form-control" required>
                                <small class="text-muted">
                                    <i class="bi bi-cloud-upload me-2"></i>Drag and drop or click to select
                                </small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-upload me-2"></i>Upload Ad
                    </button>
                </form>

                <!-- Display All Ads -->
                <h2 class="text-center mt-5 mb-4" style="color: #003D23;">
                    <i class="bi bi-images me-2"></i>Current Advertisements
                </h2>
                <div class="row">
                    <?php if (empty($ads)): ?>
                        <div class="col-12 text-center">
                            <p class="text-muted">No advertisements uploaded yet.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($ads as $ad): ?>
                        <div class="col-md-4 ad-card">
                            <div class="card">
                                <img src="<?php echo $ad['image_path']; ?>" alt="Ad Image" class="card-img-top">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Position <?php echo $ad['position']; ?></h5>
                                    <form action="delete_ad.php" method="post" onsubmit="return confirm('Are you sure you want to delete this ad?');">
                                        <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                        <button type="submit" name="delete_ad" class="btn btn-danger w-100 mt-2">
                                            <i class="bi bi-trash me-2"></i>Delete Ad
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Buttons -->
                <div class="text-center mt-4">
                    <a href="admin_dashboard.php" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>