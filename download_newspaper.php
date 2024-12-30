<?php
require_once "config.php";
require __DIR__ . "/fpdf/fpdf.php";

// Check if the date is provided
if (!isset($_POST['date']) || empty($_POST['date'])) {
    die("Invalid request. Date is required.");
}

$date = $_POST['date'];

// Fetch newspaper images for the given date
$conn = Database::getInstance()->getConnection();
$sql = "SELECT np.image_path, np.page_number 
        FROM newspaper_pages np
        JOIN newspaper_editions ne ON np.edition_id = ne.id
        WHERE ne.upload_date = ?
        ORDER BY np.page_number";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$pages = $result->fetch_all(MYSQLI_ASSOC);
if (empty($pages)) {
    die("No newspaper pages found for the selected date.");
}

// Generate PDF using FPDF
$pdf = new FPDF();
$pdf->SetAutoPageBreak(true);

foreach ($pages as $page) {
    $imagePath = $page['image_path'];
    if (file_exists($imagePath)) {
        $pdf->AddPage();
        $pdf->Image($imagePath, 10, 10, 190); // Adjust image size and position as needed
    }
}

// Set filename
$pdfFileName = "newspaper_" . $date . ".pdf";

// Clear any output that might have been sent already
if (ob_get_length()) ob_clean();

// Set headers to force download
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');

// Output the PDF
$pdf->Output('F', $pdfFileName); // Save to file first
readfile($pdfFileName); // Send file to browser
unlink($pdfFileName); // Delete the temporary file
exit();
?>
