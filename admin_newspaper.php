
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

// Set timezone to Pakistan Standard Time
date_default_timezone_set('Asia/Karachi');

// Set default date and time to current date and time in Pakistan
$default_date = date("Y-m-d");
$default_time = date("H:i");
$target_dir = "uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $upload_date = !empty($_POST["upload_date"]) ? $_POST["upload_date"] : $default_date;
    $upload_time = !empty($_POST["upload_time"]) ? $_POST["upload_time"] : $default_time;

    // Check if an edition already exists for this date, and delete it if it does
    $sql = "SELECT id FROM newspaper_editions WHERE upload_date = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $upload_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $edition_id = $row['id'];

        // Delete existing pages from the uploads folder
        $sql = "SELECT image_path FROM newspaper_pages WHERE edition_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $edition_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($page = mysqli_fetch_assoc($result)) {
            unlink($page['image_path']);  // Delete the images from the folder
        }

        // Delete the edition and its pages from the database
        $sql = "DELETE FROM newspaper_editions WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $edition_id);
        mysqli_stmt_execute($stmt);
    }

    // Now insert the new edition
    $sql = "INSERT INTO newspaper_editions (upload_date, upload_time) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $upload_date, $upload_time);
    mysqli_stmt_execute($stmt);
    $new_edition_id = mysqli_insert_id($conn);

    // Insert pages for this new edition
    foreach ($_FILES["newspaper_pages"]["name"] as $key => $name) {
        $imageFileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $new_filename = $upload_date . "_p" . ($key + 1) . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["newspaper_pages"]["tmp_name"][$key], $target_file)) {
            $sql = "INSERT INTO newspaper_pages (page_number, image_path, upload_date, upload_time, edition_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $page_number = $key + 1;
            mysqli_stmt_bind_param($stmt, "isssi", $page_number, $target_file, $upload_date, $upload_time, $new_edition_id);
            mysqli_stmt_execute($stmt);
        }
    }

    mysqli_commit($conn);
    $upload_success = "Newspaper edition uploaded successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Upload - Girdonawah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .upload-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,100,0,0.1);
            padding: 40px;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #003D23;
            border-color: #003D23;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #004d00;
            border-color: #004d00;
        }
        #image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        #image-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            object-fit: cover;
        }
        .form-label {
            color: #003D23;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="upload-container">
            <h2 class="text-center mb-4" style="color: #003D23;">Upload Newspaper Pages</h2>
            
            <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="upload_date" class="form-label">Upload Date</label>
                    <input type="date" name="upload_date" class="form-control" value="<?php echo $default_date; ?>">
                </div>
                <div class="mb-3">
                    <label for="upload_time" class="form-label">Upload Time</label>
                    <input type="time" name="upload_time" class="form-control" value="<?php echo $default_time; ?>">
                </div>
                <div class="mb-3">
                    <label for="newspaper_pages" class="form-label">Select newspaper pages (multiple files allowed)</label>
                    <input type="file" name="newspaper_pages[]" id="newspaper_pages" class="form-control" multiple required accept="image/*">
                    <div id="image-preview" class="mt-3"></div>
                </div>
                <div class="mb-3 text-center">
                    <input type="submit" class="btn btn-primary btn-lg" value="Upload Newspaper">
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="admin_dashboard.php" class="btn btn-danger">back to Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // Image preview
        document.getElementById('newspaper_pages').addEventListener('change', function(event) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = ''; // Clear previous previews

            Array.from(event.target.files).forEach(file => {
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail');
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        // Success message
        <?php if(!empty($upload_success)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Upload Successful!',
            text: '<?php echo $upload_success; ?>',
            background: '#f0fff0',
            confirmButtonColor: '#003D23'
        });
        <?php endif; ?>
    </script>
</body>
</html>