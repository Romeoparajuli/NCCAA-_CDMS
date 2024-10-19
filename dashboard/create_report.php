
<?php
// Include the database connection file
require_once '../includes/db_connection.php'; // Adjust the path if necessary

// Start session for feedback messages
session_start();

// Check for success messages
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']); // Clear the message after it's read
} else {
    $successMessage = '';
}

// Check for error messages
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']); // Clear the message after it's read
} else {
    $errorMessage = '';
}

// Fetch districts from the database
$stmt = $pdo->prepare("SELECT id, name FROM districts"); // Adjust the table name and fields as necessary
$stmt->execute();
$districts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect and sanitize input data
        $programName = htmlspecialchars($_POST['program_name']);
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $address = htmlspecialchars($_POST['address']);
        $introduction = htmlspecialchars($_POST['introduction']);
        $objectives = htmlspecialchars($_POST['objectives']);
        $execution = htmlspecialchars($_POST['execution']);
        $achievement = htmlspecialchars($_POST['achievement']);
        $districtId = $_POST['id']; // Get the selected district ID

        // Handle guests and participants
        $participants = $_POST['participants']; // Array of participants
        $guests = $_POST['guests']; // Array of guests

        // Calculate total participants
        $totalParticipants = count($guests);
        foreach ($participants as $participant) {
            $totalParticipants += isset($participant['count']) ? intval($participant['count']) : 0;
        }

        // Prepare and execute the report insertion
        $stmt = $pdo->prepare("INSERT INTO reportstable (program_name, start_date, end_date, address, introduction, objectives, execution, achievement, total_participants, district_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); // Added district_id to the query
        $stmt->execute([$programName, $startDate, $endDate, $address, $introduction, $objectives, $execution, $achievement, $totalParticipants, $districtId]);
        $reportId = $pdo->lastInsertId(); // Get the ID of the newly created report

        // Insert participants
        foreach ($participants as $participant) {
            if (!empty($participant['type']) && !empty($participant['count'])) {
                $stmt = $pdo->prepare("INSERT INTO participants (report_id, type, count) VALUES (?, ?, ?)");
                $stmt->execute([$reportId, $participant['type'], intval($participant['count'])]);
            }
        }

        // Insert guests
        foreach ($guests as $guest) {
            if (!empty($guest['name'])) {
                $stmt = $pdo->prepare("INSERT INTO guests (report_id, name, service, rank) VALUES (?, ?, ?, ?)");
                $stmt->execute([$reportId, htmlspecialchars($guest['name']), $guest['service'], $guest['rank']]);
            }
        }

        // Redirect or provide success message
        $_SESSION['success'] = "Report created successfully!";
    } catch (Exception $e) {
        // Handle exceptions (log the error, show a message, etc.)
        error_log($e->getMessage());
        $_SESSION['error'] = "An error occurred while creating the report.";
    }

    // Redirect to prevent resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report - NCCAA Lumbini</title>
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .remove-button:hover {
            cursor: pointer;
            color: red;
        }

        .success-popup {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background: #38a169;
            color: white;
            padding: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-100 p-6">
    <h1 class="text-3xl font-bold mb-4">Create New Report</h1>

    <!-- Success message popup -->
    <div id="success-popup" class="success-popup" style="display: <?= !empty($successMessage) ? 'flex' : 'none' ?>;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m1 0a9 9 0 11-6-3.72" />
        </svg>
        <span><?= $successMessage ?></span>
        <span class="remove-button text-xl text-red-600" onclick="closePopup()">X</span>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md space-y-4">
        <!-- Program Details -->

    <!-- District Selection Dropdown -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Select District</label>
        <select name="district_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded">
            <option value="">Select a district</option>
            <?php foreach ($districts as $district): ?>
                <option value="<?= htmlspecialchars($district['id']) ?>"><?= htmlspecialchars($district['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Name of the Program/Project</label>
            <input type="text" name="program_name" required class="mt-1 block w-full p-2 border border-gray-300 rounded">
        </div>

        <div class="flex space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" required class="mt-1 block w-full p-2 border border-gray-300 rounded">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" required class="mt-1 block w-full p-2 border border-gray-300 rounded">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" name="address" required class="mt-1 block w-full p-2 border border-gray-300 rounded">
        </div>

        <!-- Guests Section -->
        <div id="guests-section">
            <h2 class="text-lg font-semibold mt-4">Guests</h2>
            <div class="guest-template flex space-x-4 items-center mb-2">
                <input type="text" name="guests[0][name]" placeholder="Guest Name" class="flex-1 p-2 border border-gray-300 rounded">
                <select name="guests[0][service]" class="p-2 border border-gray-300 rounded" onchange="updateRank(this)">
                    <option value="">Select Service</option>
                    <option value="army">Nepal Army</option>
                    <option value="nccaa">NCCAA</option>
                    <option value="police">Nepal Police</option>
                </select>
                <div class="rank-container flex-1">
                    <select name="guests[0][rank]" class="p-2 border border-gray-300 rounded">
                        <option value="">Select Rank</option>
                    </select>
                </div>
                <span class="remove-button text-xl text-red-600" onclick="removeElement(this)">X</span>
            </div>
            <button type="button" onclick="addGuest()" class="text-blue-500 hover:underline">Add More Guests</button>
        </div>

        <!-- Participants Section -->
        <div id="participants-section">
            <h2 class="text-lg font-semibold mt-4">Participants</h2>
            <div class="participant-template flex space-x-4 items-center mb-2">
                <select name="participants[0][type]" class="p-2 border border-gray-300 rounded">
                    <option value="">Select Participant Type</option>
                    <option value="cadets">NCCAA Cadets</option>
                    <option value="students">Students</option>
                    <option value="civilians">Civilians</option>
                    <option value="reporters">Reporters</option>
                    <option value="trainers">Trainers</option>
                </select>
                <input type="number" name="participants[0][count]" placeholder="Number" min="0" class="p-2 border border-gray-300 rounded">
                <span class="remove-button text-xl text-red-600" onclick="removeElement(this)">X</span>
            </div>
            <button type="button" onclick="addParticipant()" class="text-blue-500 hover:underline">Add More Participants</button>
        </div>

        <!-- Total Participants Display -->
        <p class="mt-4 font-bold">Total Participants: <span id="total-participants">0</span></p>

        <!-- Other Fields -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Introduction of the Program</label>
            <textarea name="introduction" required class="mt-1 block w-full p-2 border border-gray-300 rounded"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Objectives of the Program</label>
            <textarea name="objectives" required class="mt-1 block w-full p-2 border border-gray-300 rounded"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Explanation of How the Program Was Completed</label>
            <textarea name="execution" required class="mt-1 block w-full p-2 border border-gray-300 rounded"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Final Achievement of the Program</label>
            <textarea name="achievement" required class="mt-1 block w-full p-2 border border-gray-300 rounded"></textarea>
        </div>

        

        <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">Submit Report</button>
    </form>

    <script>

// function addGuest() {
//             const index = document.querySelectorAll('.guest-template').length; // Get current index
//             const template = document.querySelector('.guest-template').cloneNode(true);
//             template.querySelectorAll('input, select').forEach(input => {
//                 input.value = '';
//                 if (input.name) {
//                     input.name = input.name.replace(/\[\d+\]/, [${index}]); // Update name attribute
//                 }
//             });
//             document.getElementById('guests-section').appendChild(template);
//         }

//         // Function to dynamically add a new participant section
//         function addParticipant() {
//             const index = document.querySelectorAll('.participant-template').length; // Get current index
//             const template = document.querySelector('.participant-template').cloneNode(true);
//             template.querySelectorAll('input, select').forEach(input => {
//                 input.value = '';
//                 if (input.name) {
//                     input.name = input.name.replace(/\[\d+\]/, [${index}]); // Update name attribute
//                 }
//             });
//             document.getElementById('participants-section').appendChild(template);
//         }

//         // Function to remove a guest or participant section
//         function removeElement(element) {
//             element.closest('.flex').remove();
//         }

//         // Function to update rank options based on service selection
//         function updateRank(selectElement) {
//             const service = selectElement.value;
//             const rankSelect = selectElement.closest('.guest-template').querySelector('select[name*="[rank]"]');
//             rankSelect.innerHTML = ''; // Clear existing options
//             rankSelect.innerHTML += '<option value="">Select Rank</option>';

//             if (service === "police") {
//                 rankSelect.innerHTML += '<option value="Deputy Inspector General (DIG)">Deputy Inspector General (DIG)</option>';
//                 rankSelect.innerHTML += '<option value="Senior Superintendent of Police (SSP)">Senior Superintendent of Police (SSP)</option>';
//                 rankSelect.innerHTML += '<option value="Superintendent of Police (SP)">Superintendent of Police (SP)</option>';
//                 rankSelect.innerHTML += '<option value="Deputy Superintendent of Police (DSP)">Deputy Superintendent of Police (DSP)</option>';
//                 rankSelect.innerHTML += '<option value="Inspector">Inspector</option>';
//                 rankSelect.innerHTML += '<option value="Assistant Sub-Inspector">Assistant Sub-Inspector</option>';
//                 rankSelect.innerHTML += '<option value="Sub-Inspector">Sub-Inspector</option>';
//                 rankSelect.innerHTML += '<option value="Constable">Constable</option>';
                
//             } else if (service === "nccaa") {
//                 rankSelect.innerHTML += '<option value="Central Director">Central Director</option>';
//                 rankSelect.innerHTML += '<option value="Province Director">Province Director</option>';
//                 rankSelect.innerHTML += '<option value="District Director">District Director</option>';
//                 rankSelect.innerHTML += '<option value="Central Co-Director">Central Co-Director</option>';
//                 rankSelect.innerHTML += '<option value="Province Co-Director">Province Co-Director</option>';
//                 rankSelect.innerHTML += '<option value="District Co-Director">District Co-Director</option>';
//                 rankSelect.innerHTML += '<option value="District Member">District Member</option>';
//                 rankSelect.innerHTML += '<option value="Province Member">Province Member</option>';
//                 rankSelect.innerHTML += '<option value="Central Member">Central Member</option>';
                
//             } else if (service === "army") {
//                 rankSelect.innerHTML += '<option value="Chief of Army Staff (COAS)">Chief of Army Staff (COAS)</option>';
//                 rankSelect.innerHTML += '<option value="General">General</option>';
//                 rankSelect.innerHTML += '<option value="Lieutenant General">Lieutenant General</option>';
//                 rankSelect.innerHTML += '<option value="Major General">Major General</option>';
//                 rankSelect.innerHTML += '<option value="Brigadier General">Brigadier General</option>';
//                 rankSelect.innerHTML += '<option value="Colonel">Colonel</option>';
//                 rankSelect.innerHTML += '<option value="Lieutenant Colonel">Lieutenant Colonel</option>';
//                 rankSelect.innerHTML += '<option value="Major">Major</option>';
//                 rankSelect.innerHTML += '<option value="Captain">Captain</option>';
//                 rankSelect.innerHTML += '<option value="Lieutenant">Lieutenant</option>';
//                 rankSelect.innerHTML += '<option value="Second Lieutenant">Second Lieutenant</option>';
//             }
//         }

//            // Display success message if set in session
//         window.onload = function () {
//             <?php if (isset($_SESSION['success'])): ?>
//                 document.getElementById('success-popup').style.display = 'flex';
//                 setTimeout(() => {
//                     document.getElementById('success-popup').style.display = 'none';
//                     <?php unset($_SESSION['success']); ?>
//                 }, 3000);
//             <?php endif; ?>
//         };

        
//     // Function to dynamically add a new guest section
//     function addGuest() {
//         const index = document.querySelectorAll('.guest-template').length; // Get current index
//         const template = document.querySelector('.guest-template').cloneNode(true);
//         template.querySelectorAll('input, select').forEach(input => {
//             input.value = '';
//             if (input.name) {
//                 input.name = input.name.replace(/\[\d+\]/, [${index}]); // Update name attribute
//             }
//         });
//         document.getElementById('guests-section').appendChild(template);
//         updateTotalParticipants(); // Update total participants
//     }

//     // Function to dynamically add a new participant section
//     function addParticipant() {
//         const index = document.querySelectorAll('.participant-template').length; // Get current index
//         const template = document.querySelector('.participant-template').cloneNode(true);
//         template.querySelectorAll('input, select').forEach(input => {
//             input.value = '';
//             if (input.name) {
//                 input.name = input.name.replace(/\[\d+\]/, [${index}]); // Update name attribute
//             }
//         });
//         document.getElementById('participants-section').appendChild(template);
//         // Add event listener for the new input field
//         template.querySelector('input[name^="participants["][type="number"]').addEventListener('input', updateTotalParticipants);
//         updateTotalParticipants(); // Update total participants
//     }

//     // Function to remove a guest or participant section
//     function removeElement(element) {
//         element.closest('.flex').remove();
//         updateTotalParticipants(); // Update total participants
//     }

//     // Update total participants function
//     function updateTotalParticipants() {
//         const participantCount = Array.from(document.querySelectorAll('input[name^="participants["][type="number"]'))
//             .reduce((total, input) => total + (parseInt(input.value) || 0), 0);
        
//         const guestCount = Array.from(document.querySelectorAll('input[name^="guests["][type="text"]'))
//             .filter(input => input.value.trim() !== '').length;

//         const total = participantCount + guestCount;
//         document.getElementById('total-participants').textContent = total;
//     }

//     // Call this function when the page loads
//     document.addEventListener("DOMContentLoaded", function () {
//         // Initialize event listeners for existing participant count inputs
//         document.querySelectorAll('input[name^="participants["][type="number"]').forEach(input => {
//             input.addEventListener('input', updateTotalParticipants);
//         });
        
//         // Initialize the total participants count
//         updateTotalParticipants();
//     });

//     function closePopup() {
//             document.getElementById('success-popup').style.display = 'none';
//         }
        
//         document.getElementById('report-form').addEventListener('submit', function(event) {
//             event.preventDefault(); // Prevent page reload

//             const formData = new FormData(this);
//             fetch('', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     document.getElementById('popup-message').innerText = data.success;
//                     document.getElementById('popup-message').classList.remove('error-popup');
//                     document.getElementById('popup-message').classList.add('success-popup');
//                     document.getElementById('popup-message').style.display = 'flex';
//                     this.reset(); // Clear the form fields
//                 } else {
//                     document.getElementById('popup-message').innerText = data.error;
//                     document.getElementById('popup-message').classList.remove('success-popup');
//                     document.getElementById('popup-message').classList.add('error-popup');
//                     document.getElementById('popup-message').style.display = 'flex';
//                 }
//             })
//             .catch(error => console.error('Error:', error));
//         });
    
//         function closePopup() {
//             document.getElementById('success-popup').style.display = 'none';
//         }
    </script>