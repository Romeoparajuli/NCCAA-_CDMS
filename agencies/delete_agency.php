<?php
// Include the database connection file
require_once '../includes/db_connection.php';

$agency_id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM agencies WHERE id = ?");
$stmt->execute([$agency_id]);

header("Location: agencies.php");
?>
