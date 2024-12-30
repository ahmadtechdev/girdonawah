<!-- <?php
require_once "config.php";

    // Get database connection using the singleton pattern
    $conn = Database::getInstance()->getConnection();


// Fetch all distinct newspaper dates
$sql = "SELECT DISTINCT upload_date FROM newspaper_pages ORDER BY upload_date DESC";
$result = mysqli_query($conn, $sql);
$dates = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرانے اخبارات - گِردوںواہ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Noto Nastaliq Urdu', serif;
            direction: rtl;
        }
        .calendar-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">پرانے اخبارات</h2>

        <div class="calendar-container">
            <h5>اخبار کی تاریخ منتخب کریں</h5>
            <input type="text" id="datepicker" class="form-control" placeholder="تاریخ منتخب کریں">
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            const availableDates = <?php echo json_encode(array_column($dates, 'upload_date')); ?>;
            $("#datepicker").datepicker({
                dateFormat: "yy-mm-dd",
                beforeShowDay: function(date) {
                    const string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    return [availableDates.includes(string)];
                },
                onSelect: function (dateText) {
                    window.location.href = "view_archive.php?date=" + dateText;
                }
            });
        </script>
    </div>
</body>
</html> -->