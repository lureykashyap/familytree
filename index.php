<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-8 text-center">Family Tree Application</h1>
        <div class="flex justify-center space-x-4">
            <a href="register.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Register
            </a>
            <a href="login.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Login
            </a>
        </div>
    </div>
</body>
</html>

