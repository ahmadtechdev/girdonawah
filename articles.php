<?php
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

// Fetch general articles from the database
$sql = "SELECT * FROM articles WHERE category = 'general' ORDER BY publish_date DESC";
$result = mysqli_query($conn, $sql);
$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مضامین - گردونواح</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @font-face {
            font-family: 'Jameel Noori Nastaliq';
            src: url('Jameel_Noori_Nastaleeq.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            background-color: #fff;
            font-family: 'Jameel Noori Nastaliq', serif;
            direction: rtl;
            color: #333;
        }

        .page-title {
            color: #004d2b;
            text-align: center;
            padding: 15px 0;
            margin: 10px 0 20px 0;
            font-size: 2rem;
        }

        /* Article Section Styles */
        .article-container {
            padding: 20px 0;
        }

        .article-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .article-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .article-card .card-body {
            padding: 15px;
        }

        .article-card .card-title {
            color: #004d2b;
            font-size: 1.4rem;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .article-card .card-text {
            color: #555;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .btn-read-more {
            background: linear-gradient(135deg, #004d2b 0%, #003D23 100%);
            color: #fff !important;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-read-more:hover {
            background: linear-gradient(135deg, #003D23 0%, #004d2b 100%);
            transform: translateX(-5px);
            color: #fff !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .article-card .card-img-top {
                height: 180px;
            }
            
            .article-card .card-title {
                font-size: 1.2rem;
            }

            .page-title {
                font-size: 1.6rem;
                padding: 10px 0;
                margin: 5px 0 15px 0;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Page Title -->
    <h2 class="page-title">مضامین</h2>

    <!-- Articles Section -->
    <div class="container article-container">
        <div class="row g-4">
            <?php foreach ($articles as $article): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="article-card">
                        <img src="<?php echo htmlspecialchars($article['image_path']); ?>" class="card-img-top" alt="Article Image">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($article['description']); ?></p>
                            <a href="view_article.php?id=<?php echo $article['id']; ?>" class="btn btn-read-more mt-auto">
                                مزید پڑھیں
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>