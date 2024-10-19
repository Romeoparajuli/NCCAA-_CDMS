<?php
// Initialize the error message variable
$errorMsg = ''; // Ensure this variable is defined

// Your existing logic to populate $reports and $districts goes here...

// Example: Setting an error message based on some condition
// if (/* some error condition */) {
//     $errorMsg = 'An error occurred while fetching the reports.';
// }

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
                <input type="date" id="filter-input" name="date" class="border border-gray-300 p-2 rounded" placeholder="Select Date">
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
                            <tr class="report-row">
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
            </div>

            <div class="mt-4">
                <button class="bg-green-600 text-white p-2 rounded" onclick="loadCreateReport()">Create New Report</button>
            </div>

            <div id="notification-popup" class="notification-popup hidden">
                <span></span>
                <span class="close-btn" onclick="closeNotification()">&times;</span>
            </div>
        </main>
    </div>

    
    <!-- ... -->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterButton = document.getElementById('filter-button');
        if (filterButton) {
            filterButton.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent default form submission
                const filterInput = document.getElementById('filter-input').value.trim().toLowerCase();
                const districtSelect = document.getElementById('district-select').value;
                filterReports(filterInput, districtSelect);
            });
        }
    });

    // Function to view detailed report
    function viewReport(reportId) {
        window.location.href = 'view_report.php?id=' + reportId;
    }

    // Function to load the create report page
    function loadCreateReport() {
        window.location.href = 'create_report.php';
    }

    // Function to close notification popup
    function closeNotification() {
        document.getElementById('notification-popup').style.display = 'none';
    }

    // Function to filter reports based on input
    function filterReports(dateValue, districtValue) {
        const rows = document.querySelectorAll('#reports-table tbody tr');
        rows.forEach(row => {
            const date = row.cells[1].textContent.trim();
            const programName = row.cells[2].textContent.trim();
            const matchDate = !dateValue || date.includes(dateValue);
            const matchDistrict = !districtValue || programName === districtValue;
            
            // Show or hide the row based on the filter
            if (matchDate && matchDistrict) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }
</script>
    
</body>
</html>
