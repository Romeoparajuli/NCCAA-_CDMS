<?php
require '../includes/db_connection.php';

// Initialize variables
$name = $date_of_birth = $gender = $blood_group = $contact_number = $email = "";
$facebook_link = $father_name = $mother_name = $father_contact = $mother_contact = "";
$permanent_address = $temporary_address = $education_qualification = $see_school = "";
$ncc_cadet_number = $rank = $division = $ncc_batch = $ncc_year = $ncc_school = $active_status = "Active";
$district_id = $province_id = "";
$selected_trainings = [];
$errors = [];
$success_message = ""; // Initialize success message variable

// Fetch provinces and districts for dropdowns
$districts = [];
$provinces = [];
$trainings = [];

// Fetch districts
$result = $pdo->query("SELECT id, name FROM districts");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $districts[] = $row;
}

// Fetch provinces
$result = $pdo->query("SELECT id, name FROM provinces");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $provinces[] = $row;
}

// Fetch trainings
$result = $pdo->query("SELECT id, training_name FROM trainingsname");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $trainings[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch and validate inputs
    $name = trim($_POST['name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $facebook_link = trim($_POST['facebook_link']);
    $father_name = trim($_POST['father_name']);
    $mother_name = trim($_POST['mother_name']);
    $father_contact = trim($_POST['father_contact']);
    $mother_contact = trim($_POST['mother_contact']);
    $permanent_address = trim($_POST['permanent_address']);
    $temporary_address = trim($_POST['temporary_address']);
    $education_qualification = trim($_POST['education_qualification']);
    $see_school = trim($_POST['see_school']);
    $ncc_cadet_number = trim($_POST['ncc_cadet_number']);
    $rank = trim($_POST['rank']);
    $division = $_POST['division'];
    $ncc_batch = trim($_POST['ncc_batch']);
    $ncc_year = trim($_POST['ncc_year']);
    $ncc_school = trim($_POST['ncc_school']);
    $active_status = $_POST['active_status'];
    $district_id = $_POST['district_id'];
    $province_id = $_POST['province_id'];
   
    // Validation
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($date_of_birth)) $errors[] = "Date of Birth is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($blood_group)) $errors[] = "Blood Group is required.";
    if (empty($district_id)) $errors[] = "District is required.";
    if (empty($province_id)) $errors[] = "Province is required.";
    if (empty($contact_number)) $errors[] = "Contact Number is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";

    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO cadets 
(name, date_of_birth, gender, blood_group, contact_number, email, 
facebook_link, father_name, mother_name, father_contact, mother_contact, 
permanent_address, temporary_address, education_qualification, see_school, 
ncc_cadet_number, rank, division, ncc_batch, ncc_year, ncc_school, 
active_status, district_id, province_id) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


        $stmt->execute([$name, $date_of_birth, $gender, $blood_group, $contact_number, $email, $facebook_link, 
        $father_name, $mother_name, $father_contact, $mother_contact, $permanent_address, $temporary_address, 
        $education_qualification, $see_school, $ncc_cadet_number, $rank, $division, $ncc_batch, $ncc_year, $ncc_school, 
        $active_status, $district_id, $province_id]);

        // Set success message
        $success_message = "Cadet added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cadet</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .input-field {
            background-color: #f0f8f2; /* Olive green background */
            border: 1px solid #a1c6a8; /* Olive green border */
        }
        .btn-theme {
            background-color: #4f7f4f; /* Olive green button */
            color: white;
            padding: 0.5rem 1.5rem; /* Adjust padding for a better button size */
            font-size: 1rem; /* Font size adjustment */
        }
        .notification {
            position: fixed;
            right: 20px;
            top: 20px;
            background-color: #4caf50; /* Green background */
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white rounded shadow-md">
        <h1 class="text-3xl font-semibold mb-6 text-center">Add Cadet</h1>
        <?php if (!empty($errors)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="notification" id="successNotification">
                <?php echo htmlspecialchars($success_message); ?>
                <button onclick="dismissNotification()" class="ml-2">✖</button>
            </div>
            <script>
                document.getElementById('successNotification').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('successNotification').style.display = 'none';
                }, 5000); // Automatically disappear after 5 seconds
                function dismissNotification() {
                    document.getElementById('successNotification').style.display = 'none';
                }
            </script>
        <?php endif; ?>

        <form method="POST" action="">
            <h2 class="text-xl font-semibold mb-4">Personal Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block font-medium">Full Name:</label>
                    <input type="text" name="name" id="name" class="input-field rounded p-2 w-full" placeholder="Enter full name" required>
                </div>
                <div>
                    <label for="date_of_birth" class="block font-medium">Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="input-field rounded p-2 w-full" required>
                </div>
                <div>
                    <label for="gender" class="block font-medium">Gender:</label>
                    <select name="gender" id="gender" class="input-field rounded p-2 w-full" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="blood_group" class="block font-medium">Blood Group:</label>
                    <select name="blood_group" id="blood_group" class="input-field rounded p-2 w-full" required>
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div>
                    <label for="contact_number" class="block font-medium">Contact Number:</label>
                    <input type="text" name="contact_number" id="contact_number" class="input-field rounded p-2 w-full" placeholder="Enter contact number" required>
                </div>
                <div>
                    <label for="email" class="block font-medium">Email:</label>
                    <input type="email" name="email" id="email" class="input-field rounded p-2 w-full" placeholder="Enter email" required>
                </div>
                <div>
                    <label for="facebook_link" class="block font-medium">Facebook Link:</label>
                    <input type="url" name="facebook_link" id="facebook_link" class="input-field rounded p-2 w-full" placeholder="Enter Facebook link">
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-4">Family Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="father_name" class="block font-medium">Father's Name:</label>
                    <input type="text" name="father_name" id="father_name" class="input-field rounded p-2 w-full" placeholder="Enter father's name">
                </div>
                <div>
                    <label for="mother_name" class="block font-medium">Mother's Name:</label>
                    <input type="text" name="mother_name" id="mother_name" class="input-field rounded p-2 w-full" placeholder="Enter mother's name">
                </div>
                <div>
                    <label for="father_contact" class="block font-medium">Father's Contact:</label>
                    <input type="text" name="father_contact" id="father_contact" class="input-field rounded p-2 w-full" placeholder="Enter father's contact number">
                </div>
                <div>
                    <label for="mother_contact" class="block font-medium">Mother's Contact:</label>
                    <input type="text" name="mother_contact" id="mother_contact" class="input-field rounded p-2 w-full" placeholder="Enter mother's contact number">
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-4">Address Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="permanent_address" class="block font-medium">Permanent Address:</label>
                    <input type="text" name="permanent_address" id="permanent_address" class="input-field rounded p-2 w-full" placeholder="Enter permanent address">
                </div>
                <div>
                    <label for="temporary_address" class="block font-medium">Temporary Address:</label>
                    <input type="text" name="temporary_address" id="temporary_address" class="input-field rounded p-2 w-full" placeholder="Enter temporary address">
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-4">NCC Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="education_qualification" class="block font-medium">Education Qualification:</label>
                    <input type="text" name="education_qualification" id="education_qualification" class="input-field rounded p-2 w-full" placeholder="Enter education qualification">
                </div>
                <div>
                    <label for="see_school" class="block font-medium">School Name:</label>
                    <input type="text" name="see_school" id="see_school" class="input-field rounded p-2 w-full" placeholder="Enter school name">
                </div>
                <div>
                    <label for="ncc_cadet_number" class="block font-medium">NCC Cadet Number:</label>
                    <input type="text" name="ncc_cadet_number" id="ncc_cadet_number" class="input-field rounded p-2 w-full" placeholder="Enter NCC cadet number">
                </div>
                <div>
                    <label for="rank" class="block font-medium">Rank:</label>
                    <input type="text" name="rank" id="rank" class="input-field rounded p-2 w-full" placeholder="Enter rank">
                </div>
                <div>
                    <label for="division" class="block font-medium">Division:</label>
                    <select name="division" id="division" class="input-field rounded p-2 w-full">
                        <option value="A">Senior</option>
                        <option value="B">Junior</option>
                    
                    </select>
                </div>
                <div>
                    <label for="ncc_batch" class="block font-medium">NCC Batch:</label>
                    <input type="text" name="ncc_batch" id="ncc_batch" class="input-field rounded p-2 w-full" placeholder="Enter NCC batch">
                </div>
                <div>
                    <label for="ncc_year" class="block font-medium">NCC Year:</label>
                    <input type="text" name="ncc_year" id="ncc_year" class="input-field rounded p-2 w-full" placeholder="Enter NCC year">
                </div>
                <div>
                    <label for="ncc_school" class="block font-medium">NCC School:</label>
                    <input type="text" name="ncc_school" id="ncc_school" class="input-field rounded p-2 w-full" placeholder="Enter NCC school name">
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-4">Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="active_status" class="block font-medium">Active Status:</label>
                    <select name="active_status" id="active_status" class="input-field rounded p-2 w-full">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="district_id" class="block font-medium">District:</label>
                    <select name="district_id" id="district_id" class="input-field rounded p-2 w-full" required>
                        <option value="">Select District</option>
                        <?php foreach ($districts as $district): ?>
                            <option value="<?php echo $district['id']; ?>"><?php echo htmlspecialchars($district['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="province_id" class="block font-medium">Province:</label>
                    <select name="province_id" id="province_id" class="input-field rounded p-2 w-full" required>
                        <option value="">Select Province</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?php echo $province['id']; ?>"><?php echo htmlspecialchars($province['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="text-center">
    <!-- Add Cadet Button styled to match the Back button -->
    <button type="submit" 
            class="inline-flex items-center justify-center px-6 py-2 text-white bg-gray-600 hover:bg-green-700 focus:ring-4 focus:ring-green-400 rounded-lg shadow-md transform hover:scale-105 transition-transform ease-in-out duration-300 mr-4">
        <i class="fas fa-user-plus mr-2"></i> Add Cadet
    </button>

    <!-- Back Button -->
    <a href="http://localhost/NCCAA_CDMS/dashboard/dashboard.php" 
       class="inline-flex items-center justify-center px-6 py-2 text-white bg-gray-600 hover:bg-green-700 focus:ring-4 focus:ring-green-400 rounded-lg shadow-md transform hover:scale-105 transition-transform ease-in-out duration-300">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>
        </form>
    </div>
</body>
</html>
