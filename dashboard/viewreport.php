<?php
// Database connection
require '../includes/db_connection.php'; // Ensure you have the correct path for your db connection

// Initialize variables
$errorMsg = '';
$report = null;

// Check if the report ID is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $report_id = intval($_GET['id']);

    // Fetch the report details from the database
    $query = "SELECT * FROM reports WHERE id = :report_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['report_id' => $report_id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        $errorMsg = 'Report not found.';
    }
} else {
    $errorMsg = 'No report ID provided.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details - NCCAA Lumbini</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-4">Report Details</h1>

        <?php if ($errorMsg): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php else: ?>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold">Program Name: <?php echo htmlspecialchars($report['program_name']); ?></h2>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($report['start_date']); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($report['end_date']); ?></p>
                <p><strong>Created By:</strong> <?php echo htmlspecialchars($report['user_id']); ?></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($report['description'])); ?></p>
                <!-- Add more fields as necessary -->
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="reports.php" class="bg-green-600 text-white p-2 rounded">Back to Reports Overview</a>
        </div>
    </div>
</body>
</html>
