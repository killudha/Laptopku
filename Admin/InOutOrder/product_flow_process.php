<?php
// Connect to the database
include 'C:\xampp\htdocs\Laptopku\config.php';

try {
    // Ensure that the received data is complete
    if (isset($_POST['type'], $_POST['id_product'], $_POST['date'], $_POST['quantity'])) {
        // Get data from the form
        $type = $_POST['type'];
        $id_product = $_POST['id_product'];
        $date = $_POST['date'];
        $quantity = $_POST['quantity'];

        // Ensure the quantity is greater than 0
        if ($quantity > 0) {
            // Begin transaction for atomicity
            $conn->beginTransaction();

            // Determine the table to be used based on the type and update stock
            if ($type == 'In') {
                // Query to save incoming product data (In_Product table)
                $query = "INSERT INTO In_Product (id_product, in_date, quantity) 
                          VALUES (:id_product, :date, :quantity)";
                
                // Update stock in Products table
                $update_query = "UPDATE Products 
                                 SET stock = stock + :quantity
                                 WHERE id_product = :id_product";
            } elseif ($type == 'Out') {
                // Query to save outgoing product data (Out_Product table)
                $query = "INSERT INTO Out_Product (id_product, out_date, quantity) 
                          VALUES (:id_product, :date, :quantity)";
                
                // Update stock in Products table
                $update_query = "UPDATE Products 
                                 SET stock = stock - :quantity
                                 WHERE id_product = :id_product";
            } else {
                throw new Exception("Invalid type");
            }

            // Prepare and execute the queries
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Prepare the update query for stock
                $stmt_update = $conn->prepare($update_query);
                $stmt_update->bindParam(':id_product', $id_product, PDO::PARAM_INT);
                $stmt_update->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                if ($stmt_update->execute()) {
                    // Commit the transaction if both queries succeed
                    $conn->commit();
                    // Redirect back to the main page after success
                    header("Location: product_flow.php?message=Data saved and stock updated successfully");
                    exit;
                } else {
                    throw new Exception("Failed to update stock in Products table");
                }
            } else {
                throw new Exception("Failed to save data");
            }
        } else {
            throw new Exception("Quantity must be greater than 0");
        }
    } else {
        throw new Exception("Incomplete data received");
    }
} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollBack();
    // Display error message if an issue occurs
    die("An error occurred: " . $e->getMessage());
}
?>
