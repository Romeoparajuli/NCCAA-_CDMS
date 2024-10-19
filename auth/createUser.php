<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include database connection file
require '../includes/db_connection.php'; // Adjust the path as necessary

// Initialize variables for form inputs
$name = $email = $password = $role = $member_type = $district_name = $province_name = "";
$errors = [];

// Fetch provinces and districts from the database
$districts = [];
$provinces = [];

// Fetch province names
$result = $pdo->query("SELECT name FROM provinces");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $provinces[] = $row['name'];
}

// Fetch district names
$result = $pdo->query("SELECT name FROM districts");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $districts[] = $row['name'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $member_type = $_POST['member_type'] ?? null;
    $district_name = $_POST['district_name'] ?? null;
    $province_name = $_POST['province_name'] ?? null;

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if (empty($role)) {
        $errors[] = "Role is required.";
    }
    if ($role === 'District Admin' && empty($district_name)) {
        $errors[] = "District name is required for District Admins.";
    }
    if ($role === 'Province Admin' && empty($province_name)) {
        $errors[] = "Province name is required for Province Admins.";
    }
    if ($role === 'Member' && empty($member_type)) {
        $errors[] = "Member type is required for Members.";
    }
    if ($member_type === 'District Member' && empty($district_name)) {
        $errors[] = "District name is required for District Members.";
    }
    if ($member_type === 'Province Member' && empty($province_name)) {
        $errors[] = "Province name is required for Province Members.";
    }

    // If no errors, proceed with user creation
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
// Prepare SQL statement to insert user
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, member_type, district_id, province_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

// Retrieve district and province IDs based on names
$district_id = null;
if ($district_name) {
    $district_stmt = $pdo->prepare("SELECT id FROM districts WHERE name = ?");
    $district_stmt->execute([$district_name]);
    $district_id = $district_stmt->fetchColumn(); // Get the first column of the first row
}

$province_id = null;
if ($province_name) {
    $province_stmt = $pdo->prepare("SELECT id FROM provinces WHERE name = ?");
    $province_stmt->execute([$province_name]);
    $province_id = $province_stmt->fetchColumn(); // Get the first column of the first row
}

// Bind parameters and execute the statement
$stmt->execute([$name, $email, $hashed_password, $role, $member_type, $district_id, $province_id]);

// Execute the statement and provide feedback
if ($stmt) {
    echo "<script>alert('User created successfully!');</script>";
} else {
    $errors[] = "Error creating user: " . $stmt->errorInfo()[2];
}

        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - NCCAA Lumbini</title>
    <link href="public/output.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold text-center mb-2">NCCAA CDMS</h1>
    <h2 class="text-2xl font-bold text-center mb-6">Create User</h2>

    <!-- Display errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="bg-red-200 text-red-700 p-4 mb-4 rounded">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="bg-white p-6 rounded shadow-md mx-auto" style="max-width: 500px;">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="border rounded w-full py-2 px-3" required value="<?php echo htmlspecialchars($name); ?>">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="border rounded w-full py-2 px-3" required value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="border rounded w-full py-2 px-3" required>
            <button type="button" id="togglePassword" class="text-blue-500 mt-2">Show/Hide Password</button>
        </div>
        <div class="mb-4">
            <label for="role" class="block text-gray-700">Role</label>
            <select name="role" id="role" class="border rounded w-full py-2 px-3" required>
                <option value="">Select Role</option>
                <option value="Province Admin" <?php echo ($role == 'Province Admin') ? 'selected' : ''; ?>>Province Admin</option>
                <option value="District Admin" <?php echo ($role == 'District Admin') ? 'selected' : ''; ?>>District Admin</option>
                <option value="Member" <?php echo ($role == 'Member') ? 'selected' : ''; ?>>Member</option>
            </select>
        </div>
        <div class="mb-4" id="provinceContainer" style="display: none;">
            <label for="province_name" class="block text-gray-700">Select Province</label>
            <select name="province_name" id="province_name" class="border rounded w-full py-2 px-3">
                <option value="">Select Province</option>
                <?php foreach ($provinces as $province): ?>
                    <option value="<?php echo $province; ?>" <?php echo ($province_name == $province) ? 'selected' : ''; ?>><?php echo $province; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4" id="districtContainer" style="display: none;">
            <label for="district_name" class="block text-gray-700">Select District</label>
            <select name="district_name" id="district_name" class="border rounded w-full py-2 px-3">
                <option value="">Select District</option>
                <?php foreach ($districts as $district): ?>
                    <option value="<?php echo $district; ?>" <?php echo ($district_name == $district) ? 'selected' : ''; ?>><?php echo $district; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4" id="memberTypeContainer" style="display: none;">
            <label for="member_type" class="block text-gray-700">Member Type</label>
            <select name="member_type" id="member_type" class="border rounded w-full py-2 px-3">
                <option value="">Select Member Type</option>
                <option value="Province Member" <?php echo ($member_type == 'Province Member') ? 'selected' : ''; ?>>Province Member</option>
                <option value="District Member" <?php echo ($member_type == 'District Member') ? 'selected' : ''; ?>>District Member</option>
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create User</button>
            <a href="../dashboard/dashboard.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back to Dashboard</a>
        </div>
    </form>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
    });

    // JavaScript for dynamic dropdowns based on role selection
    const roleSelect = document.getElementById('role');
    const memberTypeContainer = document.getElementById('memberTypeContainer');
    const provinceContainer = document.getElementById('provinceContainer');
    const districtContainer = document.getElementById('districtContainer');
    const memberTypeSelect = document.getElementById('member_type');

    roleSelect.addEventListener('change', function () {
        provinceContainer.style.display = 'none';
        districtContainer.style.display = 'none';
        memberTypeContainer.style.display = 'none';

        if (this.value === 'Province Admin') {
            provinceContainer.style.display = 'block';
        } else if (this.value === 'District Admin') {
            districtContainer.style.display = 'block';
        } else if (this.value === 'Member') {
            memberTypeContainer.style.display = 'block';
        }
    });

    memberTypeSelect.addEventListener('change', function () {
        provinceContainer.style.display = this.value === 'Province Member' ? 'block' : 'none';
        districtContainer.style.display = this.value === 'District Member' ? 'block' : 'none';
    });
</script>

</body>
</html>
