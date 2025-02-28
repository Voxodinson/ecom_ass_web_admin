<?php
    include_once('config.php');

// Get the product ID to update
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from the database
    $stmt = $con->prepare("SELECT * FROM products_tb WHERE id = :product_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Product not found.</div>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link href="src/output.css" rel="stylesheet">
</head>
<body>
    <div class="w-[50%] flex">
        <form method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <!-- Product Name -->
            <div class="mb-4">
                <label for="name" class="block text-lg font-medium text-gray-700">Product Name</label>
                <input type="text" name="name" id="name" placeholder="Product Name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Product Type -->
            <div class="mb-4">
                <label for="product_type" class="block text-lg font-medium text-gray-700">Product Type</label>
                <input type="text" name="product_type" id="product_type" placeholder="Product Type" value="<?php echo htmlspecialchars($product['product_type']); ?>" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Price -->
            <div class="mb-4">
                <label for="price" class="block text-lg font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="price" placeholder="Price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Sizes -->
            <div class="mb-4">
                <label for="size" class="block text-lg font-medium text-gray-700">Sizes</label>
                <div id="size-container">
                    <?php
                    $sizes = json_decode($product['size'], true);
                    foreach ($sizes as $size) {
                        echo '<input type="text" name="size[]" value="' . htmlspecialchars($size) . '" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mb-2">';
                    }
                    ?>
                </div>
                <button type="button" id="add-size" class="inline-block mt-2 text-sm text-indigo-600 hover:text-indigo-900">Add more sizes</button>
            </div>

            <!-- Image Upload -->
            <div class="mb-4">
                <label for="image-input" class="block text-lg font-medium text-gray-700">Product Images</label>
                <input type="file" name="images[]" multiple id="image-input" class="mt-1 block w-full text-sm text-gray-500 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                
                <!-- Show image previews if they exist -->
                <div id="image-previews" class="mt-4 flex flex-wrap gap-4">
                    <?php
                    $images = json_decode($product['images'], true);
                    if ($images) {
                        foreach ($images as $image) {
                            echo '<div class="inline-block mr-4"><img src="uploads/images/' . htmlspecialchars($image) . '" class="w-32 h-32 object-cover"></div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Rating -->
            <div class="mb-4">
                <label for="rating" class="block text-lg font-medium text-gray-700">Rating (0-5)</label>
                <input type="number" name="rating" id="rating" placeholder="Rating" value="<?php echo htmlspecialchars($product['rating']); ?>" step="0.1" min="0" max="5" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Stock Quantity -->
            <div class="mb-4">
                <label for="stock_qty" class="block text-lg font-medium text-gray-700">Stock Quantity</label>
                <input type="number" name="stock_qty" id="stock_qty" placeholder="Stock Quantity" value="<?php echo htmlspecialchars($product['stock_qty']); ?>" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-lg font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="new arrive" <?php echo ($product['status'] == 'new arrive') ? 'selected' : ''; ?>>New Arrive</option>
                    <option value="best sale" <?php echo ($product['status'] == 'best sale') ? 'selected' : ''; ?>>Best Sale</option>
                </select>
            </div>

            <!-- Product Details -->
            <div class="mb-4">
                <label for="details" class="block text-lg font-medium text-gray-700">Product Details</label>
                <textarea name="details" id="details" placeholder="Product Details" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($product['details']); ?></textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Update Product in Database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $product_type = $_POST['product_type'] ?? null;
    $price = $_POST['price'];
    $sizes = isset($_POST['size']) ? json_encode($_POST['size']) : json_encode([]);
    $rating = $_POST['rating'] ?? null;
    $stock_qty = $_POST['stock_qty'] ?? 0;
    $status = $_POST['status'] ?? null;
    $details = $_POST['details'] ?? null;
    $images = $_FILES['images'] ?? null;

    // Handle image upload
    $uploaded_images = json_decode($product['images'], true);
    if (!empty($images['name'][0])) {
        $upload_directory = 'uploads/images/';
        $new_images = [];

        foreach ($images['name'] as $index => $image_name) {
            $tmp_name = $images['tmp_name'][$index];
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            $new_image_name = uniqid("img_", true) . "." . $extension;
            $new_image_path = $upload_directory . $new_image_name;

            if (move_uploaded_file($tmp_name, $new_image_path)) {
                $new_images[] = $new_image_path;
            }
        }

        // Combine existing images with new ones
        $uploaded_images = array_merge($uploaded_images, $new_images);
    }

    // Convert image paths to JSON
    $images_json = json_encode($uploaded_images);

    try {
        $sql = "UPDATE products_tb 
                SET name = :name, product_type = :product_type, price = :price, size = :size, images = :images, 
                    details = :details, rating = :rating, stock_qty = :stock_qty, status = :status
                WHERE id = :product_id";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':product_type', $product_type);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':size', $sizes);
        $stmt->bindParam(':images', $images_json);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':stock_qty', $stock_qty);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':product_id', $_POST['product_id']);

        if ($stmt->execute()) {
            echo "<div class='bg-green-500 text-white p-3 mb-4 rounded-lg'>Product updated successfully!</div>";
        } else {
            echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Error updating product.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Database Error: " . $e->getMessage() . "</div>";
    }
}
?>
