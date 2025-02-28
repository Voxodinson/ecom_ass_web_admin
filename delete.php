<?php
include('config.php');

// Check if product_id is set
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Delete product from the database
    try {
        $stmt = $con->prepare("DELETE FROM products_tb WHERE id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        if ($stmt->execute()) {
            echo "<div class='bg-green-500 text-white p-3 mb-4 rounded-lg'>Product deleted successfully!</div>";
        } else {
            echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Error deleting product.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='bg-red-500 text-white p-3 mb-4 rounded-lg'>Database Error: " . $e->getMessage() . "</div>";
    }
}
?>
