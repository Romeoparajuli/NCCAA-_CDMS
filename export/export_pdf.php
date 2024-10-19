<?php
require '../includes/db_connection.php';
session_start(); // Start session at the beginning

// Check if the user has permission to export data
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Province Admin' && $_SESSION['role'] !== 'District Admin')) {
    $_SESSION['error_message'] = "You do not have permission to access this resource.";
    header("Location: cadet_details.php?id=" . ($_GET['id'] ?? ''));
    exit();
}

// Initialize variables
$cadet_id = $_GET['id'] ?? null;
$cadet_details = [];

// Fetch cadet details if ID is provided
if ($cadet_id) {
    $stmt = $pdo->prepare("SELECT cadets.*, provinces.name AS province_name, districts.name AS district_name 
                            FROM cadets 
                            LEFT JOIN provinces ON cadets.province_id = provinces.id 
                            LEFT JOIN districts ON cadets.district_id = districts.id 
                            WHERE cadets.id = ?");
    $stmt->execute([$cadet_id]);
    $cadet_details = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$cadet_details) {
    die("Cadet not found.");
}

// Load PDF library
require 'vendor/autoload.php'; // Ensure you have installed the library via Composer

use Dompdf\Dompdf;

// Initialize Dompdf
$dompdf = new Dompdf();

// Create HTML content for the PDF
$html = '<h1>Cadet Details</h1>';
$html .= '<p><strong>Name:</strong> ' . htmlspecialchars($cadet_details['name']) . '</p>';
$html .= '<p><strong>Date of Birth:</strong> ' . htmlspecialchars($cadet_details['date_of_birth']) . '</p>';
$html .= '<p><strong>Gender:</strong> ' . htmlspecialchars($cadet_details['gender']) . '</p>';
$html .= '<p><strong>Blood Group:</strong> ' . htmlspecialchars($cadet_details['blood_group']) . '</p>';
$html .= '<p><strong>Contact Number:</strong> ' . htmlspecialchars($cadet_details['contact_number']) . '</p>';
$html .= '<p><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($cadet_details['email']) . '">' . htmlspecialchars($cadet_details['email']) . '</a></p>';
$html .= '<p><strong>Facebook Link:</strong> <a href="' . htmlspecialchars($cadet_details['facebook_link']) . '" target="_blank">' . htmlspecialchars($cadet_details['facebook_link']) . '</a></p>';
$html .= '<h2>Family Information</h2>';
$html .= '<p><strong>Father\'s Name:</strong> ' . htmlspecialchars($cadet_details['father_name']) . '</p>';
$html .= '<p><strong>Mother\'s Name:</strong> ' . htmlspecialchars($cadet_details['mother_name']) . '</p>';
$html .= '<h2>Address Information</h2>';
$html .= '<p><strong>Permanent Address:</strong> ' . htmlspecialchars($cadet_details['permanent_address']) . '</p>';
$html .= '<p><strong>Temporary Address:</strong> ' . htmlspecialchars($cadet_details['temporary_address']) . '</p>';
$html .= '<h2>NCC Information</h2>';
$html .= '<p><strong>Education Qualification:</strong> ' . htmlspecialchars($cadet_details['education_qualification']) . '</p>';
$html .= '<h2>Status Information</h2>';
$html .= '<p><strong>District:</strong> ' . htmlspecialchars($cadet_details['district_name']) . '</p>';
$html .= '<p><strong>Province:</strong> ' . htmlspecialchars($cadet_details['province_name']) . '</p>';

// Load HTML content into Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the PDF
$dompdf->render();

// Set headers to prompt for download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"cadet_details_{$cadet_id}.pdf\"");
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Output the generated PDF to browser
echo $dompdf->output();
?>
