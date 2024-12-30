
<?php
require_once "config.php";
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
        }

        @media (max-width: 480px) {
            .header-image {
                max-height: 100px;
            }
        }
    </style>
</head>

<body>
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
                    <li class="nav-item">
                        <a class="nav-link" href="okara_news.php">اوکاڑہ کی خبریں</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles.php">مضامین</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>