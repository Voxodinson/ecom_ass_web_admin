<?php
include('auth.php');
include_once('config.php');
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    try {
        $sql = "DELETE FROM products_tb WHERE id = :id";
        $stmt = $con->prepare($sql);

        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: products_list.php?status=success');
        } else {
            header('Location: products_list.php?status=error');
        }
    } catch (PDOException $e) {
        die("Delete Error: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
}

$con = null;
?>
