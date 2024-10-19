<?php
// Include the database connection file
require_once '../includes/db_connection.php';

$contact_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT agency_id FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);
$agency_id = $stmt->fetchColumn();

$stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);

header("Location: agency_contacts.php?id=$agency_id");
?>

