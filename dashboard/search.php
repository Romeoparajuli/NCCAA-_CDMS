<?php
// Database connection
include 'includes/db_connection.php';

$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($searchQuery)) {
    // Prepare search query
    $stmt = $conn->prepare("SELECT * FROM agencies WHERE agency_name LIKE ? UNION ALL
                            SELECT * FROM reports WHERE program_name LIKE ? OR introduction LIKE ? OR objectives LIKE ?
                            UNION ALL
                            SELECT * FROM members WHERE name LIKE ? OR email LIKE ? OR contact_number LIKE ?");
    $stmt->bind_param("sssssss", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    $searchResults = array();
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = array(
            'name' => $row['name'],
            'type' => $row['agency_name'] ? 'Agency' : ($row['program_name'] ? 'Report' : 'Member')
        );
    }

    // Close database connection
    $stmt->close();
    $conn->close();

    // Return search results in JSON format
    header('Content-Type: application/json');
    echo json_encode($searchResults);
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'No search query provided'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Bar with Database</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body class="bg-gray-100">

    <!-- Search Bar with Action Icons -->
    <div class="flex items-center mb-4 p-4">
        <input type="text" id="searchInput" placeholder="Search..." class="border border-gray-300 p-2 rounded w-full">
        <div class="flex items-center ml-4">
            <a href="../auth/createUser.php" class="text-gray-600 hover:text-green-600">
                <i class="fas fa-cog sidebar-icon"></i>
            </a>
            <a href="#" class="text-gray-600 hover:text-green-600 ml-4">
                <i class="fas fa-user sidebar-icon"></i>
            </a>
        </div>
    </div>

    <!-- Display Results -->
    <div id="searchResults" class="mt-4 px-4"></div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            let query = this.value;
            if (query.length > 2) {  // Trigger search after typing at least 3 characters
                fetch('search.php?q=' + query)
                    .then(response => response.json())
                    .then(data => {
                        let resultsContainer = document.getElementById('searchResults');
                        resultsContainer.innerHTML = '';  // Clear previous results
                        if (data.length > 0) {
                            data.forEach(item => {
                                let div = document.createElement('div');
                                div.classList.add('p-2', 'border-b', 'border-gray-300', 'bg-white', 'rounded', 'mb-2');
                                div.innerHTML = `<strong>${item.type}</strong>: ${item.name}`;
                                resultsContainer.appendChild(div);
                            });
                        } else {
                            resultsContainer.innerHTML = '<p class="text-gray-600">No results found</p>';
                        }
                    });
            } else {
                document.getElementById('searchResults').innerHTML = '';
            }
        });
    </script>

</body>
</html>
