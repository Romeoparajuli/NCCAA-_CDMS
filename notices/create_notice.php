<?php
// Start the session
session_start();

//Ensure the session is populated correctly
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

require '../includes/db_connection.php'; // Make sure this file defines $pdo

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form inputs
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $priority = $_POST['priority'];
    $posted_by = $_SESSION['user_id']; // Get logged-in user's ID

    try {
        // Insert the notice into the database
        $sql = "INSERT INTO notices (title, description, type, priority, posted_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql); // Using PDO instead of mysqli
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $type);
        $stmt->bindParam(4, $priority);
        $stmt->bindParam(5, $posted_by);

        if ($stmt->execute()) {
            // Get the last inserted notice ID
            $notice_id = $pdo->lastInsertId();

            // Send notifications to all users
            $sql = "INSERT INTO notifications (user_id, notice_id) SELECT id, :notice_id FROM users";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':notice_id', $notice_id);
            $stmt->execute();

            // Redirect back to dashboard with success message
            header("Location: dashboard.php?notice_created=1");
            exit();
        } else {
            // Handle database errors
            echo "Error executing the query.";
        }
    } catch (PDOException $e) {
        // Handle exception
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Notice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add smooth transitions for form elements */
        .transition-effect {
            transition: all 0.3s ease-in-out;
        }

        /* Focus effect for inputs */
        .input-effect:focus {
            outline: none;
            border-color: #34D399;
            box-shadow: 0 0 8px rgba(52, 211, 153, 0.4);
        }

        /* Glowing button effect */
        .glow-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            box-shadow: 0 4px 14px rgba(67, 56, 202, 0.4);
        }

        .glow-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 56, 202, 0.6);
        }

        /* Back button with subtle glow */
        .back-button {
            background: #E5E7EB;
            color: #1F2937;
            border-radius: 12px;
            padding: 12px 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .back-button:hover {
            background: #D1D5DB;
            color: #111827;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="max-w-3xl mx-auto bg-white p-8 mt-16 rounded-lg shadow-lg transition-all duration-300">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">Create New Notice</h2>

        <form action="create_notice.php" method="POST" class="space-y-6">
            <!-- Title Input -->
            <div class="mb-4">
                <label for="title" class="block text-lg font-medium text-gray-700 mb-2">Title</label>
                <input type="text" id="title" name="title" class="w-full p-4 border border-gray-300 rounded-lg focus:ring focus:ring-green-300 input-effect transition-effect" placeholder="Enter notice title" required>
            </div>

            <!-- Description Input -->
            <div class="mb-4">
                <label for="description" class="block text-lg font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" class="w-full p-4 border border-gray-300 rounded-lg focus:ring focus:ring-green-300 input-effect transition-effect" rows="6" placeholder="Enter notice description" required></textarea>
            </div>

            <!-- Type and Priority Inputs (Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="type" class="block text-lg font-medium text-gray-700 mb-2">Type</label>
                    <select id="type" name="type" class="w-full p-4 border border-gray-300 rounded-lg focus:ring focus:ring-green-300 input-effect transition-effect">
                        <option value="Program">Program</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Report Submission">Report Submission</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="priority" class="block text-lg font-medium text-gray-700 mb-2">Priority</label>
                    <select id="priority" name="priority" class="w-full p-4 border border-gray-300 rounded-lg focus:ring focus:ring-green-300 input-effect transition-effect">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
            </div>

            <!-- Buttons Section -->
            <div class="flex justify-between items-center space-x-4">
                <!-- Publish Button with Icon -->
                <button type="submit" class="glow-button">
                    <i class="fas fa-bullhorn mr-2"></i> Publish Notice
                </button>

                <!-- Back to Dashboard Button -->
                <a href="dashboard.php" class="back-button">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</body>

</html>

