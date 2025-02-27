<?php
    include('auth.php');
?>
<!doctype html>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<body>
    <div class="flex w-full relative h-[100vh]">
        <div class="w-[300px] h-full bg-white shadow-md flex-shrink-0">
            <?php include('includes/sidebar.php'); ?>
        </div>
        <div 
            class="w-[calc(100%-300px)] h-full overflow-y-auto">
            <div class="h-[60px] px-4 w-full border-b-[1px] border-gray-200 flex items-center justify-between">
                <h3>Product Management</h3>
                <?php include('user.php')?>
            </div>
            <div class="h-[calc(100vh-60px)] w-full">
                
            </div>
        </div>
    </div>
</body>
</html>