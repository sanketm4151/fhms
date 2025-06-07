<!DOCTYPE html>
<html>

<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8"/>
  <title><?php echo $settings_r['site_title'] ?> - Booking Status</title>
  <style>
    .pop:hover {
      border-top-color: #2ec1ac !important;
      transform: scale(1.03);
      transition: all 0.3s;
    }
  </style>
</head>

<body class="bg-light">
  <?php include("includes/header.php"); ?>

  <div class="container">
    <div class="row">
      <div class="col-12 my-5 mb-3 px-4">
        <h2 class="fw-bold">PAYMENT STATUS</h2>
      </div>
      <?php
      $frm_data = filteration($_POST);
      if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('index.php');
      }

      $booking_q = "SELECT bo.*,bd.* FROM `booking_order` bo 
         INNER JOIN `booking_details` bd ON bo.booking_id=bd.booking_id 
         WHERE bo.order_id=? AND bo.user_id=? AND bo.booking_status!=?";
      $booking_res = select($booking_q, [$_GET['order'], $_SESSION['uId'], 'pending'], 'sis');
      $booking_fetch = mysqli_fetch_assoc($booking_res);
      if ($booking_fetch['trans_status'] == "TXN_SUCCESS") {
        echo <<<data
           <div class="col-12 px-4">
           <p class="fw-bold alert-success">
              <i class="bi bi-check-circle-fill"></i>
              Payment done! Booking successful.
              <br><br>
              <a href='bookings.php'>Go to Booking</a>
           </p>
           </div>
           data;
      } else {
        echo <<<data
            <div class="col-12 px-4">
            <p class="fw-bold alert-danger">
              <i class="bi bi-exclamation-triangle"></i>
               Payment Failed! $booking_fetch[trans_resp_msg]
               <br><br>
               <a href='bookings.php'>Go to Booking</a>
            </p>
            </div>
            data;
      }

      ?>
    </div>

    <div class="col-lg-7 col-md-12 px-4">
      <?php
      $hall_id = htmlspecialchars($booking_fetch['hall_id'] ?? 0);
      $hall_name = htmlspecialchars($booking_fetch['hall_name'] ?? 'Unknown Hall');
      $hall_price = htmlspecialchars($booking_fetch['price'] ?? '0');
      $hall_thumb = HALLS_IMG_PATH . "thumbnail.jpg";
      $thumb_q = mysqli_query($con, "SELECT * FROM `hall_images` WHERE `hall_id`='$hall_id' AND `thumb`=1");

      if (mysqli_num_rows($thumb_q) > 0) {
        $thumb_res = mysqli_fetch_assoc($thumb_q);
        $hall_thumb = HALLS_IMG_PATH . $thumb_res['image'];
      }
      echo <<<data
           <div class="card p-3 shadow-sm rounded">
           <img src="$hall_thumb" class="img-fluid rounded mb-3">
           <h5>$hall_name</h5>
           <h5>₹$hall_price per day</h5>
           </div>

          data;
      ?>
    </div>

  </div>
  </div>

  <?php require("includes/footer.php"); ?>
</body>

</html>