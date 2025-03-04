
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="assets/bot.webp">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php
        session_start();

        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "ecom_web_assignment";

        $con = mysqli_connect($host, $user, $pass, $dbname);
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $password = $_POST['password'];

            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($con, $query);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
        }

        if (isset($_GET['logout'])) {
            session_destroy();
            header("Location: index.php");
            exit();
        }
    ?>
    <div class="flex flex-col items-center justify-center min-h-screen text-center">
        <h1 class="text-3xl font-bold">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p class="text-lg mt-2">You are logged in as <strong class="font-semibold"><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>
        <a href="?logout=true" class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">Logout</a>
    </div>

</body>
</html>