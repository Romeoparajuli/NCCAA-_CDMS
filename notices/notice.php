<?php
require '../includes/db_connection.php';

// Fetch recent notices from the database
$sql = "SELECT * FROM notices ORDER BY created_at DESC"; // Order by creation date
$stmt = $pdo->prepare($sql);
$stmt->execute();
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function loadCreateNotice() {
            fetch('../notices/create_notice.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-placeholder').innerHTML = data;
                })
                .catch(error => console.error('Error loading create notice:', error));
        }

        function loadViewNotice(id) {
            fetch(`view_notice.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-placeholder').innerHTML = data;
                })
                .catch(error => console.error('Error loading view notice:', error));
        }
    </script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-green-800 mb-6">Recent Notices</h1>

    <!-- Button to create a new notice -->
    <button onclick="loadCreateNotice()" class="mb-6 inline-block bg-green-700 text-white py-2 px-4 rounded hover:bg-green-600 transition duration-200">Create Notice</button>

    <!-- Table for notices -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posted By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (count($notices) > 0): ?>
                    <?php foreach ($notices as $notice): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="javascript:void(0);" onclick="loadViewNotice(<?= htmlspecialchars($notice['id']) ?>);" class="text-green-600 hover:underline"><?= htmlspecialchars($notice['title']) ?></a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($notice['posted_by']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars(date('F j, Y, g:i a', strtotime($notice['created_at']))) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($notice['priority']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-gray-700 py-4">No notices found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Placeholder for loading content -->
    <div id="content-placeholder" class="mt-6"></div>
</div>
</body>
</html>
