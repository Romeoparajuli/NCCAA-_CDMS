<?php
require '../includes/db_connection.php';


// Initialize variables
$cadet_id = $_GET['id'] ?? null;
$cadet_details = [];

// Fetch cadet details if ID is provided
if ($cadet_id) {
    $stmt = $pdo->prepare("SELECT cadets.*, provinces.name AS province_name, districts.name AS district_name 
                            FROM cadets 
                            LEFT JOIN provinces ON cadets.province_id = provinces.id 
                            LEFT JOIN districts ON cadets.district_id = districts.id 
                            WHERE cadets.id = ?");
    $stmt->execute([$cadet_id]);
    $cadet_details = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$cadet_details) {
    header("Location: view_cadet.php");
    exit;
}

// Handle delete request
if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM cadets WHERE id = ?");
    $stmt->execute([$cadet_id]);
    header("Location: http://localhost/NCCAA_CDMS/dashboard/dashboard.php"); // Redirect after deletion
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadet Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #eef2f3;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 8px;
            background: #f9fafb;
            border-left: 4px solid #4B8A3B;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 1.4rem;
            color: #4B8A3B;
        }
        .detail {
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 1.1rem;
        }
        .button-group {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem; 
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            font-weight: 500;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .btn-export { background-color: #28a745; }
        .btn-edit { background-color: #007bff; }
        .btn-delete { background-color: #dc3545; }
        .btn-back { background-color: #6c757d; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center text-3xl font-semibold text-gray-800 mb-6">Cadet Details</h2>

        <div class="section">
            <div class="section-title">Personal Information</div>
            <div class="detail"><strong>Name:</strong> <span><?php echo htmlspecialchars($cadet_details['name']); ?></span></div>
            <div class="detail"><strong>Date of Birth:</strong> <span><?php echo htmlspecialchars($cadet_details['date_of_birth']); ?></span></div>
            <div class="detail"><strong>Gender:</strong> <span><?php echo htmlspecialchars($cadet_details['gender']); ?></span></div>
            <div class="detail"><strong>Blood Group:</strong> <span><?php echo htmlspecialchars($cadet_details['blood_group']); ?></span></div>
            <div class="detail"><strong>Contact Number:</strong> <span><?php echo htmlspecialchars($cadet_details['contact_number']); ?></span></div>
            <div class="detail"><strong>Email:</strong> <span><a href="mailto:<?php echo htmlspecialchars($cadet_details['email']); ?>" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($cadet_details['email']); ?></a></span></div>
            <div class="detail"><strong>Facebook Link:</strong> <span><a href="<?php echo htmlspecialchars($cadet_details['facebook_link']); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($cadet_details['facebook_link']); ?></a></span></div>
        </div>

        <div class="section">
            <div class="section-title">Family Information</div>
            <div class="detail"><strong>Father's Name:</strong> <span><?php echo htmlspecialchars($cadet_details['father_name']); ?></span></div>
            <div class="detail"><strong>Mother's Name:</strong> <span><?php echo htmlspecialchars($cadet_details['mother_name']); ?></span></div>
            <div class="detail"><strong>Father's Contact:</strong> <span><?php echo htmlspecialchars($cadet_details['father_contact']); ?></span></div>
            <div class="detail"><strong>Mother's Contact:</strong> <span><?php echo htmlspecialchars($cadet_details['mother_contact']); ?></span></div>
        </div>

        <div class="section">
            <div class="section-title">Address Information</div>
            <div class="detail"><strong>Permanent Address:</strong> <span><?php echo htmlspecialchars($cadet_details['permanent_address']); ?></span></div>
            <div class="detail"><strong>Temporary Address:</strong> <span><?php echo htmlspecialchars($cadet_details['temporary_address']); ?></span></div>
        </div>

        <div class="section">
            <div class="section-title">NCC Information</div>
            <div class="detail"><strong>Education Qualification:</strong> <span><?php echo htmlspecialchars($cadet_details['education_qualification']); ?></span></div>
            <div class="detail"><strong>School Name:</strong> <span><?php echo htmlspecialchars($cadet_details['see_school']); ?></span></div>
            <div class="detail"><strong>NCC Cadet Number:</strong> <span><?php echo htmlspecialchars($cadet_details['ncc_cadet_number']); ?></span></div>
            <div class="detail"><strong>Rank:</strong> <span><?php echo htmlspecialchars($cadet_details['rank']); ?></span></div>
            <div class="detail"><strong>Division:</strong> <span><?php echo htmlspecialchars($cadet_details['division']); ?></span></div>
            <div class="detail"><strong>NCC Batch:</strong> <span><?php echo htmlspecialchars($cadet_details['ncc_batch']); ?></span></div>
            <div class="detail"><strong>NCC Year:</strong> <span><?php echo htmlspecialchars($cadet_details['ncc_year']); ?></span></div>
            <div class="detail"><strong>NCC School:</strong> <span><?php echo htmlspecialchars($cadet_details['ncc_school']); ?></span></div>
        </div>

        <div class="section">
            <div class="section-title">Status Information</div>
            <div class="detail"><strong>Active Status:</strong> <span><?php echo htmlspecialchars($cadet_details['active_status']); ?></span></div>
            <div class="detail"><strong>District:</strong> <span><?php echo htmlspecialchars($cadet_details['district_name']); ?></span></div>
            <div class="detail"><strong>Province:</strong> <span><?php echo htmlspecialchars($cadet_details['province_name']); ?></span></div>
        </div>

        <div class="button-group">
            <a href="http://localhost/NCCAA_CDMS/cadets/edit_cadet.php?id=<?php echo $cadet_id; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
            <form action="" method="post" style="display: inline;">
                <button type="submit" name="delete" class="btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
            </form>
            <a href="http://localhost/NCCAA_CDMS/dashboard/dashboard.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back</a>
        
        </div>
    </div>
    
</body>
</html>
