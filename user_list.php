<?php
    include('auth.php');
?>
<!doctype html>
<html>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="src/output.css" rel="stylesheet">
<body>
    <div class="flex w-full relative h-[100vh]">
        <div class="w-[300px] h-full">
            <?php include('includes/sidebar.php')?>
        </div>
        <div 
            class="w-[calc(100%-300px)] h-full overflow-y-auto">
            <div class="h-[60px] w-full border-b-[1px] border-gray-200">
                ffd
            </div>
            <div class="h-[calc(100vh-60px)] w-full">user</div>
        </div>
    </div>
</body>
</html>