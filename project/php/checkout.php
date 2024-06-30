<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $country = $_POST['country'];

    $conn = new mysqli("localhost", "root", "", "online_store");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert order
    $user_id = $_SESSION['user_id']; // Assuming user is logged in and their ID is stored in session
    $total_price = 0;

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $result = $conn->query("SELECT price FROM products WHERE id=$product_id");
        $product = $result->fetch_assoc();
        $total_price += $product['price'] * $quantity;
    }

    $sql = "INSERT INTO orders (user_id, total_price) VALUES ($user_id, $total_price)";
    if ($conn->query($sql) === TRUE) {
        $order_id = $conn->insert_id;

        // Insert order items
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $result = $conn->query("SELECT price FROM products WHERE id=$product_id");
            $product = $result->fetch_assoc();
            $price = $product['price'];
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $quantity, $price)";
            $conn->query($sql);
        }

        // Clear cart
        unset($_SESSION['cart']);
        
        // Redirect to confirmation
        header("Location: ../confirmation.html");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
