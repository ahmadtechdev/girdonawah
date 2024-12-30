<?php
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

// Get article ID from URL
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch article details from the database
$sql = "SELECT `id`, `title`, `description`, `content`, `image_path`, `category`, `publish_date` FROM `articles` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $article_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$article = mysqli_fetch_assoc($result);

// Fetch recent articles for sidebar
$recent_sql = "SELECT id, title, image_path, description FROM articles WHERE id != ? ORDER BY publish_date DESC LIMIT 5";
$recent_stmt = mysqli_prepare($conn, $recent_sql);
mysqli_stmt_bind_param($recent_stmt, "i", $article_id);
mysqli_stmt_execute($recent_stmt);
$recent_result = mysqli_stmt_get_result($recent_stmt);
$recent_articles = mysqli_fetch_all($recent_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - گردونواح</title>
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
            line-height: 1.7;
        }

        .page-title {
            color: #004d2b;
            text-align: center;
            padding: 15px 0;
            margin: 10px 0 20px 0;
            font-size: 2rem;
        }

        .article-main {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .article-header {
            margin-bottom: 25px;
        }

        .article-title {
            color: #004d2b;
            font-size: 2rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .article-meta {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .article-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .article-description {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            line-height: 1.8;
        }

        .article-content {
            font-size: 1.2rem;
            line-height: 2;
            margin-bottom: 30px;
        }

        /* Recent Articles Styles */
        .recent-articles {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .recent-title {
            color: #004d2b;
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #004d2b;
        }

        .recent-article-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .recent-article-card:hover {
            transform: translateY(-5px);
        }

        .recent-article-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .recent-article-card .card-body {
            padding: 15px;
        }

        .recent-article-card .card-title {
            color: #004d2b;
            font-size: 1.1rem;
            margin-bottom: 10px;
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
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .article-title {
                font-size: 1.6rem;
            }
            
            .article-description {
                font-size: 1.1rem;
            }
            
            .article-content {
                font-size: 1.1rem;
            }
            
            .recent-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <h2 class="page-title">مضمون</h2>

    <div class="container">
        <div class="row">
            <!-- Main Article Content -->
            <div class="col-lg-8">
                <article class="article-main">
                    <header class="article-header">
                        <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                        <div class="article-meta">
                            <span class="me-3"><i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($article['category']); ?></span>
                            <span><i class="fas fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($article['publish_date'])); ?></span>
                        </div>
                    </header>

                    <?php if (!empty($article['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-image">
                    <?php endif; ?>

                    <div class="article-description">
                        <?php echo nl2br(htmlspecialchars($article['description'])); ?>
                    </div>

                    <div class="article-content">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>
                </article>
            </div>

            <!-- Sidebar with Recent Articles -->
            <div class="col-lg-4">
                <div class="recent-articles">
                    <h3 class="recent-title">حال ہی کے مضامین</h3>
                    <?php foreach ($recent_articles as $recent): ?>
                        <div class="recent-article-card">
                            <?php if (!empty($recent['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($recent['image_path']); ?>" alt="<?php echo htmlspecialchars($recent['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($recent['title']); ?></h5>
                                <p class="card-text"><?php echo substr(htmlspecialchars($recent['description']), 0, 100) . '...'; ?></p>
                                <a href="view_article.php?id=<?php echo $recent['id']; ?>" class="btn-read-more">
                                    مزید پڑھیں
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>