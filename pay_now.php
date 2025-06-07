<?php
require 'includes/razorpay/config.php';
require 'includes/razorpay/vendor/autoload.php';
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

use Razorpay\Api\Api;

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if (isset($_POST['pay_now'])) {
    $api = new Api(API_KEY, API_SECRET);
    $CUST_ID = $_SESSION['uId'];
    $TXN_AMOUNT = $_SESSION['hall']['payment'];
    try {
        $res = $api->order->create(
            array(
                'receipt' => '123',
                'amount' => $TXN_AMOUNT * 100, // Convert to smallest currency unit (paise)
                'currency' => 'INR',
                'payment_capture' => 1 // Auto-capture
            )
        );
        if (!empty($res['id'])) {
            $_SESSION['order_id'] = $res['id'];
            $ORDER_ID = $res['id'];
            ?>
            <form action="pay_response.php" method="POST">
                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="<?php echo API_KEY ?>"
                    data-amount="<?php echo $TXN_AMOUNT; ?>" data-currency="INR" data-order_id="<?php echo $res['id']; ?>"
                    data-buttontext="Pay <?php echo $TXN_AMOUNT; ?> with Razorpay" data-name="<?php echo COMPANY_NAME; ?>"
                    data-description="Payment for <?php echo COMPANY_NAME; ?>" data-prefill.name="<?php echo $name; ?>"
                    data-prefill.email="<?php echo $email; ?>" data-theme .color="#F2994A">
                    </script>
                <input type="hidden" custom="Hidden Element" name="hidden" />
            </form>
            <?php
        }
        // Insert payment data into database
        $frm_data = filteration($_POST);
        $services_list = isset($_SESSION['services']) ? json_decode($_SESSION['services'], true) : [];
        $services_string = is_array($services_list) ? implode(',', $services_list) : '';
        $query1 = "INSERT INTO `booking_order`(`user_id`, `hall_id`, `check_in`, `check_out`,`event_type`, `order_id`, `trans_amt`,`services`) VALUES (?,?,?,?,?,?,?,?)";
        insert($query1, [$CUST_ID, $_SESSION['hall']['id'], $frm_data['checkin'], $frm_data['checkout'], $frm_data['no_events'], $ORDER_ID, $TXN_AMOUNT, $services_string], 'isssssis');

        $booking_id = mysqli_insert_id($con);

        $query2 = "INSERT INTO `booking_details`(`booking_id`, `hall_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
        insert($query2, [$booking_id, $_SESSION['hall']['name'], $_SESSION['hall']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']], 'issssss');

        // Insert into user_services table

    } catch (Exception $e) {
        echo 'Razorpay Error: ' . $e->getMessage();
        exit;
    }
}

?>