<?php
session_start();
require '../includes/db_connection.php';  // Include your database connection file

// Initialize error messages
$usernameError = $passwordError = $loginError = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username)) {
        $usernameError = 'Username is required';
    }
    if (empty($password)) {
        $passwordError = 'Password is required';
    }

    // Proceed only if both fields are valid
    if (empty($usernameError) && empty($passwordError)) {
        // Prepare SQL query to check credentials
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($query); // Use $pdo instead of $conn

        if ($stmt) {
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Check if user exists
            if ($user) {
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Set session variables and redirect to dashboard
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['username'] = $user['name']; // Store username in session

                    header("Location: http://localhost/NCCAA_CDMS/dashboard/dashboard.php");
                    exit;
                } else {
                    $loginError = 'Incorrect password';
                }
            } else {
                $loginError = 'User not found';
            }
        } else {
            $loginError = 'Database query error.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCCAA Lumbini Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <img src="../images/logo.png" alt="NCCAA Lumbini Logo" class="mx-auto w-24 h-24 mb-4 rounded-full">
        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">NCCAA Lumbini CDMS</h2>
        
        <form action="" method="POST" class="space-y-4">
            <div class="flex flex-col">
                <label for="username" class="mb-2 text-gray-700">Username:</label>
                <input type="text" id="username" name="username" class="border border-gray-300 p-2 rounded focus:outline-none focus:border-green-500" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                <div class="text-red-500 text-sm mt-1"><?php echo $usernameError; ?></div>
            </div>
            <div class="flex flex-col relative">
                <label for="password" class="mb-2 text-gray-700">Password:</label>
                <input type="password" id="password" name="password" class="border border-gray-300 p-2 rounded focus:outline-none focus:border-green-500">
                <span id="password-toggle" class="absolute top-9 right-3 cursor-pointer text-gray-500"><i class="fa fa-eye"></i></span>
                <div class="text-red-500 text-sm mt-1"><?php echo $passwordError; ?></div>
            </div>
            <div class="text-red-500 text-sm"><?php echo $loginError; ?></div>
            <div class="flex justify-center">
                <button type="submit" class="bg-green-600 text-white py-2 px-6 rounded hover:bg-green-700 transition">Login</button>
            </div>
            <a href="forgot-password.php" class="block text-center text-green-600 mt-4">Forgot Password?</a>
        </form>
    </div>

    <script>
        // Password toggle functionality
        const passwordToggle = document.getElementById('password-toggle');
        const passwordInput = document.getElementById('password');

        passwordToggle.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.innerHTML = '<i class="fa fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                passwordToggle.innerHTML = '<i class="fa fa-eye"></i>';
            }
        });
    </script>
</body>
</html>
