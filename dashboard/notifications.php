<?php
session_start(); // Start the session
require '../includes/db_connection.php';

// Ensure user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    // Handle the case where user_id is not set, e.g., redirect to login
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the PDO connection is established
if ($pdo) {
    $sql = "SELECT * FROM notifications n 
            JOIN notices nt ON n.notice_id = nt.id 
            WHERE n.user_id = :user_id AND n.is_read = 0";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);
    
    // Bind the parameter
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Fetch the results
    echo "<ul class='max-w-lg mx-auto bg-white shadow-lg rounded-lg overflow-hidden'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li class='flex items-center p-4 border-b last:border-b-0 hover:bg-gray-100 transition duration-200 ease-in-out cursor-pointer'>";
        echo "<div class='flex-shrink-0 mr-3'>";
        echo "<svg class='h-8 w-8 text-green-500' fill='currentColor' viewBox='0 0 20 20'><path d='M10 0a10 10 0 100 20 10 10 0 000-20zm1 15.59L9.59 14 8 15.59 12.59 20 16 16.59 14.41 15z' /></svg>"; // Example icon
        echo "</div>";
        echo "<div class='flex-grow'>";
        echo "<span class='text-gray-800 font-semibold text-lg'>New {$row['type']} notice</span>";
        echo "<p class='text-gray-600 text-base'>{$row['title']}</p>";
        echo "<span class='text-sm text-gray-500'>{$row['created_at']}</span>";
        echo "</div>";
        echo "<div class='flex-shrink-0 ml-3'>";
        echo "<span class='text-sm text-green-500 font-bold bg-green-100 px-2 py-1 rounded-full'>New</span>"; // Tag for new notifications
        echo "</div>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='text-red-500'>Database connection failed.</p>";
}
?>
