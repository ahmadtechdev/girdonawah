
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


// Handle deletion request
if (isset($_POST['delete_article'])) {
    $article_id = $_POST['article_id'];
    $delete_sql = "DELETE FROM articles WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $article_id);
    if (mysqli_stmt_execute($stmt)) {
        $upload_success = "Article deleted successfully!";
    } else {
        $upload_err = "Error deleting the article.";
    }
    mysqli_stmt_close($stmt);
}

// Handle upload request
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_article'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = $_POST['content'];
    $category = $_POST['category'];

    $target_dir = "uploads/articles/";
    $imageFileType = strtolower(pathinfo($_FILES["article_image"]["name"], PATHINFO_EXTENSION));
    $image_name = time() . "." . $imageFileType;
    $target_file = $target_dir . $image_name;

    if ($_FILES["article_image"]["size"] > 2000000) {
        $upload_err = "File is too large. Max 2MB allowed.";
    } elseif(!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        $upload_err = "Only JPG, JPEG, and PNG files are allowed.";
    } else {
        if (move_uploaded_file($_FILES["article_image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO articles (title, description, content, image_path, category, publish_date) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $content, $target_file, $category);
            if (mysqli_stmt_execute($stmt)) {
                $upload_success = "Article uploaded successfully!";
            } else {
                $upload_err = "Error uploading the article.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $upload_err = "Error uploading image.";
        }
    }

   
}

// Fetch all articles
$articles_sql = "SELECT * FROM articles ORDER BY publish_date DESC";
$articles_result = mysqli_query($conn, $articles_sql);
$articles = mysqli_fetch_all($articles_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Article - Girdonawah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .article-container {
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
        .form-label {
            color: #003D23;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #003D23;
            box-shadow: 0 0 0 0.2rem rgba(0, 100, 0, 0.25);
        }
        .article-card {
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .article-card:hover {
            transform: scale(1.03);
        }
        .article-card img {
            height: 250px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        .nav-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 article-container">
                <h2 class="text-center mb-4" style="color: #003D23;">
                    <i class="bi bi-newspaper me-2"></i>Upload Article
                </h2>
                
                <!-- Error and Success Messages -->
                <?php 
                if (!empty($upload_err)) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . 
                         $upload_err . 
                         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }
                if (!empty($upload_success)) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                         $upload_success . 
                         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }
                ?>

                <!-- Article Upload Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Article Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="okara">اوکاڑہ کی خبریں</option>
                                <option value="general">مضامین</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Article Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Full Article Content</label>
                        <textarea name="content" class="form-control" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="article_image" class="form-label">Article Image</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="article_image" class="form-control" required>
                            <small class="text-muted">
                                <i class="bi bi-cloud-upload me-2"></i>Drag and drop or click to select
                            </small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-upload me-2"></i>Upload Article
                    </button>
                </form>

                <!-- Article List -->
                <h2 class="text-center mt-5 mb-4" style="color: #003D23;">
                    <i class="bi bi-journals me-2"></i>All Articles
                </h2>
                <div class="row">
                    <?php if (empty($articles)): ?>
                        <div class="col-12 text-center">
                            <p class="text-muted">No articles uploaded yet.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($articles as $article): ?>
                    <div class="col-md-4 article-card">
                        <div class="card">
                            <img src="<?php echo $article['image_path']; ?>" alt="Article Image" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $article['title']; ?></h5>
                                <p class="card-text"><?php echo substr($article['description'], 0, 100) . '...'; ?></p>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                    <button type="submit" name="delete_article" class="btn btn-danger w-100 mt-2" 
                                            onclick="return confirm('Are you sure you want to delete this article?');">
                                        <i class="bi bi-trash me-2"></i>Delete Article
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Buttons -->
                <div class="nav-buttons">
                    <a href="admin_dashboard.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <!-- <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a> -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>