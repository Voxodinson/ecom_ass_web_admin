<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="w-full h-full relative overflow-y-auto border-r-[1px] border-gray-200">
        <div class="w-full h-[60px] flex items-center justify-center overflow-hidden bg-[#3674B5]">
            <img 
                src="assets/logo.png" 
                alt=""
                class="w-[180px] h-[180px]">
        </div>
        <ul class="*:w-full p-3 *:px-4 *:py-2 flex flex-col gap-2 *:rounded-md *:hover:bg-[#3674B5] *:hover:bg-opacity-30 *:transition-all *:ease-in-out *:duration-300 *:hover:text-white">
            <?php 
                $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            <li class="flex items-center gap-2 <?= $current_page == 'index.php' ? 'bg-[#3674B5] text-white' : '' ?>">
                <i class="fas fa-home text-[1.2rem] w-[30px]"></i>  
                <a href="index.php">Dashboard</a>
            </li>
            <li class="flex items-center gap-2 <?= $current_page == 'products_list.php' ? 'bg-[#3674B5] text-white' : '' ?>">
                <i class="fas fa-box text-[1.2rem] w-[30px]"></i>  
                <a href="products_list.php">Product Management</a>
            </li>
            <li class="flex items-center gap-2 <?= $current_page == 'user_list.php' ? 'bg-[#3674B5] text-white' : '' ?>">
                <i class="fas fa-user text-[1.2rem] w-[30px]"></i>  
                <a href="user_list.php">User Management</a>
            </li>
        </ul>


        <div class="w-full flex p-3  absolute bottom-0 left-0">
            <a 
                href="logout.php" class="w-full rounded-md px-4 py-3 bg-[#3674B5] text-white">
                <i class="fa-solid fa-right-from-bracket text-[1.2rem] w-[30px]"></i>  
                Logout
            </a>
        </div>
    </div>
</body>
</html>