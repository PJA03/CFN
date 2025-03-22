<?php
require_once '../conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Invalid QR code ID']);
        exit;
    }

    // Fetch the QR code to delete the associated image file
    $query = "SELECT qr_image FROM tb_payment_qr_codes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $qr = $result->fetch_assoc();

    if ($qr) {
        $upload_dir = '../uploads/qr/';
        $image_path = $upload_dir . $qr['qr_image'];
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }

        // Delete the QR code from the database
        $query = "DELETE FROM tb_payment_qr_codes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete QR code from database']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'QR code not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>