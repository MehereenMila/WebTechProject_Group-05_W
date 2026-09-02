<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'full';
$colCount = ($mode === 'mini') ? 4 : 6;

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    http_response_code(403);
    echo "<tr><td colspan='$colCount' style='text-align:center;'>Unauthorized Access</td></tr>";
    exit();
}

$sql = "SELECT d.id, d.food_type, d.location, d.delivery_location, d.status,
               u1.name as donor_name,
               u2.name as volunteer_name, u2.phone as volunteer_phone
        FROM donations d
        JOIN users u1 ON d.donor_id = u1.id
        LEFT JOIN users u2 ON d.volunteer_id = u2.id
        WHERE d.status IN ('Assigned', 'In Transit')
        ORDER BY d.id DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) 
{
    while ($row = $result->fetch_assoc()) 
    {
        $volunteer = $row['volunteer_name'] ? $row['volunteer_name'] : 'Not Assigned';
        $phone = $row['volunteer_phone'] ? $row['volunteer_phone'] : '-';
        $deliveryLoc = !empty($row['delivery_location']) ? $row['delivery_location'] : 'Not specified';

        if ($mode === 'mini')
        {
            echo "<tr style='border-top:1px solid #f0f0f0; font-size:13px;'>";
            echo "<td style='padding:8px;'><strong>#" . $row['id'] . "</strong> - " . htmlspecialchars($row['food_type']) . "</td>";
            echo "<td style='padding:8px;'>" . htmlspecialchars($volunteer) . "<br><span style='color:#888; font-size:11px;'>" . $phone . "</span></td>";
            echo "<td style='padding:8px;'>📍 " . htmlspecialchars($row['location']) . " <br>➔ 🏁 " . htmlspecialchars($deliveryLoc) . "</td>";
            echo "<td style='padding:8px;'><span class='status badge-transit'>" . htmlspecialchars($row['status']) . "</span></td>";
            echo "</tr>";
        } 
        else 
        {
            $statusClass = ($row['status'] === 'Assigned') ? 'status-assigned' : 'status-transit';
            echo "<tr>";
            echo "<td><strong>#" . $row['id'] . "</strong> - " . htmlspecialchars($row['food_type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['donor_name']) . "</td>";
            echo "<td>" . htmlspecialchars($volunteer) . "<br><span style='color:#888; font-size:12px;'>" . $phone . "</span></td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . htmlspecialchars($deliveryLoc) . "</td>";
            echo "<td><span class='status-pill " . $statusClass . "'>" . htmlspecialchars($row['status']) . "</span></td>";
            echo "</tr>";
        }
    }
} 
else 
{
    echo "<tr><td colspan='$colCount' style='text-align:center; color:#999; padding:15px;'>No active deliveries found.</td></tr>";
}

$conn->close();
?>
