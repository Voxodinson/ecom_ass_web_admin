<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User & Admin Management</title>
    <link href="src/output.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/bot.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="flex w-full relative h-[100vh]">
        <div class="w-[300px] h-full bg-white flex-shrink-0">
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
            <div class="h-[calc(100vh-60px)] w-full p-3">
            </div>
        </div>
    </div>
</body>
</html>
