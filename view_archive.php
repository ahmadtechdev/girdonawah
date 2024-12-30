<?php
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sql = "SELECT * FROM newspaper_pages WHERE upload_date = ? ORDER BY page_number";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pages = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get the upload time
$time_sql = "SELECT upload_time FROM newspaper_pages WHERE upload_date = ? LIMIT 1";
$time_stmt = mysqli_prepare($conn, $time_sql);
mysqli_stmt_bind_param($time_stmt, "s", $date);
mysqli_stmt_execute($time_stmt);
$time_result = mysqli_stmt_get_result($time_stmt);
$upload_time = mysqli_fetch_assoc($time_result)['upload_time'];
?>

<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Girdonawah - Archived Edition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Noto Nastaliq Urdu', serif;
        }
        .navbar {
            background-color: #006400;
        }
        .navbar-brand {
            font-size: 2rem;
            color: #fff;
        }
        .slick-slide img {
            width: 100%;
            height: auto;
        }
        .page-number {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .date-time {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">گِردوںواہ</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Latest Edition</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="archive.php">Archive</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="date-time">
            Archived Edition: <?php echo date('F j, Y', strtotime($date)); ?> | 
            Uploaded at: <?php echo date('g:i A', strtotime($upload_time)); ?>
        </div>

        <?php if (!empty($pages)): ?>
            <div class="newspaper-carousel">
                <?php foreach ($pages as $page): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($page['image_path']); ?>" alt="Newspaper page <?php echo $page['page_number']; ?>">
                        <div class="page-number">Page <?php echo $page['page_number']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No newspaper pages available for this date.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.newspaper-carousel').slick({
                dots: true,
                infinite: false,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true
            });
        });
    </script>
</body>
</html>