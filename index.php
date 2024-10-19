<?php
// Include the database connection file
include 'includes/db_connection.php';

// Query to get the 5 most recent notices
$sql = "SELECT title, created_at FROM notices ORDER BY created_at DESC LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no notices, provide a default message
if (empty($notices)) {
    $notices[] = ['title' => 'No recent notices', 'created_at' => ''];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCCAA Lumbini - Introduction</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
        }

        .header-bg {
            background: linear-gradient(135deg, #6b8e23, #556b2f);
        }
        .btn-login {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-login:hover {
            background-color: #3e4e24;
            transform: translateY(-2px);
        }
        .fade-in {
            opacity: 0;
            animation: fadeIn 1s forwards;
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        .loading {
            display: none; /* Hidden by default */
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .marquee {
            width: 100%; /* Adjust as needed */
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            }

        .marquee-content {
        display: inline-block;
        animation: marquee 15s linear infinite; /* Adjust duration as needed */
                }

            @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
            }




        .card {
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img {
            width: 100%;
            height: 300px; /* Increased height for better visibility */
            object-fit: cover;
            border-radius: 8px;
        }
        .slider-img {
            display: none;
            border-radius: 8px;
            transition: opacity 1.5s ease; /* Set transition duration to 1.5 seconds */
        }
        .slider-img.active {
            display: block;
            opacity: 1;
        }
        .slider-img:not(.active) {
            opacity: 0;
        }
        footer {
            background-color: #556b2f; /* Main color theme */
            color: white;
            padding: 2rem 0;
        }
    </style>
</head>
<body class="fade-in">

    <!-- Header Section -->
    <header class="header-bg p-6 flex justify-between items-center text-white">
        <div class="flex-grow text-center">
            <h1 class="text-4xl font-bold">Welcome to NCCAA Lumbini</h1>
            <p class="mt-2 text-lg">Discipline is the foundation of national service</p>
        </div>
        <div class="relative">
            <button id="login-btn" onclick="goToLogin()" class="btn-login bg-green-700 px-6 py-2 rounded text-white">Login</button>
            <div id="loading" class="loading">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 100 8v4a8 8 0 01-8-8z"></path>
                </svg>
            </div>
        </div>
    </header>
    <!-- Marquee Section for Notices -->
    <div class="marquee overflow-hidden whitespace-nowrap">
        <span class="marquee-content">
            <?php foreach ($notices as $notice): ?>
                <span class="mr-8"><?php echo htmlspecialchars($notice['title']); ?> (<?php echo date('d M Y', strtotime($notice['created_at'])); ?>)</span>
            <?php endforeach; ?>
        </span>
    </div>

    <!-- Main Content -->
    <section class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Card: Introduction to NCC -->
            <div class="card bg-white shadow-md rounded-lg p-6 relative">
                <h2 class="text-3xl font-semibold text-green-800 mb-4">Introduction to NCC</h2>
                <p class="text-gray-700 mb-4">
                    The National Cadet Corps (NCC) is a premier youth organization that engages students in military-style training, leadership development, and social service...
                </p>
                <div class="image-slider">
                    <img src="images/ncctraining/image1.jpg" alt="NCC Training" class="card-img slider-img active">
                    <img src="images/ncctraining/image2.jpg" alt="NCC Training" class="card-img slider-img">
                    <img src="images/ncctraining/image3.jpg" alt="NCC Training" class="card-img slider-img">
                    <img src="images/ncctraining/image7.jpg" alt="NCC Training" class="card-img slider-img">
                    <img src="images/ncctraining/image8.jpg" alt="NCC Training" class="card-img slider-img">
                    <img src="images/ncctraining/image13.jpg" alt="NCC Training" class="card-img slider-img">
                    <img src="images/ncctraining/image14.jpg" alt="NCC Training" class="card-img slider-img">
                </div>
            </div>

            <!-- Card: Importance of NCC in Nepal -->
            <div class="card bg-white shadow-md rounded-lg p-6 relative">
                <h2 class="text-3xl font-semibold text-green-800 mb-4">Importance of NCC in Nepal</h2>
                <p class="text-gray-700 mb-4">
                    NCC plays a crucial role in molding the character of young people...
                </p>
                <div class="image-slider">
                    <img src="images/ncctraining/image4.jpg" alt="Importance of NCC" class="card-img slider-img active">
                    <img src="images/ncctraining/image5.jpg" alt="Importance of NCC" class="card-img slider-img">
                    <img src="images/ncctraining/image6.jpg" alt="Importance of NCC" class="card-img slider-img">
                    <img src="images/ncctraining/image9.jpg" alt="Importance of NCC" class="card-img slider-img">
                    <img src="images/ncctraining/image10.jpg" alt="Importance of NCC" class="card-img slider-img">
                    <img src="images/ncctraining/image11.jpg" alt="Importance of NCC" class="card-img slider-img">
                    <img src="images/ncctraining/image12.jpg" alt="Importance of NCC" class="card-img slider-img">
                </div>
            </div>
        </div>

        <!-- About NCCAA Section -->
        <div class="card bg-white shadow-md rounded-lg p-6 mt-8">
            <h2 class="text-3xl font-semibold text-green-800 mb-4">About NCCAA Lumbini</h2>
            <p class="text-gray-700 mb-4">
                NCCAA Lumbini is dedicated to fostering continued engagement among former NCC cadets...
            </p>
            <img src="images/nccaaActivity/imagemain.jpg" alt="About NCCAA" class="w-full h-56 object-cover rounded mt-4">
        </div>

        <!-- Additional Information Cards with Image Sliders -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
            <!-- Card: NCC Activities -->
            <div class="card bg-white shadow-md rounded-lg p-6 relative">
                <h3 class="text-2xl font-semibold text-green-800 mb-2">NCCAA Activities</h3>
                <p class="text-gray-700">
                    NCC cadets participate in various activities including drills, parades, adventure training...
                </p>
                <div class="image-slider">
                    <img src="images/nccaaActivity/image2.jpg" alt="NCC Activities" class="card-img slider-img active">
                    <img src="images/nccaaActivity/image3.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image4.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image1.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image11.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image13.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image12.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image26.jpg" alt="NCC Activities" class="card-img slider-img">
                    <img src="images/nccaaActivity/image27.jpg" alt="NCC Activities" class="card-img slider-img">
                </div>
            </div>

            <!-- Card: Leadership Development -->
            <div class="card bg-white shadow-md rounded-lg p-6 relative">
                <h3 class="text-2xl font-semibold text-green-800 mb-2">Leadership Development</h3>
                <p class="text-gray-700">
                    Leadership is a cornerstone of NCC training...
                </p>
                <div class="image-slider">
                    <img src="images/nccaaActivity/image5.jpg" alt="Leadership Development" class="card-img slider-img active">
                    <img src="images/nccaaActivity/image6.jpg" alt="Leadership Development" class="card-img slider-img">
                    <img src="images/nccaaActivity/image7.jpg" alt="Leadership Development" class="card-img slider-img">
                    <img src="images/nccaaActivity/image8.jpg" alt="Leadership Development" class="card-img slider-img">
                    <img src="images/nccaaActivity/image9.jpg" alt="Leadership Development" class="card-img slider-img">
                    <img src="images/nccaaActivity/image10.jpg" alt="Leadership Development" class="card-img slider-img">
                    <img src="images/nccaaActivity/image14.jpg" alt="Leadership Development" class="card-img slider-img">
                </div>
            </div>

            <!-- Card: Social Service -->
            <div class="card bg-white shadow-md rounded-lg p-6 relative">
                <h3 class="text-2xl font-semibold text-green-800 mb-2">Social Service</h3>
                <p class="text-gray-700">
                    NCC cadets actively engage in community service...
                </p>
                <div class="image-slider">
                    <img src="images/nccaaActivity/image15.jpg" alt="Social Service" class="card-img slider-img active">
                    <img src="images/nccaaActivity/image16.jpg" alt="Social Service" class="card-img slider-img">
                    <img src="images/nccaaActivity/image17.jpg" alt="Social Service" class="card-img slider-img">
                    <img src="images/nccaaActivity/image18.jpg" alt="Social Service" class="card-img slider-img">
                    <img src="images/nccaaActivity/image19.jpg" alt="Social Service" class="card-img slider-img">
                    <img src="images/nccaaActivity/image20.jpg" alt="Social Service" class="card-img slider-img">
                    <img src="images/nccaaActivity/image21.jpg" alt="Social Service" class="card-img slider-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="text-center mt-8">
        <div class="container mx-auto px-4">
            <p class="text-lg">&copy; 2024 NCCAA Lumbini. All Rights Reserved.</p>
            <p class="mt-2">Contact Us: <a href="mailto:nccaalumbini@gmail.com" class="underline">nccaalumbini@gmail.com</a> | WhatsApp: <a href="https://wa.me/9769307983" class="underline">9769307983</a></p>
            <p class="mt-2">Follow us on <a href="https://www.facebook.com/groups/572305610438125" class="underline">Facebook</a></p>
        </div>
    </footer>

    <script>
        function goToLogin() {
            const btn = document.getElementById('login-btn');
            const loading = document.getElementById('loading');

            // Show loading spinner
            btn.style.display = 'none';
            loading.style.display = 'inline-flex';

            // Fade out effect
            document.body.style.opacity = 0; // Fade out
            setTimeout(() => {
                window.location.href = './auth/login.php'; // Redirect to login page after fading out
            }, 1000); // 1 second delay for the fade
        }

        // Initialize image sliders
        document.querySelectorAll('.image-slider').forEach(slider => {
            const sliders = slider.querySelectorAll('.slider-img');
            let currentIndex = 0;

            function showNextImage() {
                sliders[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % sliders.length;
                sliders[currentIndex].classList.add('active');
            }

            setInterval(showNextImage, 3000); // Change image every 3 seconds
        });
    </script>
</body>
</html>
 