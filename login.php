<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ecom_web_assignment";

$con = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT id, username, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/x-icon" href="assets/bot.webp">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-lg p-8 w-96">
        <h3 class="text-center text-2xl font-bold mb-4">Login</h3>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-2 rounded text-center mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-200" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-200" required>
            </div>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Login</button>
        </form>
    </div>
</body>
</html>
