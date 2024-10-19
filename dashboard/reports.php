<?php
// Database connection
require '../includes/db_connection.php'; // Ensure you have the correct path for your db connection

$errorMsg = '';

// Fetch all districts
$districts = [];
$query = "SELECT id, name FROM districts";
$result = $pdo->query($query);
if ($result) {
    $districts = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    $errorMsg = 'Failed to fetch districts.';
}

// Fetch reports based on selected district (if any)
$reports = [];
if (isset($_POST['district_id']) && !empty($_POST['district_id'])) {
    $district_id = intval($_POST['district_id']);
    $query = "SELECT * FROM reports WHERE district_id = :district_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['district_id' => $district_id]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all reports if no district is selected
    $query = "SELECT * FROM reports";
    $stmt = $pdo->query($query);
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

            <form id="filter-form" method="POST" class="flex mb-4 space-x-2">
                <select name="district_id" id="district-select" class="border border-gray-300 p-2 rounded">
                    <option value="">Select District</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo htmlspecialchars($district['id']); ?>">
                            <?php echo htmlspecialchars($district['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" id="filter-button" class="bg-green-600 text-white p-2 rounded">Filter</button>
            </form>

            <?php if ($errorMsg): ?>
                <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                    <?php echo htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>

            <div id="reports-table" class="mt-4">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-green-600 text-white">
                            <th class="py-2 px-4">Program Name</th>
                            <th class="py-2 px-4">Start Date</th>
                            <th class="py-2 px-4">End Date</th>
                            <th class="py-2 px-4">Created By</th>
                            <th class="py-2 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reports)): ?>
                            <?php foreach ($reports as $report): ?>
                            <tr class="report-row">
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['program_name']); ?></td>
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['start_date']); ?></td>
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['end_date']); ?></td>
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($report['user_id']); ?></td>
                                <td class="py-2 px-4 border">
                                    <button class="text-blue-600 hover:underline" onclick="viewReport(<?php echo $report['id']; ?>)">View</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-2 px-4 border text-center">No reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button class="bg-green-600 text-white p-2 rounded" onclick="loadCreateReport()">Create New Report</button>
            </div>

        </main>
    </div>

    <script>
        // Function to view detailed report
        function viewReport(reportId) {
            window.location.href = './viewreport.php?id=' + reportId;
        }

        // Function to load the create report page
        function loadCreateReport() {
            window.location.href = 'create_report.php';
        }
    </script>
</body>
</html>
