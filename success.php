<?php 
    define('DB_DSN', 'mysql:host=localhost;dbname=fhms');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');

    require 'config.php'; // Ensure API_KEY and API_SECRET are defined here
    session_start();

    // Check if the payment data exists in the POST request
    if (!empty($_POST)) {
        // Get Razorpay response data from the POST request
        $razorpay_order_id = $_POST['razorpay_order_id'];
        $razorpay_payment_id = $_POST['razorpay_payment_id'];
        $razorpay_signature = $_POST['razorpay_signature'];
        
        // The order ID you stored in session
        $order_id = $_SESSION['order_id'];
        
        // Generate the server-side signature
        $generated_signature = hash_hmac('sha256', $order_id . "|" . $razorpay_payment_id, API_SECRET);

        // Check if the generated signature matches the Razorpay signature
        if ($generated_signature == $razorpay_signature) {
            // Payment is successful, now store the data in the database

            // Retrieve payment details (ensure these are available in your form or session)
            $user_id = $_POST['user_id'];  // Assuming you send user_id along with the payment form
            $hall_id = $_POST['hall_id'];  // Hall ID
            $amount = $_POST['amount'] * 100;  // Amount in paise (multiply by 100 for paise)
            $currency = 'INR';  // Currency in INR
            $checkin_date = $_POST['checkin'];  // Check-in date
            $checkout_date = $_POST['checkout'];  // Checkout date
            $hall_name = $_POST['hall_name'];  // Hall name

            // Initialize a PDO connection for your database
            try {
                $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Insert payment data into the database
                $stmt = $pdo->prepare("INSERT INTO payments 
                    (user_id, hall_id, amount, currency, payment_status, order_id, payment_id, checkin_date, checkout_date, hall_name)
                    VALUES (:user_id, :hall_id, :amount, :currency, :payment_status, :order_id, :payment_id, :checkin_date, :checkout_date, :hall_name)");

                $stmt->execute([
                    ':user_id' => $user_id,
                    ':hall_id' => $hall_id,
                    ':amount' => $amount,
                    ':currency' => $currency,
                    ':payment_status' => 'captured',  // Assuming payment was successful
                    ':order_id' => $razorpay_order_id,
                    ':payment_id' => $razorpay_payment_id,
                    ':checkin_date' => $checkin_date,
                    ':checkout_date' => $checkout_date,
                    ':hall_name' => $hall_name
                ]);

                // Optionally, you can update payment status if needed
                // For example, adding a refund status, etc.
                
                // After successfully storing payment details, redirect to the main page or confirmation page
                header("Location: main_page.php?status=success"); // Or the page where you want to redirect
                exit();
            } catch (PDOException $e) {
                // Handle database connection or query error
                echo 'Database error: ' . $e->getMessage();
            }
        } else {
            // Payment failed due to signature mismatch
            echo 'Payment failed: Invalid signature';
        }
    } else {
        echo 'Payment failed: No data received';
    }
?>
