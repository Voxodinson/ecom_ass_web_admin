<?php
include('auth.php');
include_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $product_type = $_POST['product_type'];
    $price = $_POST['price'];
    $size = json_encode($_POST['size']); // JSON encode sizes
    $rating = $_POST['rating'];
    $stock_qty = $_POST['stock_qty'];
    $status = $_POST['status'];
    $details = $_POST['details'];
    $brand = $_POST['brand'];
    $color = $_POST['color'];
    $material = $_POST['material'];
    $style = $_POST['style'];
    $product_for = $_POST['product_for'];

    // Handle file upload
    $uploaded_images = [];
    $upload_directory = 'uploads/images/';   
    foreach ($_FILES['images']['name'] as $index => $image_name) {
        if ($_FILES['images']['error'][$index] == 0) {
            $tmp_name = $_FILES['images']['tmp_name'][$index];
            $new_image_name = $upload_directory . basename(preg_replace('/[^a-zA-Z0-9_-]/', '_', $image_name));
            
            $file_size = $_FILES['images']['size'][$index];
            $max_file_size = 200 * 1024 * 1024; // 200MB
            if ($file_size > $max_file_size) {
                $error_message = "File size exceeds the allowed limit of 10MB for $image_name.";
                break;
            }
            
            if (move_uploaded_file($tmp_name, $new_image_name)) {
                $uploaded_images[] = $new_image_name;
            } else {
                error_log("Failed to move file from $tmp_name to $new_image_name");
                $error_message = "Error moving file: $image_name.";
                break;
            }
        } else {
            error_log("File upload error for $image_name: " . $_FILES['images']['error'][$index]);
            $error_message = "Error uploading file: $image_name.";
            break;
        }
    }

    $images = json_encode($uploaded_images);

    if (!isset($error_message)) {
        try {
            $sql = "INSERT INTO products_tb 
                    (name, product_type, price, size, images, details, rating, stock_qty, status, brand, color, material, style, product_for) 
                    VALUES 
                    (:name, :product_type, :price, :size, :images, :details, :rating, :stock_qty, :status, :brand, :color, :material, :style, :product_for)";
            
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':product_type', $product_type);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':size', $size);
            $stmt->bindParam(':images', $images);  // Bind the images JSON string
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':rating', $rating);
            $stmt->bindParam(':stock_qty', $stock_qty);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':brand', $brand);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':material', $material);
            $stmt->bindParam(':style', $style);
            $stmt->bindParam(':product_for', $product_for);

            if ($stmt->execute()) {
                $success_message = "Product created successfully!";
            } else {
                $error_message = "There was an error creating the product.";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="flex-1 h-full overflow-y-auto">
    <div class="">
        <div class="bg-white p-2 rounded-lg shadow-md">
            <?php if (isset($success_message)): ?>
                <div class="bg-green-500 text-white p-3 mb-4 rounded-lg">
                    <?= $success_message ?>
                </div>
            <?php elseif (isset($error_message)): ?>
                <div class="bg-red-500 text-white p-3 mb-4 rounded-lg">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col">
                        <label for="name" class="mb-2 font-semibold">Product Name</label>
                        <input type="text" name="name" id="name" class="border p-2 rounded" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="product_type" class="mb-2 font-semibold">Product Type</label>
                        <input type="text" name="product_type" id="product_type" class="border p-2 rounded">
                    </div>

                    <div class="flex flex-col">
                        <label for="price" class="mb-2 font-semibold">Price</label>
                        <input type="number" name="price" id="price" class="border p-2 rounded" required step="0.01">
                    </div>

                    <div class="flex flex-col">
                        <label for="size" class="mb-2 font-semibold">Size</label>
                        <div id="size-container" class="w-full grid grid-cols-3 flex-col gap-3">
                            <input type="text" name="size[]" class="border p-2 rounded mb-2 w-full" placeholder="Size 1">
                        </div>
                        <button type="button" id="add-size" class="text-blue-500 mt-2">Add more sizes</button>
                    </div>

                    <div class="flex flex-col">
                        <label for="images" class="mb-2 font-semibold">Images (Upload Multiple)</label>
                        <input type="file" name="images[]" class="border p-2 rounded mb-2" accept="image/*" multiple id="image-input" required>
                        <div id="image-previews" class="mt-4"></div>
                    </div>

                    <div class="flex flex-col">
                        <label for="rating" class="mb-2 font-semibold">Rating (0-5)</label>
                        <input type="number" name="rating" id="rating" class="border p-2 rounded" required step="0.1" min="0" max="5">
                    </div>

                    <div class="flex flex-col">
                        <label for="stock_qty" class="mb-2 font-semibold">Stock Quantity</label>
                        <input type="number" name="stock_qty" id="stock_qty" class="border p-2 rounded" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="status" class="mb-2 font-semibold">Status</label>
                        <select name="status" id="status" class="border p-2 rounded" required>
                            <option value="new arrive">New Arrive</option>
                            <option value="best sale">Best Sale</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="details" class="mb-2 font-semibold">Product Details</label>
                        <textarea name="details" id="details" class="border p-2 rounded" rows="4"></textarea>
                    </div>

                    <div class="flex flex-col">
                        <label for="brand" class="mb-2 font-semibold">Brand</label>
                        <input type="text" name="brand" id="brand" class="border p-2 rounded">
                    </div>

                    <div class="flex flex-col">
                        <label for="color" class="mb-2 font-semibold">Color</label>
                        <input type="text" name="color" id="color" class="border p-2 rounded">
                    </div>

                    <div class="flex flex-col">
                        <label for="material" class="mb-2 font-semibold">Material</label>
                        <input type="text" name="material" id="material" class="border p-2 rounded">
                    </div>

                    <div class="flex flex-col">
                        <label for="style" class="mb-2 font-semibold">Style</label>
                        <input type="text" name="style" id="style" class="border p-2 rounded">
                    </div>

                    <div class="flex flex-col">
                        <label for="product_for" class="mb-2 font-semibold">For</label>
                        <select name="product_for" id="product_for" class="border p-2 rounded">
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('image-input').addEventListener('change', function(event) {
        const previewContainer = document.getElementById('image-previews');
        previewContainer.innerHTML = ''; // Clear any previous previews

        for (let i = 0; i < event.target.files.length; i++) {
            const file = event.target.files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('w-32', 'h-32', 'object-cover', 'mr-2');
                
                const deleteButton = document.createElement('button');
                deleteButton.innerText = 'Delete';
                deleteButton.classList.add('text-red-500', 'mt-2');
                deleteButton.onclick = function() {
                    img.remove();
                    deleteButton.remove();
                };

                const preview = document.createElement('div');
                preview.classList.add('inline-block', 'mr-4');
                preview.appendChild(img);
                preview.appendChild(deleteButton);

                previewContainer.appendChild(preview);
            };

            reader.readAsDataURL(file);
        }
    });

    document.getElementById('add-size').addEventListener('click', function() {
        const sizeContainer = document.getElementById('size-container');
        const newSizeInput = document.createElement('input');
        newSizeInput.type = 'text';
        newSizeInput.name = 'size[]';
        newSizeInput.classList.add('border', 'p-2', 'rounded', 'mb-2');
        newSizeInput.placeholder = 'Additional Size';
        sizeContainer.appendChild(newSizeInput);
    });
</script>
