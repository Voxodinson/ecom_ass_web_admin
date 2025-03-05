<div class="w-full h-full relative overflow-y-auto border-r-[1px] border-gray-200">
    <div class="w-full h-[60px] flex items-center justify-center overflow-hidden bg-[#3674B5]">
        <img 
            src="assets/logo.png" 
            alt=""
            class="w-[180px] h-[180px]">
    </div>
    <ul class="*:w-full p-3 *:px-4 flex flex-col gap-2 *:rounded-md *:transition-all *:ease-in-out *:duration-300">
        <?php 
            $current_page = basename($_SERVER['PHP_SELF']);
        ?>
        <li class="flex items-center gap-2 hover:bg-[#3674B5] hover:text-white hover:bg-opacity-30 <?= $current_page == 'index.php' ? 'bg-[#3674B5] text-white' : '' ?>">
            <i class="fas fa-home text-[1.2rem] w-[30px]"></i>  
            <a href="index.php" class="w-full py-2">Dashboard</a>
        </li>
        <li class="flex items-center gap-2 hover:bg-[#3674B5] hover:text-white hover:bg-opacity-30 <?= $current_page == 'products_list.php' ? 'bg-[#3674B5] text-white' : '' ?>">
            <i class="fas fa-box text-[1.2rem] w-[30px]"></i>  
            <a href="products_list.php" class="w-full py-2">Product Management</a>
        </li>
        <li class="flex items-center gap-2 hover:bg-[#3674B5] hover:text-white hover:bg-opacity-30 <?= $current_page == 'orders.php' ? 'bg-[#3674B5] text-white' : '' ?>">
            <i class="fa-solid fa-box-open text-[1.2rem] w-[30px]"></i>  
            <a href="orders.php" class="w-full py-2">Sale</a>
        </li>
        <li class="flex items-center gap-2 hover:bg-[#3674B5] hover:text-white hover:bg-opacity-30 <?= $current_page == 'user_list.php' ? 'bg-[#3674B5] text-white' : '' ?>">
            <i class="fas fa-user text-[1.2rem] w-[30px]"></i>  
            <a href="user_list.php" class="w-full py-2">User Management</a>
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