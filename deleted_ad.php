<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_id = $_POST['ad_id'];

    // Delete ad from database
    $delete_sql = "DELETE FROM advertisements WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $ad_id);
    if (mysqli_stmt_execute($stmt)) {
        header("location: admin_ads.php?success=Ad deleted successfully");
        exit;
    } else {
        echo "Error deleting ad.";
    }
    mysqli_stmt_close($stmt);
}
?>