<?php
require_once "config.php";

$conn = Database::getInstance()->getConnection();

$today = date('Y-m-d');

$sql = "SELECT np.*, ne.upload_date 
        FROM newspaper_pages np
        JOIN newspaper_editions ne ON np.edition_id = ne.id
        WHERE ne.upload_date = (
            SELECT COALESCE(
                (SELECT upload_date FROM newspaper_editions WHERE upload_date = ? LIMIT 1),
                (SELECT MAX(upload_date) FROM newspaper_editions)
            )
        )
        ORDER BY np.page_number";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pages = mysqli_fetch_all($result, MYSQLI_ASSOC);

$upload_date = !empty($pages) ? $pages[0]['upload_date'] : '';

$ads_sql = "SELECT * FROM advertisements ORDER BY position LIMIT 4";
$ads_result = mysqli_query($conn, $ads_sql);
$ads = $ads_result ? mysqli_fetch_all($ads_result, MYSQLI_ASSOC) : [];
$current_url = $_SERVER['REQUEST_URI'];
if ($upload_date !== $today && strpos($current_url, 'date') === false) {
    $new_url = strtok($current_url, '?') . '?date=' . $upload_date;
    header("Location: $new_url");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گردونواح - ڈیجیٹل اخبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        @font-face {
            font-family: 'Jameel Noori Nastaliq';
            src: url('Jameel_Noori_Nastaleeq.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Jameel Noori Nastaliq', serif;
            direction: rtl;
        }

        /* Header Styles */
        .header-container {
            width: 100%;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .header-image-wrapper {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            overflow: hidden;
        }

        .header-image {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
            max-height: 200px;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #003D23;
            padding: 0.5rem 1rem;
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
            padding: 0.5rem 1.2rem !important;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar .container {
            max-width: 1200px;
        }

        .nav-item {
            margin: 0 0.5rem;
        }

        /* Main Content Styles */
        .news-page {
            text-align: center;
            margin-top: 20px;
        }

        .newspaper-page {
            position: relative;
            margin: 0 auto;
            max-width: 100%;
            text-align: center;
        }

        .newspaper-page img {
            width: 100%;
            height: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }

        .newspaper-image {
            max-width: 100%;
            height: auto;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }

        /* Advertisement Styles */
        .ad-container {
            position: sticky;
            top: 80px;
            width: 160px;
            margin: 0 auto;
        }

        .ad-side {
            padding: 0 10px;
        }

        .ad-side img {
            width: 100%;
            height: 300px;
            margin-bottom: 15px;
            border-radius: 5px;
            object-fit: cover;
        }

        /* Page Navigation Styles */
        .page-number {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 1.1rem;
        }

        .page-controls {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .page-controls button {
            padding: 5px 15px;
            background-color: #003D23;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .page-controls button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        /* Date Related Styles */
        .date-info {
            font-size: 1.2rem;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .date-picker-container {
            text-align: center;
            margin: 20px 0;
            display: none;
        }

        .flatpickr-input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }

        /* Container Adjustments */
        .container {
            max-width: 1400px;
            padding: 0 15px;
        }

        .col-md-8 {
            padding: 0 20px;
        }

        /* Responsive Styles */
        @media (min-width: 768px) {
            .navbar-expand-md .navbar-nav {
                display: flex !important;
                flex-basis: auto;
            }

            .navbar-expand-md .navbar-collapse {
                display: flex !important;
            }

            .navbar-expand-md .navbar-toggler {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .navbar-collapse {
                background-color: #003D23;
                padding: 10px;
                border-radius: 0 0 5px 5px;
            }

            .nav-item {
                margin: 5px 0;
            }

            .navbar-nav {
                text-align: right;
            }
        }


        @media (max-width: 768px) {
            .header-image {
                max-height: 150px;
            }

            .ad-container {
                position: static;
                width: 100%;
                max-width: 160px;
                margin: 10px auto;
            }

            .ad-side img {
                height: 200px;
            }

            .col-md-8 {
                padding: 0 10px;
            }

            .date-info {
                font-size: 1rem;
                padding: 5px;
            }
        }

        @media (max-width: 480px) {
            .header-image {
                max-height: 100px;
            }
        }

        .date-info-container {
            margin: 15px auto;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        .date-info-wrapper {
            background: linear-gradient(135deg, #003D23, #006400);
            border-radius: 15px;
            padding: 3px;
            box-shadow: 0 4px 15px rgba(0, 61, 35, 0.2);
        }

        .date-info {
            background: white;
            border-radius: 12px;
            padding: 15px 15px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 0;
            border: none;
        }

        .date-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #003D23;
            border-radius: 50%;
            padding: 8px;
        }

        .date-icon svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .date-text {
            font-size: 1.4rem;
            color: #003D23;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }

        .date-decorative-line {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 2px;
            background: linear-gradient(90deg, transparent, #003D23, transparent);
            width: 80%;
            z-index: 0;
            opacity: 0.1;
        }

        @media (max-width: 768px) {
            .date-info-container {
                margin: 15px auto;
                max-width: 90%;
            }

            .date-text {
                font-size: 1.2rem;
            }

            .date-icon {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 480px) {
            .date-text {
                font-size: 1.1rem;
            }

            .date-icon {
                width: 30px;
                height: 30px;
            }
        }

        /* add loader */
        .side-content-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
            margin-bottom: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
            overflow: hidden;
        }

        .content-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f8f8;
        }

        .loading-indicator {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #003D23;
            border-radius: 50%;
            animation: rotate 1s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .side-column {
            position: sticky;
            top: 80px;
            width: 160px;
            margin: 0 auto;
        }

        .side-content {
            padding: 0 10px;
        }

        .side-content .side-content-wrapper img {
            width: 100%;
            height: 300px;
            border-radius: 5px;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        @media (max-width: 768px) {
            .side-content-wrapper {
                height: 200px;
            }

            .side-content .side-content-wrapper img {
                height: 200px;
            }

            .side-column {
                position: static;
                width: 100%;
                max-width: 160px;
                margin: 10px auto;
            }
        }

        
    </style>

</head>

<body>

    <!-- Header -->
    <!-- <div class="header-top container"> -->
    <!-- <img src="header.jpg" alt="Logo" class="logo" width="100%"> -->
    <!-- <div class="slogan">آپ کا پسندیدہ اخبار</div> -->
    <!-- </div> -->

    <!-- Header -->
    <div class="header-container">
        <div class="header-image-wrapper">
            <img src="header.jpg" alt="Logo" class="header-image">
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">گردونواح</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            صفحات
                        </a>
                        <ul class="dropdown-menu" id="pagesDropdown">
                            <?php foreach ($pages as $page): ?>
                                <li><a class="dropdown-item page-selector" data-page="<?php echo $page['page_number'] - 1; ?>" href="#">صفحہ <?php echo $page['page_number']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="okara_news.php">اوکاڑہ کی خبریں</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles.php">مضامین</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="archiveLink" href="#">گزشتہ شمارے</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Date Picker -->
    <div class="date-picker-container container">
        <input type="text" id="datePicker" placeholder="تاریخ منتخب کریں" readonly>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Left Ads -->
            <div class="col-md-2 side-content">
                <div class="side-column">
                    <?php
                    $left_content = array_filter($ads, function ($item) {
                        return $item['position'] >= 3;
                    });
                    foreach ($left_content as $item):
                    ?>
                        <div class="side-content-wrapper">
                            <div class="content-loading">
                                <div class="loading-indicator"></div>
                            </div>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                alt="Side Content"
                                onload="this.parentElement.querySelector('.content-loading').style.display='none'"
                                onerror="this.parentElement.style.display='none'"
                                loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Newspaper Pages -->
            <div class="col-md-8">
                <div class="news-page">
                    <?php if (!empty($upload_date)): ?>
                        <?php
                        $date = isset($_GET['date']) ? $_GET['date'] : $upload_date;
                        $formattedDate = date(' D, d F Y   ', strtotime($date));
                        ?>


                        <div class="date-info-container">
                            <div class="date-info-wrapper">
                                <div class="date-info">
                                    <div class="date-icon">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 4h-1V3c0-.55-.45-1-1-1s-1 .45-1 1v1H8V3c0-.55-.45-1-1-1s-1 .45-1 1v1H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5z" />
                                        </svg>
                                    </div>
                                    <div class="date-text">اخبار کی تاریخ: <?php echo $formattedDate; ?></div>
                                    <div class="date-decorative-line"></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>



                    <div id="newspaperContent">
                        <?php if (!empty($pages)): ?>
                            <div class="newspaper-carousel">
                                <?php foreach ($pages as $page): ?>
                                    <div class="newspaper-page">
                                        <img src="<?php echo htmlspecialchars($page['image_path']); ?>"
                                            alt="Newspaper page <?php echo $page['page_number']; ?>" loading="lazy">
                                        <div class="page-number">صفحہ <?php echo $page['page_number']; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="page-controls">
                                <button id="prevPage" class="btn-prev">پچھلا صفحہ</button>
                                <button id="nextPage" class="btn-next">اگلا صفحہ</button>
                            </div>
                            <!-- Add Download Button -->
                            <div class="text-center my-4">
                                <form action="download_newspaper.php" method="post">
                                    <input type="hidden" name="date" value="<?php echo $date; ?>">
                                    <button type="submit" class="btn btn-success">اخبار ڈاؤن لوڈ کریں</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">کوئی اخبار دستیاب نہیں ہے۔</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Ads -->
            <div class="col-md-2 side-content">
                <div class="side-column">
                    <?php
                    $right_content = array_filter($ads, function ($item) {
                        return $item['position'] <= 2;
                    });
                    foreach ($right_content as $item):
                    ?>
                        <div class="side-content-wrapper">
                            <div class="content-loading">
                                <div class="loading-indicator"></div>
                            </div>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                alt="Side Content"
                                onload="this.parentElement.querySelector('.content-loading').style.display='none'"
                                onerror="this.parentElement.style.display='none'"
                                loading="lazy">
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="newspaper-viewer.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const viewer = new NewspaperViewer({
                container: document.getElementById('newspaperContent'),
                carouselContainer: document.querySelector('.newspaper-carousel'),
                prevButton: document.getElementById('prevPage'),
                nextButton: document.getElementById('nextPage')
            });

            const datePicker = flatpickr("#datePicker", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                defaultDate: "today",
                onChange: (selectedDates) => {
                    const selectedDate = selectedDates[0];
                    const formattedDate =
                        `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}`;

                    // Reload the page with the selected date in the URL
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('date', formattedDate);
                    window.location.href = newUrl; // Trigger a page reload with the updated URL
                }
            });

            // Archive link functionality
            document.getElementById('archiveLink').addEventListener('click', (e) => {
                e.preventDefault();
                const datePickerContainer = document.querySelector('.date-picker-container');
                datePickerContainer.style.display =
                    datePickerContainer.style.display === 'none' ? 'block' : 'none';
            });

            // Load newspaper content dynamically on page load
            const urlParams = new URLSearchParams(window.location.search);
            const selectedDate = urlParams.get('date') || new Date().toISOString().split('T')[0];
            viewer.loadNewspaper(selectedDate);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if images are blocked
            const sideImages = document.querySelectorAll('.side-content-wrapper img');
            sideImages.forEach(img => {
                // Create a backup image source if needed
                if (!img.complete || img.naturalHeight === 0) {
                    // You can implement a fallback here if needed
                    console.log('Image loading blocked or failed');
                }
            });
        });
    </script>





</body>

</html>