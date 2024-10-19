<?php
session_start();
require '../includes/db_connection.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch districts for the dropdown
$districtsQuery = "SELECT id, name FROM districts";
$districtsResult = $pdo->query($districtsQuery);
$districts = $districtsResult->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() for PDO

// Initialize reports
$reports = [];

// Check if filter parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['district_id'])) {
    $districtId = $_POST['district_id'];
    
    // Prepare query to fetch reports based on filters
    $reportsQuery = "SELECT * FROM reports WHERE district_id = ?";
    $stmt = $pdo->prepare($reportsQuery);
    $stmt->execute([$districtId]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Overview - NCCAA Lumbini</title>
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <main class="flex-grow p-6 mt-[-4px]">
            <header class="mb-4">
                <h1 class="text-3xl font-semibold">Reports Overview</h1>
                <p class="text-gray-600">Manage and view reports submitted by districts.</p>
            </header>

            <form method="POST" action="" class="flex mb-4 space-x-2">
                <input type="date" name="date" class="border border-gray-300 p-2 rounded" placeholder="Select Date">
                <select name="district_id" id="district-select" class="border border-gray-300 p-2 rounded" onchange="this.form.submit()">
                    <option value="">Select District</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo htmlspecialchars($district['id']); ?>">
                            <?php echo htmlspecialchars($district['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-green-600 text-white">
                        <th class="py-2 px-4">Report Title</th>
                        <th class="py-2 px-4">Submission Date</th>
                        <th class="py-2 px-4">Program Name</th>
                        <th class="py-2 px-4">Created By</th>
                        <th class="py-2 px-4">Status</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                        <tr>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['title']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['submission_date']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['program_name']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['created_by']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['status']); ?></td>
                            <td class="py-2 px-4 border">
                                <button class="text-blue-600 hover:underline" onclick="viewReport(<?php echo $report['id']; ?>)">View</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-2 px-4 border text-center">No reports found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="mt-4">
                <button class="bg-green-600 text-white p-2 rounded" onclick="loadCreateReport()">Create New Report</button>
            </div>

            <div id="notification-popup" class="notification-popup hidden" style="display: none;">
                <span></span>
                <span class="close-btn" onclick="closeNotification()">&times;</span>
            </div>

            <div id="dynamic-content" class="mt-6"></div> <!-- Content area for dynamic loading -->
        </main>
    </div>

    <script>
        function viewReport(reportId) {
            alert("View report ID: " + reportId);
        }

        function loadCreateReport() {
            window.location.href = 'create_report.php'; // Directly pointing to the file in the same directory
        }

        // Notification popup handling
        function closeNotification() {
            document.getElementById('notification-popup').style.display = 'none';
        }

        // Example to show a notification for demonstration purposes
        document.getElementById('notification-popup').style.display = 'flex';
        setTimeout(() => {
            document.getElementById('notification-popup').style.opacity = '1';
        }, 10);

        // Automatically close notification after 10 seconds
        setTimeout(() => {
            closeNotification();
        }, 10000); // 10000 milliseconds = 10 seconds
    </script>
</body>
</html>
