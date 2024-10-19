<?php
require_once '../includes/db_connection.php';

// Get the agency ID from the request (e.g., from GET parameters)
$agency_id = $_GET['id'] ?? null;

if ($agency_id) {
    // Fetch agency details with contact information
    $stmt = $pdo->prepare("
        SELECT a.*, ac.contact_person, ac.phone, ac.email, ac.address 
        FROM agencies a
        LEFT JOIN agency_contacts ac ON a.id = ac.agency_id
        WHERE a.id = ?
    ");
    $stmt->execute([$agency_id]);
    $agency = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agency) {
        echo "Agency not found.";
        exit;
    }

    // Fetch categories for the dropdown
    $categories = $pdo->query("SELECT * FROM agency_categories")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch districts for the dropdown
    $districts = $pdo->query("SELECT * FROM districts")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all subcategories for all categories
    $subcategories = $pdo->query("SELECT * FROM agency_subcategories")->fetchAll(PDO::FETCH_ASSOC);

    // Function to organize subcategories by category
    $subcategories_by_category = [];
    foreach ($subcategories as $subcategory) {
        $subcategories_by_category[$subcategory['category_id']][] = $subcategory;
    }
} else {
    echo "No agency ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-bold text-center mb-6 text-olive-600">Edit Agency</h1>
        
        <form action="update_agency.php" method="POST" class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto">
            <input type="hidden" name="agency_id" value="<?php echo $agency['id']; ?>">

            <div class="mb-4">
                <label for="agency_name" class="block mb-2 text-sm font-medium text-gray-700">Agency Name:</label>
                <input type="text" name="agency_name" value="<?php echo htmlspecialchars($agency['agency_name']); ?>" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
            </div>

            <div class="mb-4">
                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">Category:</label>
                <select name="category_id" class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $agency['category_id']) echo 'selected'; ?>>
                            <?php echo $category['category_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="subcategory_id" class="block mb-2 text-sm font-medium text-gray-700">Subcategory:</label>
                <select name="subcategory_id" class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
                    <option value="">Select a Subcategory</option>
                    <?php if (isset($subcategories_by_category[$agency['category_id']])): ?>
                        <?php foreach ($subcategories_by_category[$agency['category_id']] as $subcategory): ?>
                            <option value="<?php echo $subcategory['id']; ?>" <?php if ($subcategory['id'] == $agency['subcategory_id']) echo 'selected'; ?>>
                                <?php echo $subcategory['subcategory_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="district_id" class="block mb-2 text-sm font-medium text-gray-700">District:</label>
                <select name="district_id" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo $district['id']; ?>" <?php if ($district['id'] == $agency['district_id']) echo 'selected'; ?>>
                            <?php echo $district['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="website" class="block mb-2 text-sm font-medium text-gray-700">Website:</label>
                <input type="url" name="website" value="<?php echo htmlspecialchars($agency['website']); ?>" class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
            </div>

            <h2 class="text-xl font-semibold mb-4 text-olive-600">Contact Information</h2>

            <div class="mb-4">
                <label for="contact_person" class="block mb-2 text-sm font-medium text-gray-700">Contact Person:</label>
                <input type="text" name="contact_person" value="<?php echo htmlspecialchars($agency['contact_person']); ?>" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
            </div>

            <div class="mb-4">
                <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">Phone:</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($agency['phone']); ?>" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
            </div>

            <div class="mb-4">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($agency['email']); ?>" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200">
            </div>

            <div class="mb-4">
                <label for="address" class="block mb-2 text-sm font-medium text-gray-700">Address:</label>
                <textarea name="address" class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600 transition duration-200"><?php echo htmlspecialchars($agency['address']); ?></textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300">Update Agency</button>
        </form>

        <div class="text-center mt-6">
            <a href="agencies.php" class="text-blue-500 hover:text-blue-700 transition duration-300">← Back to Agencies</a>
        </div>
    </div>
</body>
</html>
