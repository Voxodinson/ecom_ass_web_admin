<?php
include('config.php'); // Include database connection

// Check if the product id is provided for editing
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    try {
        // Fetch the existing product details from the database
        $stmt = $con->prepare("SELECT * FROM products_tb WHERE id = :id");
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Product not found.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Database Error: " . $e->getMessage() . "</div>";
    }
}

// Handle image deletion (if applicable)
if (isset($_GET['delete_image']) && isset($_GET['image_name'])) {
    $image_to_delete = $_GET['image_name'];
    $image_path = 'uploads/images/' . $image_to_delete;

    if (file_exists($image_path)) {
        unlink($image_path); // Delete the image from the server
        // You also need to remove it from the database (assuming JSON storage for images)
        $updated_images = array_filter(json_decode($product['images'], true), fn($image) => $image !== $image_to_delete);
        $updated_images_json = json_encode(array_values($updated_images));

        // Update the database to reflect the deletion
        $stmt = $con->prepare("UPDATE products_tb SET images = :images WHERE id = :id");
        $stmt->bindParam(':images', $updated_images_json);
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();

        header("Location: edit_product.php?id=$product_id"); // Redirect after deletion
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data (same as your original code)
    $name = $_POST['name'];
    $product_type = $_POST['product_type'] ?? null;
    $price = $_POST['price'];
    $sizes = isset($_POST['size']) ? json_encode($_POST['size']) : json_encode([]); // Encode sizes as JSON
    $rating = $_POST['rating'] ?? null;
    $stock_qty = $_POST['stock_qty'] ?? 0;
    $status = $_POST['status'] ?? null;
    $details = $_POST['details'] ?? null;
    $brand = $_POST['brand'] ?? null;
    $color = $_POST['color'] ?? null;
    $material = $_POST['material'] ?? null;
    $style = $_POST['style'] ?? null;
    $product_for = $_POST['product_for'] ?? null;

    $upload_directory = 'uploads/images/';
    $uploaded_images = [];

    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $index => $image_name) {
            if ($_FILES['images']['error'][$index] == 0) {
                $tmp_name = $_FILES['images']['tmp_name'][$index];
                $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                $new_image_name = uniqid("img_", true) . "." . $extension;
                $new_image_path = $upload_directory . $new_image_name;

                if (move_uploaded_file($tmp_name, $new_image_path)) {
                    $uploaded_images[] = $new_image_name;
                } else {
                    echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Failed to upload: $image_name</div>";
                }
            } else {
                echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>File upload error for $image_name. Error Code: " . $_FILES['images']['error'][$index] . "</div>";
            }
        }
    }

    $images = json_encode(array_merge(json_decode($product['images'], true), $uploaded_images));

    try {
        $sql = "UPDATE products_tb SET 
                    name = :name, 
                    product_type = :product_type, 
                    price = :price, 
                    size = :size, 
                    images = :images, 
                    details = :details, 
                    rating = :rating, 
                    stock_qty = :stock_qty, 
                    status = :status, 
                    brand = :brand, 
                    color = :color, 
                    material = :material, 
                    style = :style, 
                    product_for = :product_for 
                WHERE id = :id";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':product_type', $product_type);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':size', $sizes);
        $stmt->bindParam(':images', $images);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':stock_qty', $stock_qty);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':material', $material);
        $stmt->bindParam(':style', $style);
        $stmt->bindParam(':product_for', $product_for);
        $stmt->bindParam(':id', $product_id);

        if ($stmt->execute()) {
            // Display a success message
            echo "<div class='bg-green-500 text-green-500 p-3 mb-4 rounded-lg'>Product updated successfully!</div>";
            
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'products_list.php';
                }, 300);
            </script>";
        } else {
            echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Error updating product.</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Database Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto bg-white rounded-lg shadow-md mt-3">
        <h2 class="text-3xl font-semibold text-center text-gray-800 mb-8">Edit Product</h2>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="text-lg font-medium text-gray-700">Product Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>" class="w-full p-4 mt-2 rounded-lg border border-gray-300" required>
                </div>

                <div class="form-group">
                    <label for="product_type" class="text-lg font-medium text-gray-700">Product Type</label>
                    <input type="text" name="product_type" id="product_type" value="<?php echo $product['product_type']; ?>" class="w-full p-4 mt-2 rounded-lg border border-gray-300">
                </div>

                <div class="form-group">
                    <label for="price" class="text-lg font-medium text-gray-700">Price</label>
                    <input type="number" name="price" id="price" value="<?php echo $product['price']; ?>" step="0.01" class="w-full p-4 mt-2  border border-gray-300" required>
                </div>

                <div class="form-group">
                    <label for="stock_qty" class="text-lg font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="stock_qty" id="stock_qty" value="<?php echo $product['stock_qty']; ?>" class="w-full p-4 mt-2 border border-gray-300" required>
                </div>
            </div>

            <!-- Size Selection -->
            <div class="form-group mt-6">
                <label for="size" class="text-lg font-medium text-gray-700">Sizes</label>
                <div id="size-container">
                    <?php
                    $sizes = json_decode($product['size'], true);
                    foreach ($sizes as $size) {
                        echo '<input type="text" name="size[]" value="' . $size . '" class="w-full p-4 mt-2 rounded-lg border border-gray-300 mb-2" placeholder="Additional Size">';
                    }
                    ?>
                </div>
                <button type="button" id="add-size" class="text-sm text-indigo-600 hover:text-indigo-800 mt-2">+ Add more sizes</button>
            </div>

            <!-- Image Upload Section -->
            <div class="form-group w-full mt-6">
                <label for="images" class="text-lg font-medium text-gray-700">Product Images</label>
                <div id="image-preview-container" class="flex gap-3">
                    <?php
                    $existing_images = json_decode($product['images'], true);
                    if (!empty($existing_images)) {
                        foreach ($existing_images as $image) {
                            echo '
                                <div class="w-fit flex flex-wrap gap-3">
                                        <div class="image-preview-container w-[200px] h-[200px] relative mb-4">
                                            <img src="uploads/images/' . $image . '" alt="Product Image" class="w-full h-full object-cover rounded-md">
                                            <a 
                                                href="edit_product.php?id=' . $product_id . '&delete_image=1&image_name=' . $image . '" 
                                                class="absolute top-0 w-[50px] h-[50px] rounded-full flex items-center justify-center bg-red-500 text-white p-1" 
                                                onclick="return confirm(\'Are you sure you want to delete this image?\')">X</a>
                                        </div>
                                </div>';
                        }
                    }
                    ?>
                </div>
                <input type="file" name="images[]" multiple id="image-input" class="w-full p-4 mt-2 rounded-lg border border-gray-300">
            </div>

            <!-- Rating -->
            <div class="form-group mt-6">
                <label for="rating" class="text-lg font-medium text-gray-700">Rating (0-5)</label>
                <input type="number" name="rating" id="rating" value="<?php echo $product['rating']; ?>" step="0.1" min="0" max="5" class="w-full p-4 mt-2 rounded-lg border border-gray-300">
            </div>

            <!-- Status -->
            <div class="form-group mt-6">
                <label for="status" class="text-lg font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="w-full p-4 mt-2 rounded-lg border border-gray-300">
                    <option value="best sale" <?php echo $product['status'] == 'best sale' ? 'selected' : ''; ?>>Best Sale</option>
                    <option value="new arrive" <?php echo $product['status'] == 'new arrive' ? 'selected' : ''; ?>>New Arrive</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" class="w-full bg-black text-white p-4 rounded-lg">Update Product</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add new size input dynamically
            document.getElementById('add-size').addEventListener('click', function() {
                const sizeContainer = document.getElementById('size-container');
                const newSizeInput = document.createElement('input');
                newSizeInput.type = 'text';
                newSizeInput.name = 'size[]';
                newSizeInput.classList.add('w-full', 'p-4', 'mt-2', 'rounded-lg', 'border', 'border-gray-300', 'mb-2');
                newSizeInput.placeholder = 'Additional Size';
                sizeContainer.appendChild(newSizeInput);
            });

            // Preview selected images
            document.getElementById('image-input').addEventListener('change', function(e) {
                const previewContainer = document.getElementById('image-preview-container');
                previewContainer.innerHTML = ''; 
                
                const files = e.target.files;
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const imgElement = document.createElement('img');
                        imgElement.src = event.target.result;
                        imgElement.classList.add('w-full', 'h-auto', 'rounded-lg');
                        
                        const previewDiv = document.createElement('div');
                        previewDiv.classList.add('image-preview-container', 'relative', 'mb-4');
                        previewDiv.appendChild(imgElement);
                        
                        previewContainer.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
</body>
</html>
