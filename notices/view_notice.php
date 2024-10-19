<?php
require '../includes/db_connection.php';
session_start(); // Start session to check user role

// Get the notice ID from the query parameter
$notice_id = $_GET['id'] ?? null; // Use null coalescing to avoid undefined index warning

// Prepare the SQL statement
$sql = "SELECT * FROM notices WHERE id = ?";
$stmt = $pdo->prepare($sql);

// Bind the notice ID
$stmt->bindValue(1, $notice_id, PDO::PARAM_INT);

// Execute the statement
$stmt->execute();

// Fetch the result
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the notice was found
if (!$notice) {
    echo "<div class='container mx-auto p-4 text-center'><h2 class='text-red-600 font-bold'>Notice not found.</h2></div>";
    exit;
}

// Simulate admin role for testing
if (isset($_GET['role']) && $_GET['role'] === 'admin') {
    $_SESSION['role'] = 'Province Admin'; // Set to admin role for testing
}

// Check if the user is an admin
$isAdmin = isset($_SESSION['role']) && ($_SESSION['role'] === 'Province Admin' || $_SESSION['role'] === 'District Admin');

// Get the current date
$currentDate = date('Y-m-d'); // Format the date as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notice</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif; /* Set font to Times New Roman */
        }

        /* A4 size settings */
        .a4-size {
            width: 210mm; /* A4 size width */
            height: 297mm; /* A4 size height */
            padding: 20mm; /* Add padding for printed content */
            margin: 0 auto; /* Center the content */
            background-color: #ffffff; /* White background for the notice */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow for better visual separation */
            border: 1px solid #ccc; /* Optional border for better visibility */
        }

        /* Styling for text */
        .notice-text {
            margin: 15px 0; /* Add vertical margin to the text */
            text-align: justify; /* Justify text for better paragraph formatting */
            line-height: 1.5; /* Increase line height for readability */
            margin-left: 15mm; /* Left margin */
            margin-right: 15mm; /* Right margin */
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                width: 210mm; /* A4 size width */
                height: 297mm; /* A4 size height */
            }
            .container {
                page-break-after: always; /* Ensure each notice starts on a new page when printed */
            }
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-6">
    <div class="a4-size rounded-lg p-6">
        <!-- Letterhead Section -->
        <div class="text-center mb-6">
            <img src="../images/logo.png" alt="Logo" class="mx-auto h-24"> <!-- Logo -->
            <h1 class="text-2xl font-bold mt-2">एन.सि.सि अल्मुनाई एशोसिएशन नेपाल​</h1> <!-- Organization Name -->
            <h2 class="text-xl font-semibold">लुम्बिनी प्रदेश</h2> <!-- Province Name -->
        </div>
        
        <div class="flex justify-between mb-6">
            <div class="notice-text">
                <p>पत्र संख्याः-</p>
                <p>चलानी नं:-</p>
            </div>
            <div class="notice-text">
                <p>मिति: <?= htmlspecialchars($currentDate) ?></p> <!-- Current Date -->
            </div>
        </div>

        <!-- Notice Content -->
        <h2 class="text-2xl font-bold text-green-600 mb-4 notice-text"><?= htmlspecialchars($notice['title']) ?></h2>
        <p class="notice-text"><?= nl2br(htmlspecialchars($notice['description'])) ?></p>
        <p class="notice-text"><strong>Type:</strong> <?= htmlspecialchars($notice['type']) ?></p>
        <p class="notice-text"><strong>Priority:</strong> <?= htmlspecialchars($notice['priority']) ?></p>
        

        <div class="mt-6 flex justify-between space-x-4">
            <a href="../dashboard/dashboard.php" class="inline-block bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 transition duration-200">Back </a>
            <?php if ($isAdmin): ?>
                <div class="flex space-x-2">
                    <a href="update_notice.php?id=<?= htmlspecialchars($notice['id']) ?>" class="inline-block bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-400 transition duration-200">Update Notice</a>
                    <a href="delete_notice.php?id=<?= htmlspecialchars($notice['id']) ?>" class="inline-block bg-red-600 text-white py-2 px-4 rounded hover:bg-red-500 transition duration-200">Delete Notice</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
