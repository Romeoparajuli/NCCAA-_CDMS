<?php
// Include database connection
require '../includes/db_connection.php';

// Fetch cadet data from the database
$query = "SELECT id, name, rank, contact_number AS contact_info FROM cadets"; // Adjust this based on your actual column names

// Check for search filter
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query .= " WHERE name LIKE :search OR district LIKE :search"; // Ensure 'district' is a valid column name
}

// Pagination logic
$limit = 30; // Number of cadets per page
$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->execute();
$total_cadets = $stmt->rowCount();
$total_pages = ceil($total_cadets / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Prepare and execute the query with pagination
$query .= " LIMIT :offset, :limit";
$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto mt-10 px-4">
    <h1 class="text-2xl font-bold mb-5 text-olive-600">View Cadets</h1>

    

    <!-- Add Cadet button -->
    <div class="mb-5">
        <a href="http://localhost/NCCAA_CDMS/cadets/add_cadet.php" class="bg-olive-500 text-white px-4 py-2 rounded transition duration-300 hover:bg-olive-600">Add Cadet</a>
    </div>

    <!-- Cadet data table -->
    <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead class="bg-olive-200">
            <tr>
                <th class="py-2 px-4 border-b">Name</th>
                <th class="py-2 px-4 border-b">Rank/Designation</th>
                <th class="py-2 px-4 border-b">Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $cadet): ?>
            <tr class="hover:bg-gray-100 transition duration-300">
                <td class="py-2 px-4 border-b">
                    <a href="../cadets/cadet_detail.php?id=<?php echo $cadet['id']; ?>" class="text-olive-500 hover:text-olive-600 transition duration-300"><?php echo htmlspecialchars($cadet['name']); ?></a>
                </td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($cadet['rank']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($cadet['contact_info']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination links -->
    <div class="mt-5">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="#" onclick="loadCadets(<?php echo $i; ?>, '<?php echo urlencode($search); ?>'); return false;" class="mx-1 px-3 py-1 border border-gray-300 rounded <?php echo ($i == $current_page) ? 'bg-olive-500 text-white' : 'text-olive-500 hover:text-white transition duration-300 hover:bg-olive-500'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<style>
    /* Light olive green theme colors */
    .bg-olive-200 {
        background-color: #d9e7d1; /* Light olive green */
    }
    .bg-olive-500 {
        background-color: #4a8d36; /* Olive green */
    }
    .bg-olive-600 {
        background-color: #3c7329; /* Darker olive green */
    }
    .text-olive-500 {
        color: #4a8d36; /* Olive green */
    }
    .text-olive-600 {
        color: #3c7329; /* Darker olive green */
    }
</style>
