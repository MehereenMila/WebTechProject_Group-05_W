<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
require_once __DIR__ . '/../../Lib/fpdf/fpdf.php'; // FPDF library - download from fpdf.org and place here

// Security Check: Admin only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$database = new DatabaseConnection();
$conn = $database->openConnection();

// Period: weekly ba monthly, default weekly
$period = isset($_GET['period']) && $_GET['period'] === 'monthly' ? 'monthly' : 'weekly';

if ($period === 'monthly') {
    $startDate = date('Y-m-01 00:00:00');
    $endDate = date('Y-m-t 23:59:59');
    $rangeLabel = date('F Y');
} else {
    $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
    $endDate = date('Y-m-d 23:59:59');
    $rangeLabel = date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate));
}

$sql = "SELECT d.food_type, d.quantity, d.status, d.created_at, d.delivered_at,
               d.location, d.delivery_location,
               u1.name as donor_name, u2.name as volunteer_name
        FROM donations d
        JOIN users u1 ON d.donor_id = u1.id
        LEFT JOIN users u2 ON d.volunteer_id = u2.id
        WHERE d.created_at BETWEEN '$startDate' AND '$endDate'
        ORDER BY d.created_at ASC";
$result = $conn->query($sql);

$rows = [];
$totalCount = 0;
$deliveredCount = 0;
$pendingCount = 0;
$activeCount = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        $totalCount++;
        if ($row['status'] === 'Delivered') $deliveredCount++;
        elseif ($row['status'] === 'Pending') $pendingCount++;
        else $activeCount++;
    }
}

class FoodSharePDF extends FPDF
{
    public $rangeLabel = '';
    public $periodTitle = '';

    function Header()
    {
        // Brand title: "Food" (dark green) + "Share" (orange)
        $this->SetFont('Arial', 'B', 22);
        $this->SetTextColor(15, 59, 27);
        $this->Cell(30, 10, 'Food', 0, 0);
        $this->SetTextColor(212, 160, 23);
        $this->Cell(30, 10, 'Share', 0, 1);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(90, 90, 90);
        $this->Cell(0, 6, 'Food Waste Reduction & Redistribution System', 0, 1);

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(0, 10, $this->periodTitle . ' Donation Report', 0, 1);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 6, 'Period: ' . $this->rangeLabel, 0, 1);
        $this->Cell(0, 6, 'Generated on: ' . date('d M Y, h:i A'), 0, 1);

        $this->SetDrawColor(30, 126, 52);
        $this->SetLineWidth(0.6);
        $this->Line(10, $this->GetY() + 2, 200, $this->GetY() + 2);
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, '@ ' . date('Y') . ' FoodShare  |  Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function SummaryBox($total, $delivered, $pending, $active)
    {
        $this->SetFont('Arial', 'B', 11);
        $boxW = 45;
        $labels = [
            ['Total Donations', $total, [15, 59, 27]],
            ['Delivered', $delivered, [40, 167, 69]],
            ['Pending', $pending, [240, 173, 78]],
            ['In Progress', $active, [91, 192, 222]],
        ];
        foreach ($labels as $l) {
            $this->SetFillColor($l[2][0], $l[2][1], $l[2][2]);
            $this->SetTextColor(255, 255, 255);
            $this->Rect($this->GetX(), $this->GetY(), $boxW - 3, 18, 'F');
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetFont('Arial', 'B', 16);
            $this->SetXY($x, $y + 2);
            $this->Cell($boxW - 3, 8, $l[1], 0, 2, 'C');
            $this->SetFont('Arial', '', 8);
            $this->Cell($boxW - 3, 6, $l[0], 0, 2, 'C');
            $this->SetXY($x + $boxW, $y);
        }
        $this->Ln(24);
        $this->SetTextColor(0, 0, 0);
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(15, 59, 27);
        $this->SetTextColor(255, 255, 255);
        $widths = [28, 22, 12, 22, 26, 22, 32, 24];
        $headers = ['Donor', 'Food', 'Qty', 'Donated On', 'Volunteer', 'Delivered', 'Route', 'Status'];
        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i], 8, $h, 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
    }
}

$pdf = new FoodSharePDF();
$pdf->periodTitle = ucfirst($period);
$pdf->rangeLabel = $rangeLabel;
$pdf->AliasNbPages();
$pdf->AddPage('L'); // Landscape for wide table

$pdf->SummaryBox($totalCount, $deliveredCount, $pendingCount, $activeCount);
$pdf->TableHeader();

$pdf->SetFont('Arial', '', 8);
$widths = [28, 22, 12, 22, 26, 22, 32, 24];
$fill = false;

foreach ($rows as $row) {
    $pdf->SetFillColor(240, 248, 240);

    $route = $row['location'];
    if (!empty($row['delivery_location'])) {
        $route .= ' -> ' . $row['delivery_location'];
    }
    if (strlen($route) > 30) {
        $route = substr($route, 0, 27) . '...';
    }

    $donatedOn = $row['created_at'] ? date('d M, h:i A', strtotime($row['created_at'])) : '-';
    $deliveredAt = $row['delivered_at'] ? date('d M, h:i A', strtotime($row['delivered_at'])) : '-';
    $volunteer = $row['volunteer_name'] ? $row['volunteer_name'] : 'Unassigned';

    $pdf->Cell($widths[0], 7, substr($row['donor_name'], 0, 16), 1, 0, 'L', $fill);
    $pdf->Cell($widths[1], 7, substr($row['food_type'], 0, 14), 1, 0, 'L', $fill);
    $pdf->Cell($widths[2], 7, $row['quantity'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 7, $donatedOn, 1, 0, 'C', $fill);
    $pdf->Cell($widths[4], 7, substr($volunteer, 0, 16), 1, 0, 'L', $fill);
    $pdf->Cell($widths[5], 7, $deliveredAt, 1, 0, 'C', $fill);
    $pdf->Cell($widths[6], 7, $route, 1, 0, 'L', $fill);
    $pdf->Cell($widths[7], 7, $row['status'], 1, 0, 'C', $fill);
    $pdf->Ln();

    $fill = !$fill;
}

if (empty($rows)) {
    $pdf->Cell(array_sum($widths), 10, 'No donations recorded in this period.', 1, 1, 'C');
}

$filename = 'FoodShare_' . ucfirst($period) . '_Report_' . date('Y-m-d') . '.pdf';
$pdf->Output('D', $filename); // D = force download
$conn->close();
?>