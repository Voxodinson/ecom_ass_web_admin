<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $product_type = $_POST['product_type'] ?? null;
    $price = $_POST['price'];
    $sizes = isset($_POST['size']) ? json_encode($_POST['size']) : json_encode([]);
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
    
    $images = json_encode($uploaded_images);

    try {
        $sql = "INSERT INTO products_tb 
                (name, product_type, price, size, images, details, rating, stock_qty, status, brand, color, material, style, product_for) 
                VALUES 
                (:name, :product_type, :price, :size, :images, :details, :rating, :stock_qty, :status, :brand, :color, :material, :style, :product_for)";

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

        if ($stmt->execute()) {
            echo "<div class='bg-green-500 text-white p-3 mb-4 rounded-lg'>Product created successfully!</div>";
        } else {
            echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Error creating product.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Database Error: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="w-full flex items-center justify-center">
    <form method="POST" enctype="multipart/form-data" class="w-full p-3 shadow-lg rounded-2xl border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Create Product</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-lg font-medium text-gray-700">Product Name</label>
                <input type="text" name="name" id="name" placeholder="Enter product name" required class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="product_type" class="block text-lg font-medium text-gray-700">Product Type</label>
                <input type="text" name="product_type" id="product_type" placeholder="Enter product type" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        
            <div>
                <label for="price" class="block text-lg font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="price" placeholder="Enter price" step="0.01" required class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="stock_qty" class="block text-lg  font-medium text-gray-700">Stock Quantity</label>
                <input type="number" name="stock_qty" id="stock_qty" placeholder="Enter stock quantity" required class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="mt-4">
            <label for="size" class="block text-lg font-medium text-gray-700">Sizes</label>
            <div id="size-container">
                <input type="text" name="size[]" placeholder="Size 1" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mb-2">
            </div>
            <button type="button" id="add-size" class="text-sm text-indigo-600 hover:text-indigo-800">+ Add more sizes</button>
        </div>

        <div class="mt-4">
            <label class="block text-lg font-medium text-gray-700">Product Images</label>
            <input type="file" name="images[]" multiple id="image-input" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mt-4">
            <label for="rating" class="block text-lg font-medium text-gray-700">Rating (0-5)</label>
            <input type="number" name="rating" id="rating" placeholder="Enter rating" step="0.1" min="0" max="5" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mt-4">
            <label for="status" class="block text-lg font-medium text-gray-700">Status</label>
            <select name="status" id="status" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="new arrive">New Arrive</option>
                <option value="best sale">Best Sale</option>
            </select>
        </div>

        <div class="mt-4">
            <label for="details" class="block text-lg font-medium text-gray-700">Product Details</label>
            <textarea name="details" id="details" placeholder="Enter product details" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label for="brand" class="block text-lg font-medium text-gray-700">Brand</label>
                <input type="text" name="brand" id="brand" placeholder="Enter brand" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="color" class="block text-lg font-medium text-gray-700">Color</label>
                <input type="text" name="color" id="color" placeholder="Enter color" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label for="material" class="block text-lg font-medium text-gray-700">Material</label>
                <input type="text" name="material" id="material" placeholder="Enter material" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="style" class="block text-lg font-medium text-gray-700">Style</label>
                <input type="text" name="style" id="style" placeholder="Enter style" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="mt-4">
            <label for="product_for" class="block text-lg font-medium text-gray-700">Product For</label>
            <select name="product_for" id="product_for" class=" mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="men">Men</option>
                <option value="women">Women</option>
            </select>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md">
                Create Product
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('add-size').addEventListener('click', function() {
            let sizeContainer = document.getElementById('size-container');
            let newSizeInput = document.createElement('input');
            newSizeInput.type = 'text';
            newSizeInput.name = 'size[]';
            newSizeInput.classList.add('border', 'p-2', 'rounded', 'mb-2', 'w-full');
            newSizeInput.placeholder = 'Additional Size';
            sizeContainer.appendChild(newSizeInput);
        });

        document.getElementById('image-input').addEventListener('change', function(event) {
            let previewContainer = document.getElementById('image-previews');
            previewContainer.innerHTML = ''; // Clear previous previews

            for (let i = 0; i < event.target.files.length; i++) {
                let file = event.target.files[i];
                let reader = new FileReader();

                reader.onload = function(e) {
                    let img = document.createElement('img');
                    img.src = e.target.result; // Display image in preview
                    img.classList.add('w-32', 'h-32', 'object-cover', 'mr-2');

                    let preview = document.createElement('div');
                    preview.classList.add('inline-block', 'mr-4');
                    preview.appendChild(img);
                    previewContainer.appendChild(preview);
                };

                reader.readAsDataURL(file);
            }
        });
    });
</script>
