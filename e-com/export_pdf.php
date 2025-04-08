<?php
require_once '../conn.php';
require_once 'tcpdf/tcpdf.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get the JSON data from the request body
$input = json_decode(file_get_contents('php://input'), true);
$startDate = $input['startDate'] ?? '';
$endDate = $input['endDate'] ?? '';
$totalSales = $input['totalSales'] ?? 0;
$newUsers = $input['newUsers'] ?? 0;
$repeatPurchase = $input['repeatPurchase'] ?? 0;
$topProducts = $input['topProducts'] ?? [];
$orders = $input['orders'] ?? [];

// Validate required fields
if (!$startDate || !$endDate) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required date range']);
    exit;
}

// Create new PDF document with UTF-8 encoding
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Naturale Admin');
$pdf->SetTitle('Analytics Report');
$pdf->SetSubject('Analytics Report for Naturale');
$pdf->SetKeywords('Analytics, Sales, Report, Naturale');

// Set default header data
$statusFilter = isset($input['statusFilter']) ? $input['statusFilter'] : 'all';
$pdf->SetHeaderData('', 0, 'Naturale Analytics Report', "From $startDate to $endDate (Status: " . ucfirst($statusFilter) . ")");

// Set header and footer fonts
$pdf->setHeaderFont(['dejavusans', '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont(['dejavusans', '', PDF_FONT_SIZE_DATA]);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font to dejavusans to support Unicode characters like the peso sign
$pdf->SetFont('dejavusans', '', 12);

// Add a page
$pdf->AddPage();

// Title
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->Cell(0, 10, 'Analytics Report', 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(0, 10, "Date Range: $startDate to $endDate", 0, 1, 'C');
$pdf->Ln(10);

// Summary Section
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 10, 'Summary', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(60, 10, 'Total Sales:', 0, 0);
$pdf->Cell(0, 10, "₱" . number_format($totalSales, 2), 0, 1);
$pdf->Cell(60, 10, 'No. of New Users:', 0, 0);
$pdf->Cell(0, 10, $newUsers, 0, 1);
$pdf->Cell(60, 10, 'Repeat Purchase %:', 0, 0);
$pdf->Cell(0, 10, number_format($repeatPurchase, 1) . '%', 0, 1);
$pdf->Ln(10);

// Top Selling Products Table
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 10, 'Top Selling Products', 0, 1);

// Table header
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetFillColor(200, 220, 200); // Light green background for header
$pdf->Cell(40, 10, 'No. of Sales', 1, 0, 'C', 1);
$pdf->Cell(150, 10, 'Product Name', 1, 1, 'C', 1);

// Table data
$pdf->SetFont('dejavusans', '', 12);
foreach ($topProducts as $product) {
    $pdf->Cell(40, 10, $product['quantity'], 1, 0, 'C');
    $pdf->Cell(150, 10, $product['product_name'], 1, 1, 'L');
}
$pdf->Ln(10);

// Orders Table
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 10, 'Orders', 0, 1);

// Define column widths
$colWidths = [
    'orderID' => 40,
    'status' => 50, // Increased width to accommodate longer statuses
    'total_amount' => 40,
    'order_date' => 60 // Adjusted to fit total width (190)
];

// Table header
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetFillColor(200, 220, 200); // Light green background for header
$pdf->Cell($colWidths['orderID'], 10, 'Order ID', 1, 0, 'C', 1);
$pdf->Cell($colWidths['status'], 10, 'Status', 1, 0, 'C', 1);
$pdf->Cell($colWidths['total_amount'], 10, 'Total Amount', 1, 0, 'C', 1);
$pdf->Cell($colWidths['order_date'], 10, 'Order Date', 1, 1, 'C', 1);

// Table data
$pdf->SetFont('dejavusans', '', 10); // Reduced font size slightly for better fit
if (empty($orders)) {
    $pdf->Cell(190, 10, 'No orders available for this status', 1, 1, 'C');
} else {
    foreach ($orders as $order) {
        $pdf->Cell($colWidths['orderID'], 10, $order['orderID'], 1, 0, 'C');
        $pdf->MultiCell($colWidths['status'], 10, $order['status'], 1, 'C', false, 0); // Use MultiCell for wrapping
        $pdf->Cell($colWidths['total_amount'], 10, "₱" . number_format($order['total_amount'], 2), 1, 0, 'C');
        $pdf->Cell($colWidths['order_date'], 10, date('Y-m-d', strtotime($order['order_date'])), 1, 1, 'C');
    }
}

// Output the PDF
try {
    $filename = "Analytics_Report_{$startDate}_to_{$endDate}" . ($statusFilter !== 'all' ? "_{$statusFilter}" : '') . ".pdf";
    $pdf->Output($filename, 'D');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate PDF: ' + $e->getMessage()]);
    exit;
}
exit;
?>