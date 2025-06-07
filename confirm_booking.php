<!DOCTYPE html>
<html>

<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8" />
  <title><?php echo $settings_r['site_title'] ?> - CONFIRM BOOKING</title>
  <style>
    .pop:hover {
      border-top-color: #2ec1ac !important;
      transform: scale(1.03);
      transition: all 0.3s;
    }
  </style>
</head>

<body>
  <?php include("includes/header.php"); ?>

  <?php

  /*
    Check hall id from url is present or not
    shutdown mode is active or not
    User is logged in or out
  */
  if (!isset($_GET["id"]) || $settings_r['shutdown'] == true) {
    redirect("halls.php");
  } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect("halls.php");
  }

  // filter and get hall and user data
  $data = filteration($_GET);
  $hall_res = select("SELECT * FROM `halls` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');
  if (mysqli_num_rows($hall_res) == 0) {
    redirect('halls.php');
  }
  $hall_data = mysqli_fetch_assoc($hall_res);

  $_SESSION['hall'] = [
    "id" => $hall_data['id'],
    "name" => $hall_data['name'],
    "price" => $hall_data['price'],
    "payment" => null,
    "available" => false,
  ];

  $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
  $user_data = mysqli_fetch_assoc($user_res);
  ?>

  <div class="container">
    <div class="row">
      <div class="col-12 my-5 mb-4 px-4">
        <h2 class="fw-bold">CONFIRM BOOKING</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="halls.php" class="text-secondary text-decoration-none">HALLS</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
        </div>
      </div>

      <div class="col-lg-6 col-md-12 px-6 h-100">
        <?php
        $hall_thumb = HALLS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM `hall_images` WHERE `hall_id`='$hall_data[id]' AND `thumb`=1");

        if (mysqli_num_rows($thumb_q) > 0) {
          $thumb_res = mysqli_fetch_assoc($thumb_q);
          $hall_thumb = HALLS_IMG_PATH . $thumb_res['image'];
        }
        echo <<<data
            <div class="card p-3 shadow-none rounded">
              <img src="$hall_thumb" class="img-fluid rounded mb-3">
              <h5>$hall_data[name]</h5>
              <h6>₹$hall_data[price] per day</h6>
            </div>
          data;
        ?>
      </div>

      <div class="col-lg-6 col-md-12 px-6">
        <div class="card mb-4 border-0 shadow-sm rounded-3">
          <div class="card-body">
            <!-- <p class="badge bg-danger text-start text-dark mb-1" id="note">Note: Please first ensure that the hall is
              available or not</p> -->
            <form action="pay_now.php" id="booking_form" method="POST">
              <h5 class="mb-2">BOOKING DETAILS</h5>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label class="form-label">Name</label>
                  <input type="text" name="name" value="<?php echo $user_data['name'] ?>"
                    class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Phone Number</label>
                  <input type="number" name="phonenum" value="<?php echo $user_data['phonenum'] ?>"
                    class="form-control shadow-none" oninput="limitLength1(this)" required>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="form-label">Address</label>
                  <textarea class="form-control shadow-none" name="address" rows="1"
                    required><?php echo $user_data['address'] ?></textarea>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Check-In</label>
                  <input type="date" onchange="check_availability()" name="checkin" class="form-control shadow-none"
                    required>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Check-Out</label>
                  <input type="date" onchange="check_availability()" name="checkout" class="form-control shadow-none"
                    required>
                </div>
                <div class="d-none" id="event_section">
                  <div class="col-md-12 mb-2">
                    <label class="form-label">Event Type</label>
                    <select class="form-select shadow-none" name="no_events" onchange="event_change(this.value)"
                      required>
                      <?php
                      // Fetch all events that are active and not removed
                      $events_query = mysqli_query($con, "SELECT `id`, `name` FROM `events` WHERE `status`='1' AND `removed`='0'");
                      echo "<option>Select</option>";
                      while ($event = mysqli_fetch_assoc($events_query)) {
                        echo "<option value='{$event['id']}'>{$event['name']}</option>";
                      }
                      echo "<option value='Other'>Other</option>";
                      ?>
                    </select>
                  </div>
                  <div class="col-md-12 mb-2 d-none" id="event_other">
                    <input type="text" name="event_other_type" placeholder="Mention Event type"
                      class="form-control shadow-none">
                  </div>
                  <div class="col-md-12 mb-2" id="e_services">
                    <label class="form-label d-none" id="service_label">Select Services</label>
                    <div id="services_data">
                      <!-- Services will be loaded here dynamically -->
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <h6 class="mb-3 text-danger" id="pay_info">Provide Check-In & Check-Out date!</h6>
                  <button type="submit" name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>
                    Pay now
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>

  <?php require("includes/footer.php"); ?>
  <script>
    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');
    let services_data = document.getElementById('services_data');
    //let note = document.getElementById('note');
    let event_section = document.getElementById('event_section');
    document.addEventListener('change', (event) => {
      if (event.target.classList.contains('service-checkbox')) {
        updatePayment();
      }
    });

    function check_availability() {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['pay_now'].setAttribute('disabled', true);

      if (checkin_val != '' && checkout_val != '' && services_data != '') {
        pay_info.classList.add('d-none');
        pay_info.classList.replace('text-success', 'text-danger');
        info_loader.classList.remove('d-none');

        let data = new FormData();
        data.append('check_availability', '');
        data.append('check_in', checkin_val);
        data.append('check_out', checkout_val);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/confirm_booking.php", true);

        xhr.onload = function () {
          let data = JSON.parse(this.responseText);
          if (data.status === 'check_out_earlier') {
            pay_info.innerText = "Check-out date is earlier than Check-in date!";
          }
          else if (data.status === 'check_in_earlier') {
            pay_info.innerText = "Check-in date is earlier than today's date!";
          }
          else if(data.status === 'check_in_equal'){
            pay_info.innerText = "You can't book on today!";
          }
          else if (data.status === 'unavailable') {
            pay_info.innerText = "Hall not availabel for this Check-in date!";
          }
          else {
            event_section.classList.remove('d-none');
            pay_info.innerHTML = "No. of Days: " + data.days + "<br>Total Amount to Pay: ₹" + data.payment;
            pay_info.classList.replace('text-danger', 'text-success');
            booking_form.elements['pay_now'].removeAttribute('disabled');
           // note.classList.add('d-none');
          }

          pay_info.classList.remove('d-none');
          info_loader.classList.add('d-none');
        }
        xhr.send(data);
      }
    }

    function event_change(event_id) {
      const servicesData = document.getElementById('services_data');
      const eventOther = document.getElementById('event_other');
      const serviceLabel = document.getElementById('service_label');
      check_availability();
      if (event_id === "Other") {
        eventOther.classList.remove('d-none'); // Show the "Other" input field
        serviceLabel.classList.remove('d-none'); // Hide the "Service" label
        fetchAllServices(); // Fetch all services
        return;
      } else {
        eventOther.classList.add('d-none'); // Hide the "Other" input field
        serviceLabel.classList.remove('d-none'); // Show the "Service" label
      }
      // Fetch services for the selected event
      fetch('ajax/confirm_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `event_id=${event_id}`
      })
        .then(response => response.text())
        .then(data => {
          servicesData.innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    }

    function fetchAllServices() {
      const servicesData = document.getElementById('services_data');
      fetch('ajax/confirm_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'fetch_all=true'
      })
        .then(response => response.text())
        .then(data => {
          servicesData.innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    }

    function updatePayment() {
      let selectedServices = [];
      document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
        selectedServices.push(checkbox.value);
      });

      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      if (!checkin_val || !checkout_val) {
        pay_info.innerHTML = "Provide Check-In & Check-Out date!";
        return;
      }

      let data = new FormData();
      data.append('calculate_payment', 'true');
      data.append('check_in', checkin_val);
      data.append('check_out', checkout_val);
      data.append('services', JSON.stringify(selectedServices));
      fetch('ajax/confirm_booking.php', {
        method: 'POST',
        body: data
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            event_section.classList.remove('d-none');
            pay_info.innerHTML = `No. of Days: ${data.days} <br>Total Amount to Pay: ₹${data.payment}`;
            pay_info.classList.replace('text-danger', 'text-success');
            booking_form.elements['pay_now'].removeAttribute('disabled');
            //note.classList.add('d-none');
          } else {
            event_section.classList.remove('d-none');
            pay_info.innerHTML = data.message;
            pay_info.classList.replace('text-success', 'text-danger');
            booking_form.elements['pay_now'].setAttribute('disabled', true);
            //note.classList.add('d-none');
          }
        })
        .catch(error => console.error('Error:', error));
    }

  </script>


</body>

</html>