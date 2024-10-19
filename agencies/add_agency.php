<?php
require_once '../includes/db_connection.php';

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agency_names = $_POST['agency_name'];
    $category_ids = $_POST['category_id'];
    $subcategory_ids = $_POST['subcategory_id'];
    $district_ids = $_POST['district_id'];
    $websites = $_POST['website'];
    $contact_persons = $_POST['contact_person'];
    $phones = $_POST['phone'];
    $emails = $_POST['email'];
    $addresses = $_POST['address'];

    foreach ($agency_names as $index => $agency_name) {
        try {
            // Insert agency details
            $stmt = $pdo->prepare("INSERT INTO agencies (agency_name, category_id, subcategory_id, district_id, website) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $agency_name,
                $category_ids[$index],
                $subcategory_ids[$index],
                $district_ids[$index],
                $websites[$index],
            ]);

            // Get the last inserted agency ID
            $agency_id = $pdo->lastInsertId();

            // Insert contact details
            $stmt = $pdo->prepare("INSERT INTO agency_contacts (agency_id, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $agency_id,
                $contact_persons[$index],
                $phones[$index],
                $emails[$index],
                $addresses[$index],
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage()); // Log the error message
            echo "Error inserting agency: " . $e->getMessage(); // You can echo or handle it as needed
        }
    }

    // Redirect or show success message after insertion
    header("Location: agencies.php"); // Change to your success page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Agencies</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-bold text-center mb-6 text-olive-600">Manage Agencies</h1>

        <form action="add_agency.php" method="POST" class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto">
    <div id="agencies-container">
        <div class="agency-group mb-6 p-4 border border-gray-300 rounded-lg">
            <h2 class="text-2xl font-semibold mb-2 text-olive-600">Add New Agency</h2>
            
            <div class="mb-4">
                <label for="agency_name[]" class="block mb-2 text-sm font-medium text-gray-700">Agency Name:</label>
                <input type="text" name="agency_name[]" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
            </div>

            <div class="mb-4">
                <label for="category_id[]" class="block mb-2 text-sm font-medium text-gray-700">Category:</label>
                <select name="category_id[]" class="category-select border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="subcategory_id[]" class="block mb-2 text-sm font-medium text-gray-700">Subcategory:</label>
                <select name="subcategory_id[]" class="subcategory-select border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
                    <option value="">Select a Subcategory</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="district_id[]" class="block mb-2 text-sm font-medium text-gray-700">District:</label>
                <select name="district_id[]" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo $district['id']; ?>"><?php echo $district['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="website[]" class="block mb-2 text-sm font-medium text-gray-700">Website:</label>
                <input type="url" name="website[]" class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
            </div>

            <h2 class="text-xl font-semibold mb-4 text-olive-600">Contact Information</h2>

            <div class="mb-4">
                <label for="contact_person[]" class="block mb-2 text-sm font-medium text-gray-700">Contact Person:</label>
                <input type="text" name="contact_person[]" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
            </div>

            <div class="mb-4">
                <label for="phone[]" class="block mb-2 text-sm font-medium text-gray-700">Phone:</label>
                <input type="tel" name="phone[]" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
            </div>

            <div class="mb-4">
                <label for="email[]" class="block mb-2 text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email[]" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600">
            </div>

            <div class="mb-4">
                <label for="address[]" class="block mb-2 text-sm font-medium text-gray-700">Address:</label>
                <textarea name="address[]" class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-olive-600 focus:border-olive-600"></textarea>
            </div>

            <button type="button" class="remove-agency bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300">Remove Agency</button>
        </div>
    </div>

    <button type="button" id="add-agency" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-300 mb-4">Add Another Agency</button>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300">Submit All Agencies</button>
    
    <!-- Back Button -->
    <button type="button" onclick="window.location.href='../agencies/agencies.php'" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-300 mb-4">Back</button>
</form>

    </div>

    <script>
        $(document).ready(function() {
            // Subcategories data organized by category
            const subcategoriesData = <?php echo json_encode($subcategories_by_category); ?>;

            // Populate subcategories based on selected category
            $(document).on('change', '.category-select', function() {
                const selectedCategory = $(this).val();
                const subcategorySelect = $(this).closest('.agency-group').find('.subcategory-select');

                // Clear existing subcategories
                subcategorySelect.empty().append('<option value="">Select a Subcategory</option>');

                if (subcategoriesData[selectedCategory]) {
                    subcategoriesData[selectedCategory].forEach(function(subcategory) {
                        subcategorySelect.append(`<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`);
                    });
                }
            });

            // Add new agency form
            $('#add-agency').click(function() {
                const newAgencyGroup = $('.agency-group:first').clone();
                newAgencyGroup.find('input, select, textarea').val('');
                newAgencyGroup.find('.subcategory-select').empty().append('<option value="">Select a Subcategory</option>');
                $('#agencies-container').append(newAgencyGroup);
            });

            // Remove agency form
            $(document).on('click', '.remove-agency', function() {
                $(this).closest('.agency-group').remove();
            });
        });
    </script>
</body>
</html>
