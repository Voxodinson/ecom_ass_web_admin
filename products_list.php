<?php
include('auth.php');
include_once('config.php');

try {
    $sql = "SELECT * FROM products_tb";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="assets/bot.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        function toggleCreateForm() {
            const table = document.getElementById('products-table');
            const createForm = document.getElementById('create-form');
            table.classList.toggle('hidden');
            createForm.classList.toggle('hidden');
        }

        function confirmDelete(id) {
            const isConfirmed = confirm('Are you sure you want to delete this product?');
            if (isConfirmed) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</head>
<body>

<div class="flex h-screen overflow-auto ">
    <div class="w-[300px] h-full bg-white shadow-md flex-shrink-0">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <div class="flex-1 h-full overflow-auto">
        <div class="h-[60px] w-full border-b border-gray-200 flex items-center justify-between px-4">
            <h3>Product List</h3>
            <div class="flex gap-3 items-center ">
                <a href='create_product.php' class="ml-4 text-[.8rem] bg-[#3674B5] text-white px-4 py-2 rounded-full">
                    Create Product
                </a>
                <?php include('user.php')?>
            </div>
        </div>
        <?php if (isset($_GET['status'])): ?>
            <div class="absolute top-0 right-0 w-full p-4 bg-green-500 text-white text-center">
                <?php 
                    if ($_GET['status'] == 'success') {
                        echo 'Product deleted successfully!';
                    } elseif ($_GET['status'] == 'created') {
                        echo 'Product created successfully!';
                    } else {
                        echo 'Failed to delete the product!';
                    }
                ?>
            </div>

            <script>
                setTimeout(function() {
                    const url = new URL(window.location);
                    url.searchParams.delete('status');
                    window.history.replaceState({}, document.title, url.toString()); 
                    window.location.reload(); 
                }, 1500);
            </script>
        <?php endif; ?>
        <div id="products-table" class="p-3 w-full ">
            <div class="bg-white p-3 rounded-lg  border-[1px] border-gray-200">
                <h3 class="mb-4 text-lg font-semibold">Users List</h3>
                <table class="w-full bg-white  rounded-lg overflow-hidden">
                    <thead class="bg-[#3674B5] text-white">
                        <tr class=" font-thin">
                            <th class="py-2 px-4 text-left">Image</th>
                            <th class="py-2 px-4 text-left">Name</th>
                            <th class="py-2 px-4 text-left">Type</th>
                            <th class="py-2 px-4 text-left">Price</th>
                            <th class="py-2 px-4 text-left">Size</th>
                            <th class="py-2 px-4 text-left">Rating</th>
                            <th class="py-2 px-4 text-left">Stock</th>
                            <th class="py-2 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (count($result) > 0): ?>
                            <?php foreach ($result as $row): ?>
                                <tr class="border-b *:text-[.9rem] hover:bg-gray-100 transition">
                                    <td class="py-3 px-6">
                                        <?php
                                        $images = json_decode($row['images'], true);
                                        if (!empty($images) && is_array($images)) {
                                            echo '<img src="uploads/images/' . htmlspecialchars($images[0]) . '" class="w-[80px] h-[80px] object-center rounded-lg">';
                                        } else {
                                            echo '<img src="assets/no_image.jpg" class="w-[80px] h-[80px] object-center rounded-lg">';
                                        }
                                        ?>
                                    </td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($row['product_type'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-6">$<?= number_format($row['price'], 2) ?></td>
                                    <td class="py-3 px-6">
                                        <?php
                                        $sizes = json_decode($row['size'], true);
                                        echo $sizes ? implode(', ', $sizes) : 'N/A';
                                        ?>
                                    </td>
                                    <td class="py-3 px-6"><?= $row['rating'] ?? 'N/A' ?> ⭐</td>
                                    <td class="py-3 px-6"><?= $row['stock_qty'] ?></td>
                                    <td class="py-3 px-6">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:underline">
                                            <i class="fa-solid text-[1.2rem] fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="confirmDelete(<?= $row['id'] ?>)" class="text-red-500 hover:underline ml-3">
                                            <i class="fa-solid text-[1.2rem] fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="create-form" class="p-3 w-full bg-white hidden">
            <h2 class="text-xl font-semibold mb-4">Create New Product</h2>
            <?php include('create_product.php')?>
        </div>
    </div>
</div>

<?php
    $con = null;
?>

</body>
</html>
