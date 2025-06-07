<?php
require 'includes/razorpay/config.php';
    
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

// require('includes/paytm/config_paytm.php');
// require('includes/paytm/encdec_paytm.php');

session_start();
unset($_SESSION['hall']);

function regenrate_session($uid)
{
    $user_q = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$uid], 'i');
    $user_fetch = mysqli_fetch_assoc($user_q);

    $_SESSION['login'] = true;
    $_SESSION['uId'] = $user_fetch['id'];
    $_SESSION['uName'] = $user_fetch['name'];
    $_SESSION['uPic'] = $user_fetch['profile'];
    $_SESSION['uPhone'] = $user_fetch['phonenum'];
}
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if(!empty($_POST)){
    $order_id = $_SESSION['order_id'];
    // response from razorpay
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_signature = $_POST['razorpay_signature'];
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    
    // generate server side signature
    $generated_signature = hash_hmac('sha256', $order_id . "|" . $razorpay_payment_id, API_SECRET);
    $slct_query = "SELECT 'booking_id','user_id' FROM `booking_order` 
    WHERE 'order_id'='$_SESSION[order_id]'";
    $slct_res=mysqli_query($con, $slct_query);
    $slct_fetch = mysqli_fetch_assoc($slct_res);
    if($generated_signature == $razorpay_signature){
         echo 'payment is successful';
         $upd_query="UPDATE `booking_order` SET `booking_status`='booked',`trans_id`='$razorpay_payment_id',`trans_status`='TXN_SUCCESS'
          WHERE `order_id`='$order_id'";
         mysqli_query($con, $upd_query);
    }
    else{
        echo 'payment failed';
        $upd_query = "UPDATE `booking_order` SET =`booking_status`='payment failed',
        `trans_id`='$razorpay_payment_id',`trans_status`='TXN_FAIL'
         WHERE `order_id`='$order_id'";
        mysqli_query($con, $upd_query);
    }
    redirect('pay_status.php?order=' . $order_id);
}    

?>