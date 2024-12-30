<?php
require_once "config.php";

header('Content-Type: application/json');

class NewspaperEdition {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getEditionByDate($date) {
        $sql = "SELECT np.*, ne.upload_date as edition_date 
                FROM newspaper_pages np
                JOIN newspaper_editions ne ON np.edition_id = ne.id
                WHERE ne.upload_date = ?
                ORDER BY np.page_number";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLatestEditionBeforeDate($date) {
        $sql = "SELECT np.*, ne.upload_date as edition_date 
                FROM newspaper_pages np
                JOIN newspaper_editions ne ON np.edition_id = ne.id
                WHERE ne.upload_date = (
                    SELECT MAX(upload_date) 
                    FROM newspaper_editions 
                    WHERE upload_date <= ?
                )
                ORDER BY np.page_number";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Remove any whitespace or comments before this point
try {
    if (!isset($_POST['date'])) {
        throw new Exception('Date parameter is required');
    }

    $date = $_POST['date'];
    $newspaper = new NewspaperEdition();
    $pages = $newspaper->getEditionByDate($date);

    if (empty($pages)) {
        $pages = $newspaper->getLatestEditionBeforeDate($date);
        $response = [
            'status' => 'warning',
            'message' => 'منتخب کردہ تاریخ کے لئے اخبار دستیاب نہیں ہے۔ تازہ ترین دستیاب اخبار دکھایا جا رہا ہے۔',
            'pages' => $pages
        ];
    } else {
        $response = [
            'status' => 'success',
            'pages' => $pages
        ];
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}