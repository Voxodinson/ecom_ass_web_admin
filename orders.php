<?php 
    include('config.php');
    include('auth.php'); 

    try {
        $ordersSql = "
            SELECT 
                ci.checkout_info_id, ci.user_id, ci.fname, ci.lname, ci.address, 
                ci.towncity, ci.stateprovince, ci.zippostalcode, ci.email, ci.phone, ci.payment_method,
                oh.order_history_id, oh.product_name, oh.price, oh.quantity, 
                oh.subtotal, oh.order_date, oh.image
            FROM checkout_info ci
            LEFT JOIN order_history oh ON ci.checkout_info_id = oh.checkout_info_id
            ORDER BY oh.order_date DESC, oh.order_history_id ASC
        ";

        $orderStmt = $con->prepare($ordersSql);
        $orderStmt->execute();
        $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching orders: " . $e->getMessage());
    }

    $groupedOrders = [];
    foreach ($orders as $row) {
        $checkoutId = $row['checkout_info_id'];
        if (!isset($groupedOrders[$checkoutId])) {
            $groupedOrders[$checkoutId]['user_info'] = [
                'user_id' => $row['user_id'],
                'fname' => $row['fname'],
                'lname' => $row['lname'],
                'address' => $row['address'],
                'towncity' => $row['towncity'],
                'stateprovince' => $row['stateprovince'],
                'zippostalcode' => $row['zippostalcode'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'payment_method' => $row['payment_method']
            ];
            $groupedOrders[$checkoutId]['orders'] = [];
        }

        if (!empty($row['order_history_id'])) {
            $groupedOrders[$checkoutId]['orders'][] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Checkout & Order History</title>
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
                <h3>All Checkout & Order History</h3>
                <div class="flex gap-3 items-center justify-center">
                    <?php include('user.php') ?>
                </div>
            </div>
            <div class="h-[calc(100vh-60px)] w-full p-3">
                <h2 class="text-2xl font-semibold mb-4">All Checkout Data</h2>
                <?php if (!empty($groupedOrders)): ?>
                    <?php foreach ($groupedOrders as $checkout_id => $data): ?>
                        <div class="p-3 mb-6 bg-gray-100 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Checkout ID: <span class="text-blue-400">OID-000<?= htmlspecialchars($checkout_id) ?></span></h3>
                            <p><strong>User ID:</strong> <?= htmlspecialchars($data['user_info']['user_id']) ?></p>
                            <p><strong>Name:</strong> <?= htmlspecialchars($data['user_info']['fname'] . ' ' . $data['user_info']['lname']) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($data['user_info']['address']) ?>, <?= htmlspecialchars($data['user_info']['towncity']) ?>, <?= htmlspecialchars($data['user_info']['stateprovince']) ?>, <?= htmlspecialchars($data['user_info']['zippostalcode']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($data['user_info']['email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($data['user_info']['phone']) ?></p>
                            <p><strong>Payment Method:</strong> <?= htmlspecialchars($data['user_info']['payment_method']) ?></p>
                            <?php if (!empty($data['orders'])): ?>
                                <table class="min-w-full table-auto border-collapse border-[1px] border-gray-300 rounded-md overflow-hidden bg-white mt-3">
                                    <thead class="bg-[#3674B5]">
                                        <tr class="text-xs uppercase text-white">
                                            <th class="py-2 px-4 text-left">Order History ID</th>
                                            <th class="py-2 px-4 text-left">Product Name</th>
                                            <th class="py-2 px-4 text-left">Price</th>
                                            <th class="py-2 px-4 text-left">Quantity</th>
                                            <th class="py-2 px-4 text-left">Subtotal</th>
                                            <th class="py-2 px-4 text-left">Order Date</th>
                                            <th class="py-2 px-4 text-left">Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['orders'] as $row): ?>
                                            <tr class="hover:bg-gray-100">
                                                <td class="py-2 px-8 text-left"><?= htmlspecialchars($row['order_history_id']) ?></td>
                                                <td class="py-2 px-4 text-left"><?= htmlspecialchars($row['product_name']) ?></td>
                                                <td class="py-2 px-4 text-left">$<?= htmlspecialchars($row['price']) ?></td>
                                                <td class="py-2 px-4 text-left"><?= htmlspecialchars($row['quantity']) ?></td>
                                                <td class="py-2 px-4 text-left">$<?= htmlspecialchars($row['subtotal']) ?></td>
                                                <td class="py-2 px-4 text-left"><?= htmlspecialchars($row['order_date']) ?></td>
                                                <td class="py-2 px-4 text-left">
                                                    <img    
                                                        src="<?= !empty($row['image']) ? 'uploads/images/' . htmlspecialchars($row['image']) : 'assets/no_image.jpg' ?>"
                                                        alt="Product Image" 
                                                        class="w-20 h-20 object-cover">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center py-4">No products found for this checkout.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center py-4">No checkout records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
