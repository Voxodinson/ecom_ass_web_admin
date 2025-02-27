<?php
    include('auth.php');
    include_once('config.php');

    try {
        // Use PDO to fetch products
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        function toggleCreateForm() {
            const table = document.getElementById('products-table');
            const createForm = document.getElementById('create-form');
            table.classList.toggle('hidden');
            createForm.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-auto ">
    <div class="w-[300px] h-full bg-white shadow-md flex-shrink-0">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <div class="flex-1 h-full overflow-auto">
        <div class="h-[60px] w-full border-b border-gray-200 flex items-center justify-between px-4">
            <h3>Dashboard</h3>
            <div class="flex gap-3 items-center ">
                <button onclick="toggleCreateForm()" class="ml-4 text-[.8rem] bg-[#3674B5] text-white px-4 py-2 rounded-full">
                    Create Product
                </button>
                <?php include('user.php')?>
            </div>
        </div>
        <div id="products-table" class="p-2">
            <div class="bg-white p-2 rounded-lg shadow-md">
                <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-[#3674B5] text-white">
                        <tr class="text-[1rem] font-thin">
                            <th class="py-3 px-6 text-left">Image</th>
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Type</th>
                            <th class="py-3 px-6 text-left">Price</th>
                            <th class="py-3 px-6 text-left">Size</th>
                            <th class="py-3 px-6 text-left">Rating</th>
                            <th class="py-3 px-6 text-left">Stock</th>
                            <th class="py-3 px-6 text-left">Actions</th>
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
                                            echo '<img src="' . htmlspecialchars($images[0]) . '" class="w-[80px] h-[80px] object-center rounded-lg">';
                                        } else {
                                            echo 'No Image';
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
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-500 hover:underline ml-3">
                                            <i class="fa-solid text-[1.2rem]  fa-trash"></i>
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
        <div class="w-full p-2">
            <div id="create-form" class="p-2 bg-white shadow-md rounded-lg overflow-auto hidden">
                <h2 class="text-xl font-semibold mb-4">Create New Product</h2>
                <?php include('create_product.php')?>
            </div>
        </div>
    </div>
</div>

<?php
    $con = null;
?>
</body>
</html>
