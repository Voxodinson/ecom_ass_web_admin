<?php
// Include authentication check and config for DB connection
include('auth.php');
include_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];

    // Modify SQL query to remove last_purchase_id
    $sql = "INSERT INTO users (username, email, password, role) 
            VALUES (:username, :email, :password, :role)";
    
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);

    try {
        $stmt->execute();
        $message = "New $role created successfully!";
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
    <title>User & Admin Management</title>
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        // Toggle form visibility and hide/show tables
        function toggleForm(formId, tableIds, buttonId) {
            const form = document.getElementById(formId);
            const tables = tableIds.map(id => document.getElementById(id));
            const button = document.getElementById(buttonId);

            // Check if form is visible or hidden
            if (form.classList.contains('hidden')) {
                // If form is hidden, show it and hide the tables
                form.classList.remove('hidden');
                tables.forEach(table => table.classList.add('hidden'));

                // Change button text
                button.textContent = 'Show Tables';
            } else {
                // If form is visible, hide it and show the tables again
                form.classList.add('hidden');
                tables.forEach(table => table.classList.remove('hidden'));

                // Change button text
                button.textContent = 'Create New User/Admin';
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
                <h3>User & Admin Management</h3>
                
                <div class="flex gap-3 items-center justify-center">
                    <button id="toggle-form" data-role="User or Admin" onclick="toggleForm('create-form', ['users-table', 'admins-table'], 'toggle-form')" class="px-4 py-2 text-[.8rem] bg-blue-500 text-white rounded-full">
                        Create New User/Admin
                    </button>
                    <?php include('user.php') ?>
                </div>
            </div>
            <div class="h-[calc(100vh-60px)] w-full p-4">

                <div id="create-form" class="space-y-4 hidden">
                    <h3 class="mb-4 text-lg font-semibold">Create New User or Admin</h3>
                    <?php if (isset($message)) { ?>
                        <p class="text-green-500"><?php echo $message; ?></p>
                    <?php } ?>
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
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Create</button>
                    </form>
                </div>
                <div id="users-table" class="p-3 rounded-md bg-white shadow-md border-[1px] border-gray-200">
                    <h3 class="mb-4 text-lg font-semibold">Users List</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full table-auto rounded-md overflow-hidden">
                            <thead>
                                <tr class="bg-[#3674B5] text-white">
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Username</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $row) { ?>
                                    <tr class="hover:bg-gray-200">
                                        <td class="px-4 py-2"><?php echo $row['id']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['username']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['email']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['role']; ?></td>
                                        <td class="px-4 py-2">
                                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="text-blue-500"><i class="fa-solid text-[1.2rem] fa-edit"></i></a>
                                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="text-red-500 ml-3"><i class="fa-solid text-[1.2rem] fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Display Admins Table -->
                <div id="admins-table" class="p-3 mt-3 rounded-md bg-white shadow-md border-[1px] border-gray-200">
                    <h3 class=" mb-4 text-lg font-semibold">Admins List</h3>
                    <div class="overflow-x-auto ">
                        <table class="min-w-full table-auto rounded-md overflow-hidden">
                            <thead>
                                <tr class="bg-[#3674B5] text-white">
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Username</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $row) { ?>
                                    <tr class="hover:bg-gray-200">
                                        <td class="px-4 py-2"><?php echo $row['id']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['username']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['email']; ?></td>
                                        <td class="px-4 py-2"><?php echo $row['role']; ?></td>
                                        <td class="px-4 py-2">
                                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="text-blue-500"><i class="fa-solid text-[1.2rem] fa-edit"></i></a>
                                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="text-red-500 ml-3"><i class="fa-solid text-[1.2rem] fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn = null;
?>
