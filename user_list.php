<?php
// Include authentication check and config for DB connection
include('auth.php');
include_once('config.php'); // Use the PDO connection from here

// Check if the form is submitted to create a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];
    $last_purchase_id = $_POST['last_purchase_id'] ?? NULL; // Optional

    // Prepare SQL query to insert the new user into the database
    $sql = "INSERT INTO users (username, email, password, role, last_purchase_id) 
            VALUES (:username, :email, :password, :role, :last_purchase_id)";
    
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':last_purchase_id', $last_purchase_id);

    try {
        $stmt->execute();
        $message = "New user created successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch users and admins from the database
$sql_users = "SELECT * FROM users WHERE role = 'user'";
$stmt_users = $con->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

$sql_admins = "SELECT * FROM users WHERE role = 'admin'";
$stmt_admins = $con->prepare($sql_admins);
$stmt_admins->execute();
$admins = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        // Toggle form visibility
        function toggleForm() {
            const form = document.getElementById('create-user-form');
            const table = document.getElementById('users-table');
            const toggleButton = document.getElementById('toggle-button');
            
            // Toggle visibility
            form.classList.toggle('hidden');
            table.classList.toggle('hidden');
            
            // Change button text based on form visibility
            if (form.classList.contains('hidden')) {
                toggleButton.textContent = 'Create New User';
            } else {
                toggleButton.textContent = 'Show Users Table';
            }
        }
    </script>
</head>
<body>
    <div class="flex w-full relative h-[100vh]">
        <div class="w-[300px] h-full bg-white shadow-md flex-shrink-0">
            <?php include('includes/sidebar.php'); ?>
        </div>
        <div class="w-[calc(100%-300px)] h-full overflow-y-auto">
            <div class="h-[60px] px-4 w-full border-b-[1px] border-gray-200 flex items-center justify-between">
                <h3>Product Management</h3>
                <div class="flex items-center justify-center gap-3">
                    <button id="toggle-button" onclick="toggleForm()" class="px-4 text-[.8rem] py-2 bg-blue-500 text-white rounded mb-4">
                        Create New User
                    </button>
                    <?php include('user.php') ?>
                </div>
            </div>
            <div class="h-[calc(100vh-60px)] w-full p-4">
                <h3 class="mb-4 text-lg font-semibold">User Management</h3>
                <?php if (isset($message)) { ?>
                    <p class="text-green-500"><?php echo $message; ?></p>
                <?php } ?>
                
                <!-- Create User Form -->
                <div id="create-user-form" class="space-y-4 hidden">
                    <form method="POST">
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium">Username</label>
                            <input type="text" name="username" id="username" class="mt-1 block w-full border border-gray-300 p-2" required />
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border border-gray-300 p-2" required />
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium">Password</label>
                            <input type="password" name="password" id="password" class="mt-1 block w-full border border-gray-300 p-2" required />
                        </div>
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium">Role</label>
                            <select name="role" id="role" class="mt-1 block w-full border border-gray-300 p-2">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="last_purchase_id" class="block text-sm font-medium">Last Purchase ID (Optional)</label>
                            <input type="number" name="last_purchase_id" id="last_purchase_id" class="mt-1 block w-full border border-gray-300 p-2" />
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Create User</button>
                    </form>
                </div>

                <!-- Display Users Table -->
                <div id="users-table">
                    <h3 class="mt-8 mb-4 text-lg font-semibold">Users List</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Username</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                    <th class="px-4 py-2 text-left">Last Purchase ID</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $row) { ?>
                                    <tr>
                                        <td class="px-4 py-2"><?php echo $row['id']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['username']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['email']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['role']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['last_purchase_id']; ?></td>
                                        <td class="px-4 py-2">
                                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="text-blue-500">Edit</a> |
                                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="text-red-500">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Display Admins Table -->
                <h3 class="mt-8 mb-4 text-lg font-semibold">Admins List</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Username</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Role</th>
                                <th class="px-4 py-2 text-left">Last Purchase ID</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $row) { ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $row['id']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['username']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['email']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['role']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['last_purchase_id']; ?></td>
                                    <td class="px-4 py-2">
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="text-blue-500">Edit</a> |
                                        <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="text-red-500">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$con = null;
?>
