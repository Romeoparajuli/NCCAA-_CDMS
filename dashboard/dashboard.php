<?php
// Start the session to manage user login sessions
session_start();

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit; // Always use exit after header redirects to stop further script execution
}

// User is logged in; you can proceed with your logic
$user_id = $_SESSION['user_id'];

// Include the database connection file
require '../includes/db_connection.php';

// Initialize variables
$new_reports = 0;  
$total_cadets = 0; 
$notices = 0;      
$notifications = 0;

// Fetch new reports count
if ($pdo) {
    $report_query = "SELECT COUNT(*) as count FROM reports"; // Assuming is_new marks a report as new
    $stmt = $pdo->prepare($report_query);
    $stmt->execute();
    $new_reports = $stmt->fetchColumn();

    // Fetch total cadets count
    $cadet_query = "SELECT COUNT(*) as count FROM cadets"; // Adjust the table name based on your actual database structure
    $stmt = $pdo->prepare($cadet_query);
    $stmt->execute();
    $total_cadets = $stmt->fetchColumn();

    // Fetch notices count
    $notices_query = "SELECT COUNT(*) as count FROM notices"; // Adjust the table name based on your actual database structure
    $stmt = $pdo->prepare($notices_query);
    $stmt->execute();
    $notices = $stmt->fetchColumn();

    // Fetch notifications count
    $notifications_query = "SELECT COUNT(*) as count FROM notifications"; // Adjust the condition as necessary
    $stmt = $pdo->prepare($notifications_query);
   // $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchColumn();
}
// Set the greeting message based on the current time of day
$currentHour = date('H'); // Get the current hour in 24-hour format
if ($currentHour < 12) {
    $greeting = "Good morning";
} elseif ($currentHour < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Retrieve the username from the session, or set a default if not available
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; 

// Create the welcome message combining the greeting and username
$welcomeMessage = "$greeting, " . htmlspecialchars($username) . "! Welcome to the NCCAA Lumbini CDMS.";



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NCCAA Lumbini</title>
    
    <!-- Include Tailwind CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <!-- Custom styles for the notification popup and sidebar -->
    <style>
        /* Notification popup styles */
        .notification-popup {
            position: fixed;
            bottom: 20px; 
            right: 20px;
            background-color: #38a169; 
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }

        .notification-popup .close-btn {
            cursor: pointer;
            margin-left: 10px;
        }

        /* Sidebar icon styles */
        .sidebar-icon {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar Section -->
        <div class="w-64 bg-green-600 text-white min-h-screen flex flex-col transition-all duration-300" id="sidebar">
            <div class="flex items-center justify-between h-16">
                <!-- Sidebar toggle button -->
                <button id="toggle-sidebar" class="ml-2 text-white focus:outline-none">
                    <i class="fas fa-bars" id="sidebar-toggle-icon"></i>
                </button>
            </div>
            <!-- Navigation links within the sidebar -->
            <nav class="flex-grow">
                <ul class="flex flex-col">
                    <li><a href="javascript:void(0);" onclick="loadContent('dashboard.php');" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-tachometer-alt sidebar-icon"></i> <span class="sidebar-text">Dashboard</span></a></li>
                    <li><a href="javascript:void(0);" onclick="loadContent('reports.php');" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-file-alt sidebar-icon"></i> <span class="sidebar-text">Reports</span></a></li>
                    <li><a href="javascript:void(0);" onclick="loadNotices();" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-bullhorn sidebar-icon"></i> <span class="sidebar-text">Notices</span></a></li>

                    <li><a href="javascript:void(0);" onclick="loadContent('notifications.php');" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-bell sidebar-icon"></i> <span class="sidebar-text">Notifications</span></a></li>
                    <a href="../agencies/agencies.php" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-building sidebar-icon"></i><span class="sidebar-text">Agencies</span></a>
                    <li><a href="javascript:void(0);" onclick="loadContent('../cadets/view_Cadet.php');" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-user-graduate sidebar-icon"></i> <span class="sidebar-text">Cadets</span></a></li>

                    <!-- Logout link -->
                    <li><a href="../index.php" onclick="return confirm('Are you sure you want to log out?');" class="block p-4 hover:bg-green-700 flex items-center"><i class="fas fa-sign-out-alt sidebar-icon"></i><span class="sidebar-text">Logout</span></a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Section -->
        <div class="flex-grow p-6">
            <!-- Header Section with Logo and Organizational Information -->
            <header class="flex items-center justify-between mb-6">
                <img src="../images/logo.png" alt="NCCAA Logo" class="w-32 h-32 mr-4">
                <div class="flex-grow text-center">
                    <h1 class="text-3xl font-semibold">NCC Alumni Association Nepal</h1>
                    <p class="text-xl mt-1">Lumbini Province</p>
                </div>
                <!-- Date, Time, and Language Changer -->
                <div class="text-right">
                    <div id="current-date-time" class="text-lg"></div>
                   
                </div>
            </header>

        <!-- Search Bar with Action Icons -->


<!-- Display Results -->
<div id="searchResults" class="mt-4 px-4"></div>


<!-- Display search results -->
<div id="searchResults" class="mt-4 px-4"></div>

            <!-- Content Placeholder for Dynamic Page Loading -->
            <div class="flex-grow p-6" id="content-placeholder">
                <h2 class="text-2xl font-semibold">Welcome</h2>    
                <p>Please select an option from the sidebar to get started.</p>
            </div>   

            <!-- Dashboard Statistics Cards -->
            <div id="dashboard-cards">
                <?php include 'dashboard_cards.php'; ?>
            </div>
        </div>
    </div>

    <!-- Notification Popup Section -->
    <div id="notification-popup" class="notification-popup" style="display: none;">
        <span><?php echo $welcomeMessage; ?></span>
        <span class="close-btn" onclick="closeNotification()">&times;</span>
    </div>

    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        toggleButton.addEventListener('click', () => {
            // Toggle the sidebar width between expanded and collapsed states
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                document.querySelectorAll('.sidebar-text').forEach(text => {
                    text.style.display = 'none';
                });
            } else {
                sidebar.classList.add('w-64');
                sidebar.classList.remove('w-20');
                document.querySelectorAll('.sidebar-text').forEach(text => {
                    text.style.display = 'block';
                });
            }
        });

        function loadContent(page) {
    if (page === 'dashboard.php') {
        document.getElementById('content-placeholder').innerHTML = '<h2 class="text-2xl font-semibold">Welcome</h2><p>Please select an option from the sidebar to get started.</p>';
        loadDashboardCards();
    } else {
        fetch(page) // Fetch the specified page content
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.getElementById('content-placeholder').innerHTML = data;
                document.getElementById('dashboard-cards').style.display = 'none'; // Hide dashboard cards if not loading dashboard
            })
            .catch(error => console.error('Error loading content:', error));
    }
}

    
// // Show notification popup on load
//     document.getElementById('notification-popup').style.display = 'flex';
//         setTimeout(() => {
//     document.getElementById('notification-popup').style.opacity = '1';
       // }, 10);
    

        // Function to close the notification popup
    function closeNotification() {
            document.getElementById('notification-popup').style.display = 'none';
        }

        // Display the notification popup when the page loads
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('notification-popup').style.display = 'block';
            setTimeout(closeNotification, 5000); // Auto-close after 5 seconds
        });

        // Display the current date and time
    function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
            document.getElementById('current-date-time').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute

        function loadContent(page) {
        if (page === 'dashboard.php') {
        document.getElementById('content-placeholder').innerHTML = '<h2 class="text-2xl font-semibold">Welcome</h2><p>Please select an option from the sidebar to get started.</p>';
        loadDashboardCards(); // Show dashboard cards only on the dashboard page
        } else {
        // Clear any existing dashboard cards
        document.getElementById('dashboard-cards').style.display = 'none'; 
        
        // Fetch the content from the specified page
        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.getElementById('content-placeholder').innerHTML = data;
            })
            .catch(error => console.error('Error loading content:', error));
    }
}


                // Function to load dashboard cards
    function loadDashboardCards() {
            document.getElementById('dashboard-cards').style.display = 'block'; // Show dashboard cards
        }


            // Define the function to load the create report page
    function loadCreateReport() {
        // Load the create_report.php page into the content placeholder
        fetch('create_report.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.getElementById('content-placeholder').innerHTML = data;
                document.getElementById('dashboard-cards').style.display = 'none'; // Hide dashboard cards
            })
            .catch(error => console.error('Error loading create report content:', error));
    }

    function addGuest() {
            const index = document.querySelectorAll('.guest-template').length; // Get current index
            const template = document.querySelector('.guest-template').cloneNode(true);
            template.querySelectorAll('input, select').forEach(input => {
                input.value = '';
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`); // Corrected
                    // Update name attribute
                }
            });
            document.getElementById('guests-section').appendChild(template);
        }


    // Function to remove a guest or participant section
    function removeElement(element) {
            element.closest('.flex').remove();
        }
    
           // Function to update rank options based on service selection
    function updateRank(selectElement) {
            const service = selectElement.value;
            const rankSelect = selectElement.closest('.guest-template').querySelector('select[name*="[rank]"]');
            rankSelect.innerHTML = ''; // Clear existing options
            rankSelect.innerHTML += '<option value="">Select Rank</option>';

            if (service === "police") {
                rankSelect.innerHTML += '<option value="Deputy Inspector General (DIG)">Deputy Inspector General (DIG)</option>';
                rankSelect.innerHTML += '<option value="Senior Superintendent of Police (SSP)">Senior Superintendent of Police (SSP)</option>';
                rankSelect.innerHTML += '<option value="Superintendent of Police (SP)">Superintendent of Police (SP)</option>';
                rankSelect.innerHTML += '<option value="Deputy Superintendent of Police (DSP)">Deputy Superintendent of Police (DSP)</option>';
                rankSelect.innerHTML += '<option value="Inspector">Inspector</option>';
                rankSelect.innerHTML += '<option value="Assistant Sub-Inspector">Assistant Sub-Inspector</option>';
                rankSelect.innerHTML += '<option value="Sub-Inspector">Sub-Inspector</option>';
                rankSelect.innerHTML += '<option value="Constable">Constable</option>';
                
            } else if (service === "nccaa") {
                rankSelect.innerHTML += '<option value="Central Director">Central Director</option>';
                rankSelect.innerHTML += '<option value="Province Director">Province Director</option>';
                rankSelect.innerHTML += '<option value="District Director">District Director</option>';
                rankSelect.innerHTML += '<option value="Central Co-Director">Central Co-Director</option>';
                rankSelect.innerHTML += '<option value="Province Co-Director">Province Co-Director</option>';
                rankSelect.innerHTML += '<option value="District Co-Director">District Co-Director</option>';
                rankSelect.innerHTML += '<option value="District Member">District Member</option>';
                rankSelect.innerHTML += '<option value="Province Member">Province Member</option>';
                rankSelect.innerHTML += '<option value="Central Member">Central Member</option>';
                
            } else if (service === "army") {
                rankSelect.innerHTML += '<option value="Chief of Army Staff (COAS)">Chief of Army Staff (COAS)</option>';
                rankSelect.innerHTML += '<option value="General">General</option>';
                rankSelect.innerHTML += '<option value="Lieutenant General">Lieutenant General</option>';
                rankSelect.innerHTML += '<option value="Major General">Major General</option>';
                rankSelect.innerHTML += '<option value="Brigadier General">Brigadier General</option>';
                rankSelect.innerHTML += '<option value="Colonel">Colonel</option>';
                rankSelect.innerHTML += '<option value="Lieutenant Colonel">Lieutenant Colonel</option>';
                rankSelect.innerHTML += '<option value="Major">Major</option>';
                rankSelect.innerHTML += '<option value="Captain">Captain</option>';
                rankSelect.innerHTML += '<option value="Lieutenant">Lieutenant</option>';
                rankSelect.innerHTML += '<option value="Second Lieutenant">Second Lieutenant</option>';
            }
    }

    // Function to dynamically add a new participant section
    function addParticipant() {
    const index = document.querySelectorAll('.participant-template').length; // Get current index
    const template = document.querySelector('.participant-template').cloneNode(true);
    template.querySelectorAll('input, select').forEach(input => {
        input.value = '';
        if (input.name) {
            input.name = input.name.replace(/\[\d+\]/, `[${index}]`); // Corrected: Use backticks for template literals
        }
    });
    document.getElementById('participants-section').appendChild(template);
    // Add event listener for the new input field
    template.querySelector('input[name^="participants["][type="number"]').addEventListener('input', updateTotalParticipants);
    updateTotalParticipants(); // Update total participants
    }


     // Function to remove a guest or participant section
    function removeElement(element) {
        element.closest('.flex').remove();
        updateTotalParticipants(); // Update total participants
    }
     // Update total participants function
    function updateTotalParticipants() {
        const participantCount = Array.from(document.querySelectorAll('input[name^="participants["][type="number"]'))
            .reduce((total, input) => total + (parseInt(input.value) || 0), 0);
        
        const guestCount = Array.from(document.querySelectorAll('input[name^="guests["][type="text"]'))
            .filter(input => input.value.trim() !== '').length;

        const total = participantCount + guestCount;
        document.getElementById('total-participants').textContent = total;
    }


    

    document.getElementById('report-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent page reload

            const formData = new FormData(this);
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('popup-message').innerText = data.success;
                    document.getElementById('popup-message').classList.remove('error-popup');
                    document.getElementById('popup-message').classList.add('success-popup');
                    document.getElementById('popup-message').style.display = 'flex';
                    this.reset(); // Clear the form fields
                } else {
                    document.getElementById('popup-message').innerText = data.error;
                    document.getElementById('popup-message').classList.remove('success-popup');
                    document.getElementById('popup-message').classList.add('error-popup');
                    document.getElementById('popup-message').style.display = 'flex';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    

     // Handle filter button click event
    const filterButton = document.getElementById('filter-button');
            if (filterButton) {
                filterButton.addEventListener('click', function () {
                    const filterInput = document.getElementById('filter-input').value.trim();
                    filterReports(filterInput);
                });
            }

            // Function to filter reports based on input
    function filterReports(filterValue) {
        const reports = document.querySelectorAll('.report-card');
        reports.forEach(report => {
                    if (report.textContent.toLowerCase().includes(filterValue.toLowerCase())) {
                        report.classList.remove('hidden');
                    } else {
                        report.classList.add('hidden');
                    }
                });
    }
    document.addEventListener("DOMContentLoaded", function() {
            const filterButton = document.getElementById('filter-button');
            if (filterButton) {
                filterButton.addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent default form submission
                    const filterInput = document.getElementById('filter-input').value.trim().toLowerCase();
                    const districtSelect = document.getElementById('district-select').value;
                    filterReports(filterInput, districtSelect);
                });
            }
        });

    // Function to filter reports based on input
    function filterReports(dateValue, districtValue) {
            const rows = document.querySelectorAll('.report-row');
            rows.forEach(row => {
                const date = row.cells[1].textContent.trim();
                const district = row.cells[2].textContent.trim();
                const matchDate = !dateValue || date.includes(dateValue);
                const matchDistrict = !districtValue || district === districtValue;
                
                // Show or hide the row based on the filter
                if (matchDate && matchDistrict) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }   
// Ensure this script is included at the bottom of your body or wrapped in DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
    // Adding click event listener to the Cadet menu
    const cadetMenu = document.querySelector('a[onclick="loadContent(\'./cadets/view_Cadet.php\');"]');
    
    if (cadetMenu) {
        cadetMenu.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default anchor behavior
            loadContent('./cadets/view_Cadet.php'); // Load the cadet view
        });

    }
    });

    function loadNotices() {
    // Perform an AJAX request to fetch the view_notice.php content
    fetch('../notices/notice.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('content-placeholder').innerHTML = data;
        })
        .catch(error => console.error('Error loading notices:', error));
}

function loadCreateNotice() {
            fetch('../notices/create_notice.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-placeholder').innerHTML = data;
                })
                .catch(error => console.error('Error loading create notice:', error));
        }

        function loadViewNotice(id) {
            fetch(`../notices/view_notice.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-placeholder').innerHTML = data;
                })
                .catch(error => console.error('Error loading view notice:', error));
        }
    
    // In your dashboard JavaScript, you can disable the back button
window.history.pushState(null, document.title, window.location.href);
window.addEventListener('popstate', function(event) {
    window.history.pushState(null, document.title, window.location.href);
});


    // Wait for the DOM to be fully loaded before executing the JavaScript code
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchResultsContainer = document.getElementById('searchResults');

        if (searchInput !== null) {
            let debounceTimeout = null;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length > 2) {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => {
                        fetch('search.php?q=' + query)
                            .then(response => response.json())
                            .then(data => {
                                searchResultsContainer.innerHTML = '';  // Clear previous results
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.classList.add('p-2', 'border-b', 'border-gray-300', 'bg-white', 'rounded', 'mb-2');
                                        div.innerHTML = `<strong>${item.type}</strong>: ${item.name}`;
                                        searchResultsContainer.appendChild(div);
                                    });
                                } else {
                                    searchResultsContainer.innerHTML = '<p class="text-gray-600">No results found</p>';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }, 500);
                } else {
                    searchResultsContainer.innerHTML = '';
                }
            });
        } else {
            console.error('Error: searchInput element not found');
        }
    });
</script>
</body>
</html>
