<?php
require('inc/essentials.php');
require('inc/db_config.php');
require_once('inc/pdf/vendor/autoload.php');

adminLogin();

if (isset($_GET['gen_pdf']) && isset($_GET['id'])) {
    $frm_data = filteration($_GET);
    $booking_id = $frm_data['id'];

    // Fetch booking details along with user and service information
    $query = "SELECT bo.*, bd.*, uc.email, 
                     GROUP_CONCAT(s.name ORDER BY s.id SEPARATOR ', ') AS service_names, 
                     GROUP_CONCAT(s.price ORDER BY s.id SEPARATOR ', ') AS service_prices 
              FROM `booking_order` bo 
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id 
              LEFT JOIN `services` s ON FIND_IN_SET(s.id, bo.services) > 0
              WHERE bo.booking_id = '$booking_id'
              GROUP BY bo.booking_id";

    $res = mysqli_query($con, $query);
    if (mysqli_num_rows($res) == 0) {
        header('location: dashboard.php');
        exit;
    }

    $data = mysqli_fetch_assoc($res);

    // Convert dates
    $date = date("h:ia d-m-Y", strtotime($data['datetime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));

    // Calculate number of days booked
    $check_in = new DateTime($data['check_in']);
    $check_out = new DateTime($data['check_out']);
    $days = date_diff($check_in, $check_out)->days + 1;
    //$days = max(1, $count_days->days); // Ensure at least 1 day is counted

    // Calculate Hall Total
    $hall_total = $data['price'] * $days;

    // Generate service list and calculate total service price
    $service_list = "";
    $service_total = 0;
    if (!empty($data['service_names'])) {
        $service_names = explode(", ", $data['service_names']);
        $service_prices = explode(", ", $data['service_prices']);

        foreach ($service_names as $index => $service) {
            $service_price = isset($service_prices[$index]) ? (float) $service_prices[$index] : 0;
            $service_list .= "<tr><td>$service</td><td class='price'>₹$service_price</td></tr>";
            $service_total += $service_price;
        }
    }

    // Calculate Grand Total (Hall + Services)
    $total_amount = $hall_total + $service_total;
    // Refund status
    $refund_status = ($data['booking_status'] == 'cancelled') ? ($data['refund'] ? "Amount Refunded" : "Not yet Refunded") : "N/A";

    // Generate the receipt HTML
    $html = "
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { width: 100%; padding: 20px; border: 2px solid #ccc; }
        h2 { text-align: center; color: #444; }
        .brand { text-align: center; font-size: 14px; color: #777; margin-bottom: 10px; }
        .highlight { color: #d9534f; font-weight: bold; }
        .price { color: #28a745; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-size: 16px; font-weight: bold; color: #007bff; text-align: right; }
    </style>

    <div class='container'>
        <h2>Booking Receipt</h2>
        <p class='brand'>Function Hall Management System</p>
        <table>
            <tr><td><b>Order ID:</b> <span class='highlight'>$data[order_id]</span></td></tr>
            <tr><td><b>Booking ID:</b> <span class='highlight'>$data[booking_id]</span></td></tr>
            <tr><td><b>Booking Date:</b> <span class='highlight'>$date</span></td></tr>
            <tr><td><b>Status:</b> <span class='highlight'>$data[booking_status]</span></td></tr>
            <tr><td><b>Refund Status:</b> $refund_status($data[ref_amount])</td></tr>
            <tr><td><b>Name:</b> $data[user_name]</td></tr>
            <tr><td><b>Email:</b> $data[email]</td></tr>
            <tr><td><b>Phone Number:</b> $data[phonenum]</td></tr>
            <tr><td><b>Address:</b> $data[address]</td></tr>
            <tr><td><b>Hall Name:</b> $data[hall_name]</td></tr>
            <tr><td><b>Hall Price:</b> ₹$data[price]/day</td></tr>
            <tr><td><b>Check-in Date:</b> $checkin</td></tr>
            <tr><td><b>Check-out Date:</b> $checkout</td></tr>
            <tr><td><b>Number of Days:</b> $days</td></tr>
        </table>

        <h3>Service Details</h3>
        <table>
            <tr><th>Service Name</th><th>Price</th></tr>
            <tr><td>Hall Rent Amount</td><td class='price'>₹$hall_total</td></tr>
            $service_list
            <tr><td class='total'>Total Amount:</td><td class='total price'>₹$total_amount</td></tr>
        </table>
    </div>";

    // Generate and download PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output("Booking_$data[order_id].pdf", 'D');
} else {
    header('location: dashboard.php');
}
?>