<div class="relative">
    <button id="dropdownButton" class="px-4 py-2 rounded-full bg-gray-200 w-fit flex items-center">
        <h1 class="text-[.8rem] font-normal">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
            <span class="">
                <i class="fa-solid fa-caret-down"></i>
            </span>
        </h1>
    </button>
    <div id="dropdownMenu" class="absolute right-0 mt-2 w-full bg-white border border-gray-400 shadow-md rounded-md hidden">
        <ul class="py-2 text-sm text-gray-700">
            <li>
                <a href="logout.php" class="px-4 py-2">Logout</a>
            </li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dropdownButton = document.getElementById("dropdownButton");
        const dropdownMenu = document.getElementById("dropdownMenu");

        dropdownButton.addEventListener("click", function () {
            dropdownMenu.classList.toggle("hidden");
        });

        document.addEventListener("click", function (event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add("hidden");
            }
        });
    });
</script>
