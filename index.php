<?php
    include('auth.php');
    // Include config.php for database connection
    include('config.php');

    try {
        $sql_income = "
            SELECT SUM(quantity * price) AS total_income
            FROM order_history
        ";

        $stmt_income = $con->prepare($sql_income);
        $stmt_income->execute();
        $total_income = $stmt_income->fetch(PDO::FETCH_ASSOC)['total_income'];

        $sql_top_products = "
            SELECT product_name, SUM(quantity) AS total_sales, image
            FROM order_history
            GROUP BY product_name, image
            ORDER BY total_sales DESC
            LIMIT 5
        ";

        $stmt_top_products = $con->prepare($sql_top_products);
        $stmt_top_products->execute();
        $top_products = $stmt_top_products->fetchAll(PDO::FETCH_ASSOC);

        $sql_total_purchases = "
            SELECT 
                u.id AS user_id,
                u.username,
                u.email,
                SUM(oh.quantity * oh.price) AS total_purchase_amount
            FROM 
                checkout_info ci
            JOIN 
                users u ON ci.user_id = u.id
            JOIN 
                order_history oh ON ci.checkout_info_id = oh.checkout_info_id
            GROUP BY 
                u.id
            ORDER BY 
                total_purchase_amount DESC
        ";

        // Prepare the statement for total purchases
        $stmt_purchases = $con->prepare($sql_total_purchases);
        $stmt_purchases->execute();
        $total_purchases = $stmt_purchases->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<!doctype html>
<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" type="image/x-icon" href="assets/bot.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<body>
    <div class="flex w-full relative h-[100vh]">
        <div class="w-[300px] h-full bg-white shadow-md flex-shrink-0">
            <?php include('includes/sidebar.php'); ?>
        </div>
        <div class="w-[calc(100%-300px)] h-full overflow-y-auto">
            <div class="h-[60px] px-4 w-full border-b-[1px] border-gray-200 flex items-center justify-between">
                <h3>Dashboard</h3>
                <?php include('user.php')?>
            </div>
            <div class="h-[calc(100vh-60px)] w-full p-3">
                <div class="bg-white p-6 rounded-lg shadow-md mb-6 bg-gradient-to-r from-sky-400 to-[#3674B5]">
                    <h2 class="text-2xl font-bold uppercase mb-4 text-white">Total Income</h2>
                    <p class="font-bold text-white text-[3rem]">$<?php echo number_format($total_income, 2); ?></p>
                </div>

                <div class="bg-white p-3 rounded-lg mb-6 border-[1px] border-gray-200">
                    <h2 class="text-lg font-semibold mb-4">Top 5 Most Sold Products</h2>
                    <?php if ($total_purchases): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto rounded-md overflow-hidden">
                                <thead class="bg-[#3674B5]">
                                    <tr class="*:uppercase">
                                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Username</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Email</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Total Purchase Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($total_purchases as $user): ?>
                                        <tr class=" hover:bg-gray-100">
                                            <td class="px-6 py-4 text-md text-gray-700"><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td class="px-6 py-4 text-md text-gray-700"><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td class="px-6 py-4 text-md text-gray-700">$<?php echo number_format($user['total_purchase_amount'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">No purchase data found.</p>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-3 rounded-lg mb-6 border-[1px] border-gray-200">
                    <h2 class="text-lg font-semibold mb-4">Top 5 Most Sold Products</h2>
                    <?php if ($top_products): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto rounded-md overflow-hidden">
                                <thead class="bg-[#3674B5]">
                                    <tr class="*:uppercase">
                                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Image</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Product</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Total Sales</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($top_products as $product): ?>
                                        <tr class="hover:bg-gray-100">
                                            <td class="pl-6 py-4 text-sm text-gray-700">
                                                <img 
                                                    src="uploads/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                                    alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                                    class="w-24 h-24 object-cover rounded-md">
                                            </td>
                                            <td class="px-6 py-4 text-md text-gray-700"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td class="px-6 py-4 text-md text-gray-700"><?php echo $product['total_sales']; ?> items</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">No sales data found.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
