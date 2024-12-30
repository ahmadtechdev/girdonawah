<?php
session_start();
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

// Check if the admin is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ur">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ایڈمن ڈیش بورڈ - گِردوًواہ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003D23;
            /* Base color */
            --secondary-color: #005A36;
            /* Slightly lighter shade of the base color */
            --accent-color: #007A4A;
            /* Even lighter and more vibrant shade */
        }
        

        * {
            font-family: 'Noto Nastaliq Urdu', serif;
        }

        body {
            background: linear-gradient(135deg, #f0f4f0, #e6f0e6);
            direction: rtl;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
        }

        .dashboard-container {
            flex-grow: 1;
            padding-top: 50px;
        }

        .dashboard-header {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 40px;
            font-weight: bold;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            transform-style: preserve-3d;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 100, 0, 0.1);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    transparent,
                    var(--primary-color),
                    transparent);
            transform: rotate(-45deg);
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .dashboard-card:hover::before {
            opacity: 0.1;
        }

        .dashboard-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 35px rgba(0, 100, 0, 0.2);
        }

        .dashboard-card-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover .dashboard-card-icon {
            transform: rotate(360deg);
        }

        .dashboard-card-title {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .dashboard-card:hover .dashboard-card-title {
            color: var(--secondary-color);
        }

        .dashboard-footer {
            background-color: rgba(0, 100, 0, 0.05);
            padding: 15px 0;
            text-align: center;
        }

        .dashboard-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .dashboard-footer a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                font-size: 2rem;
            }

            .dashboard-card-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container dashboard-container">
        <h2 class="dashboard-header">ایڈمن ڈیش بورڈ</h2>

        <div class="row g-4">
            <div class="col-md-4 col-sm-6">
                <a href="admin_articles.php" class="text-decoration-none">
                    <div class="card dashboard-card text-center p-4">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h5 class="dashboard-card-title">مضامین کا انتظام کریں</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="admin_ads.php" class="text-decoration-none">
                    <div class="card dashboard-card text-center p-4">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-ad"></i>
                        </div>
                        <h5 class="dashboard-card-title">اشتہارات کا انتظام کریں</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="admin_newspaper.php" class="text-decoration-none">
                    <div class="card dashboard-card text-center p-4">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h5 class="dashboard-card-title">اخبار کا انتظام کریں</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="#" class="text-decoration-none">
                    <div class="card dashboard-card text-center p-4">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h5 class="dashboard-card-title">فائلیں اپلوڈ کریں</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="admin_delete.php" class="text-decoration-none">
                    <div class="card dashboard-card text-center p-4">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-trash"></i>
                        </div>
                        <h5 class="dashboard-card-title">اخبار حذف کریں</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="logout.php" class="text-decoration-none">
                    <div class="card dashboard-card text-center p-4">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h5 class="dashboard-card-title">لاگ آؤٹ</h5>
                    </div>
                </a>
            </div>
        </div>

        <div class="dashboard-footer mt-5">
            <a href="index.php">&larr; مرکزی ڈیش بورڈ پر واپس جائیں</a>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>