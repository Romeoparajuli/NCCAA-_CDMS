<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'C:/xampp/htdocs/NCCAA_CDMS/includes/db_connection.php';

$cadet_id = $_GET['id'];
$cadet_query = "SELECT * FROM cadets WHERE id = ?";
$cadet_stmt = $pdo->prepare($cadet_query);
$cadet_stmt->execute([$cadet_id]);
$cadet_details = $cadet_stmt->fetch(PDO::FETCH_ASSOC);

$province_query = "SELECT id, name FROM provinces";
$province_result = $pdo->query($province_query);
$provinces = $province_result->fetchAll(PDO::FETCH_ASSOC);

$district_query = "SELECT id, name FROM districts";
$district_result = $pdo->query($district_query);
$districts = $district_result->fetchAll(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        htmlspecialchars($_POST['name']),
        $_POST['date_of_birth'],
        $_POST['gender'],
        htmlspecialchars($_POST['blood_group']),
        htmlspecialchars($_POST['contact_number']),
        htmlspecialchars($_POST['email']),
        htmlspecialchars($_POST['facebook_link']),
        htmlspecialchars($_POST['father_name']),
        htmlspecialchars($_POST['mother_name']),
        htmlspecialchars($_POST['father_contact']),
        htmlspecialchars($_POST['mother_contact']),
        htmlspecialchars($_POST['permanent_address']),
        htmlspecialchars($_POST['temporary_address']),
        htmlspecialchars($_POST['education_qualification']),
        htmlspecialchars($_POST['see_school']),
        htmlspecialchars($_POST['ncc_cadet_number']),
        htmlspecialchars($_POST['rank']),
        htmlspecialchars($_POST['division']),
        htmlspecialchars($_POST['ncc_batch']),
        htmlspecialchars($_POST['ncc_year']),
        htmlspecialchars($_POST['ncc_school']),
        $_POST['active_status'],
        $_POST['province_id'],
        $_POST['district_id'],
        $cadet_id
    ];

    $update_query = "UPDATE cadets SET 
                        name = ?, 
                        date_of_birth = ?, 
                        gender = ?, 
                        blood_group = ?, 
                        contact_number = ?, 
                        email = ?, 
                        facebook_link = ?, 
                        father_name = ?, 
                        mother_name = ?, 
                        father_contact = ?, 
                        mother_contact = ?, 
                        permanent_address = ?, 
                        temporary_address = ?, 
                        education_qualification = ?, 
                        see_school = ?, 
                        ncc_cadet_number = ?, 
                        rank = ?, 
                        division = ?, 
                        ncc_batch = ?, 
                        ncc_year = ?, 
                        ncc_school = ?, 
                        active_status = ?, 
                        province_id = ?, 
                        district_id = ? 
                    WHERE id = ?";

    $update_stmt = $pdo->prepare($update_query);

    if ($update_stmt->execute($data)) {
        $message = 'Cadet profile updated successfully!';
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'cadet_detail.php?id=$cadet_id';
                }, 5000);
              </script>";
    } else {
        $message = 'Error updating cadet profile.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Cadet Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9fafb;
        }
        .form-container {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }
        .form-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-field {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-input, .form-select {
            border: 1px solid #d1d5db;
            border-radius: 5px;
            padding: 8px;
            width: 100%;
        }
        .btn {
            background-color: #4a8e2d;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn:hover {
            background-color: #3b6e23;
            transform: scale(1.05);
        }
        .message {
            color: green;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="form-title">Update Cadet Profile</div>
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="grid grid-cols-2 gap-4">
            <!-- Form fields go here -->
            <div class="form-field">
                <label class="form-label">Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($cadet_details['name']); ?>" class="form-input" required>
            </div>
            <div class="form-field">
                <label class="form-label">Date of Birth:</label>
                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($cadet_details['date_of_birth']); ?>" class="form-input" required>
            </div>
            <div class="form-field">
                <label class="form-label">Gender:</label>
                <select name="gender" class="form-select" required>
                    <option value="Male" <?php echo $cadet_details['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $cadet_details['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <div class="form-field">
                <label class="form-label">Blood Group:</label>
                <input type="text" name="blood_group" value="<?php echo htmlspecialchars($cadet_details['blood_group']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Contact Number:</label>
                <input type="text" name="contact_number" value="<?php echo htmlspecialchars($cadet_details['contact_number']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($cadet_details['email']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Facebook Link:</label>
                <input type="url" name="facebook_link" value="<?php echo htmlspecialchars($cadet_details['facebook_link']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Father's Name:</label>
                <input type="text" name="father_name" value="<?php echo htmlspecialchars($cadet_details['father_name']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Mother's Name:</label>
                <input type="text" name="mother_name" value="<?php echo htmlspecialchars($cadet_details['mother_name']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Father's Contact:</label>
                <input type="text" name="father_contact" value="<?php echo htmlspecialchars($cadet_details['father_contact']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Mother's Contact:</label>
                <input type="text" name="mother_contact" value="<?php echo htmlspecialchars($cadet_details['mother_contact']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Permanent Address:</label>
                <input type="text" name="permanent_address" value="<?php echo htmlspecialchars($cadet_details['permanent_address']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Temporary Address:</label>
                <input type="text" name="temporary_address" value="<?php echo htmlspecialchars($cadet_details['temporary_address']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Education Qualification:</label>
                <input type="text" name="education_qualification" value="<?php echo htmlspecialchars($cadet_details['education_qualification']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">See School:</label>
                <input type="text" name="see_school" value="<?php echo htmlspecialchars($cadet_details['see_school']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">NCC Cadet Number:</label>
                <input type="text" name="ncc_cadet_number" value="<?php echo htmlspecialchars($cadet_details['ncc_cadet_number']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Rank:</label>
                <input type="text" name="rank" value="<?php echo htmlspecialchars($cadet_details['rank']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Division:</label>
                <input type="text" name="division" value="<?php echo htmlspecialchars($cadet_details['division']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">NCC Batch:</label>
                <input type="text" name="ncc_batch" value="<?php echo htmlspecialchars($cadet_details['ncc_batch']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">NCC Year:</label>
                <input type="text" name="ncc_year" value="<?php echo htmlspecialchars($cadet_details['ncc_year']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">NCC School:</label>
                <input type="text" name="ncc_school" value="<?php echo htmlspecialchars($cadet_details['ncc_school']); ?>" class="form-input">
            </div>
            <div class="form-field">
                <label class="form-label">Active Status:</label>
                <select name="active_status" class="form-select">
                    <option value="1" <?php echo $cadet_details['active_status'] == 1 ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $cadet_details['active_status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-field">
                <label class="form-label">Province:</label>
                <select name="province_id" class="form-select">
                    <?php foreach ($provinces as $province): ?>
                        <option value="<?php echo $province['id']; ?>" <?php echo $cadet_details['province_id'] == $province['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($province['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label class="form-label">District:</label>
                <select name="district_id" class="form-select">
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo $district['id']; ?>" <?php echo $cadet_details['district_id'] == $district['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($district['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="button" class="btn" onclick="window.location.href='cadet_detail.php?id=<?php echo $cadet_id; ?>';">Back</button>
            <button type="submit" class="btn">Update Cadet</button>
        </div>
    </form>
</div>

</body>
</html>
