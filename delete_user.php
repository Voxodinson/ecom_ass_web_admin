<?php
include('auth.php');
include_once('config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header("Location: user_list.php?message=User/Admin deleted successfully");
            exit();
        } else {
            echo "Error: Unable to delete user/admin.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
