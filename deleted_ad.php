
<?php
// delete_ad.php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

// Get database connection
$conn = Database::getInstance()->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ad_id']) && isset($_POST['image_path'])) {
    $ad_id = $_POST['ad_id'];
    $image_path = $_POST['image_path'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete from database
        $delete_sql = "DELETE FROM advertisements WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $ad_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error deleting from database");
        }
        
        // Delete file from directory
        if (file_exists($image_path)) {
            if (!unlink($image_path)) {
                throw new Exception("Error deleting image file");
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        mysqli_stmt_close($stmt);
        
        // Redirect with success message
        header("location: admin_ads.php?success=Ad deleted successfully");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        // Redirect with error message
        header("location: admin_ads.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Invalid request
    header("location: admin_ads.php?error=Invalid request");
    exit;
}
?>
