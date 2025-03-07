<?php
include('auth.php');
include_once('config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing data
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "User not found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
        $role = $_POST['role']; // Ensure role is captured correctly from the form

        try {
            $sql = "UPDATE users SET username = :username, email = :email, password = :password, role = :role WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role); // Ensure the role is updated
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: user_list.php");
                exit();
            } else {
                echo "Error: Unable to update user/admin.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User/Admin</title>
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="assets/bot.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="flex w-full relative h-[100vh]">
        <div class="w-[300px] h-full bg-white shadow-md flex-shrink-0">
            <?php include('includes/sidebar.php'); ?>
        </div>
        <div class="w-[calc(100%-300px)] h-full overflow-y-auto">
            <div class="h-[60px] px-4 w-full border-b-[1px] border-gray-200 flex items-center justify-between">
                <h3>Edit User/Admin</h3>
            </div>
            <div class="h-[calc(100vh-60px)] w-full p-4">
                <h3 class="mb-4 text-lg font-semibold">Edit User/Admin</h3>

                <?php if (isset($message)) { ?>
                    <p class="text-green-500"><?php echo $message; ?></p>
                <?php } ?>

                <form method="POST" class="space-y-4">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium">Username</label>
                        <input type="text" name="username" id="username" class="mt-1 block w-full border border-gray-300 p-2" value="<?php echo $user['username']; ?>" required />
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full border border-gray-300 p-2" value="<?php echo $user['email']; ?>" required />
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium">Password (Leave empty to keep current)</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border border-gray-300 p-2" />
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium">Role</label>
                        <select name="role" id="role" class="mt-1 block w-full border border-gray-300 p-2">
                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
