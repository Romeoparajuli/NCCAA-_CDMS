<?php
// Include the database connection file
require '../includes/db_connection.php';

// Fetch districts from the database
$stmt = $pdo->query("SELECT * FROM districts");
$districts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch agencies based on the selected district 
$selected_district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : null;
$agencies = [];
$district_name = '';

// Check if a district is selected
if ($selected_district_id) {
    // Fetch the district name
    $stmt = $pdo->prepare("SELECT name FROM districts WHERE id = ?");
    $stmt->execute([$selected_district_id]);
    $district = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If the district exists, fetch the agencies and their contacts
    if ($district) {
        $district_name = $district['name']; 
        
        // Join query to fetch agencies and related contacts
        $stmt = $pdo->prepare("
            SELECT agencies.*, agency_contacts.contact_person, agency_contacts.phone, agency_contacts.email
            FROM agencies
            LEFT JOIN agency_contacts ON agencies.id = agency_contacts.agency_id
            WHERE agencies.district_id = ?
        ");
        $stmt->execute([$selected_district_id]);
        $agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $district_name = 'Unknown District';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agencies by District</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .agency-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .agency-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .btn {
            background-color: #3B6A1D; /* Olive Green */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            display: inline-block;
        }
        .btn:hover {
            background-color: #4D8B2D; /* Darker Olive Green */
            transform: translateY(-2px);
        }
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4 text-center text-olive">Agencies by District</h1>
        
        <!-- District Selection -->
        <div class="mb-4">
            <form method="GET">
                <select name="district_id" onchange="this.form.submit()" class="w-full p-2 border rounded">
                    <option value="">Select a District</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo $district['id']; ?>" <?php echo ($selected_district_id == $district['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($district['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($selected_district_id && isset($district_name)): ?>
            <h2 class="text-2xl font-bold mb-4">Agencies in <?php echo htmlspecialchars($district_name); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($agencies)): ?>
                    <?php foreach ($agencies as $agency): ?>
                        <div class="agency-card bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                            <h3 class="text-xl font-semibold text-olive"><?php echo htmlspecialchars($agency['agency_name']); ?></h3>
                            <p class="mt-2">
                                <strong>Contact Info:</strong> 
                                <?php if (!empty($agency['contact_person'])): ?>
                                    <div>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($agency['contact_person']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($agency['phone']); ?></p>
                                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($agency['email']); ?>" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($agency['email']); ?></a></p>
                                        <p><strong>Website:</strong> 
                                            <a href="<?php echo htmlspecialchars($agency['website']); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($agency['website']); ?></a>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </p>
                            <div class="mt-4">
                                <a href="edit_agency.php?id=<?php echo $agency['id']; ?>" class="text-blue-500 hover:underline">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_agency.php?id=<?php echo $agency['id']; ?>" class="text-red-500 hover:underline ml-4">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="mt-4 text-gray-600">No agencies found for this district.</p>
                <?php endif; ?>
            </div>
        <?php elseif ($selected_district_id): ?>
            <p class="mt-4 text-gray-600">The selected district does not exist.</p>
        <?php else: ?>
            <p class="mt-4 text-gray-600">Please select a district to view the agencies.</p>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="mt-6 flex justify-between">
            <a href="../dashboard/dashboard.php" class="btn">Back to Dashboard</a>
            <a href="add_agency.php" class="btn">Add Agency</a>
        </div>
    </div>
</body>
</html>
