<!DOCTYPE html>
<html>

<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8"/>
  <title><?php echo $settings_r['site_title'] ?> - Halls</title>
  <style>
    .pop:hover {
      border-top-color: #2ec1ac !important;
      transform: scale(1.03);
      transition: all 0.3s;
    }
  </style>
</head>

<body>
  <?php
  include("includes/header.php");
  $checkin_default = "";
  $checkout_default = "";
  $no_guests_default = "";
  if (isset($_GET['check_availability'])) {
    $frm_data = filteration($_GET);
    $checkin_default = $frm_data['checkin'];
    $checkout_default = $frm_data['checkout'];
    $no_guests_default = $frm_data['no_guests'];
  }
  ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">OUR HALLS</h2>
    <div class="h-line bg-dark">

    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
          <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="d-flex align-items-center justify-content-between mt-3">FILTERS
              <button id="filter_btn" class="btn btn-sm text-secondary d-none shadow-none"
                onclick="filter_clear()">RESET</button>
            </h4>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown"
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">

              <!-- Check availability -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size:18px;">
                  <span>CHECK AVAILABILITY</span>
                  <button id="chk_avail_btn" class="btn btn-sm text-secondary d-none shadow-none"
                    onclick="chk_avail_clear()">Reset</button>
                </h5>
                <label class="form-label">Check-in</label>
                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkin_default ?>"
                  id="checkin" onchange="chk_avail_filter()">
                <label class="form-label">Check-out</label>
                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkout_default ?>"
                  id="checkout" onchange="chk_avail_filter()">
                <!-- <h6 class="mb-3 text-danger" id="pay_info">Provide Check-In & Check-Out date!</h6> -->
              </div>

              <!-- Facilities -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size:18px;">
                  <span>FACILITIES</span>
                  <button id="facilities_btn" class="btn btn-sm text-secondary d-none shadow-none"
                    onclick="facilities_clear()">Reset</button>
                </h5>
                <?php
                $facilities_q = selectAll('facilities');
                while ($row = mysqli_fetch_assoc($facilities_q)) {
                  echo <<<facilities
                    <div class='mb-2'>
                      <input type='checkbox' name='facilities' value='$row[id]' onclick='fetch_halls()' class='form-check-input shadow-none me-1' id='$row[id]'>
                      <label class='form-check-label' for='$row[id]'>$row[name]</label>
                    </div>
                  facilities;
                }
                ?>
              </div>

              <!-- Guests -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size:18px;">
                  <span>GUESTS</span>
                  <button id="guest_btn" class="btn btn-sm text-secondary d-none shadow-none"
                    onclick="guests_clear()">Reset</button>
                </h5>
                <div class="d-flex">
                  <div class="me-3">
                    <label class="form-label">No. of Guests</label>
                    <input type="number" min="1" value="<?php echo $no_guests_default ?>" id="no_guests"
                      class="form-control shadow-none" oninput="guests_filter()">
                  </div>
                </div>
              </div>

            </div>
          </div>
        </nav>
      </div>

      <div class="col-lg-9 col-md-12 px-4" id="halls-data">

      </div>

    </div>
  </div>

  <script>
    let halls_data = document.getElementById('halls-data');

    let filter_btn = document.getElementById('filter_btn');

    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let chk_avail_btn = document.getElementById('chk_avail_btn');
    //let pay_info = document.getElementById('pay_info');

    let no_guests = document.getElementById('no_guests');
    let guest_btn = document.getElementById('guest_btn');

    let facilities_btn = document.getElementById('facilities_btn');


    function fetch_halls() {
      let chk_avail = JSON.stringify({
        checkin: checkin.value,
        checkout: checkout.value
      });

      let guests = JSON.stringify({
        no_guests: no_guests.value
      });

      let facility_list = { "facilities": [] };

      let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
      if (get_facilities.length > 0) {
        get_facilities.forEach((facility) => {
          facility_list.facilities.push(facility.value);
        });
        facilities_btn.classList.remove('d-none');
        filter_btn.classList.remove('d-none');
      }
      else {
        facilities_btn.classList.add('d-none');
      }
      facility_list = JSON.stringify(facility_list);

      let xhr = new XMLHttpRequest();
      xhr.open('GET', 'ajax/halls.php?fetch_halls&chk_avail=' + chk_avail + '&guests=' + guests + '&facility_list=' + facility_list, true);
      xhr.onprogress = function () {
        halls_data.innerHTML = `<div class="spinner-border text-info mb-3 d-block mx-auto" id="loader" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>`;
      }
      xhr.onload = function () {
        halls_data.innerHTML = this.responseText;
      }
      xhr.send();
    }

    function chk_avail_filter() {
      if (checkin.value != '' && checkout.value != '') {
        //pay_info.classList.add('d-none');
        fetch_halls();
        chk_avail_btn.classList.remove('d-none');
        filter_btn.classList.remove('d-none');
      }
      else{
        filter_btn.classList.add('d-none');
      }
    }

    function chk_avail_clear() {
      checkin.value = "";
      checkout.value = "";
      chk_avail_btn.classList.add('d-none');
      //pay_info.classList.remove('d-none');
      fetch_halls();
    }

    function guests_filter() {
      if (no_guests.value > 0) {
        fetch_halls();
        guest_btn.classList.remove('d-none');
        filter_btn.classList.remove('d-none');
      }
      else{
        filter_btn.classList.add('d-none');
      }
    }

    function guests_clear() {
      no_guests.value = "";
      guest_btn.classList.add('d-none');
      fetch_halls();
    }

    function facilities_clear() {
      let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
      get_facilities.forEach((facility) => {
        facility.checked = false;
      });
      facilities_btn.classList.add('d-none');
      fetch_halls();
    }

    function filter_clear() {
      checkin.value = "";
      checkout.value = "";
      no_guests.value = "";
      let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
      chk_avail_btn.classList.add('d-none');
      //pay_info.classList.remove('d-none');
      guest_btn.classList.add('d-none');
      get_facilities.forEach((facility) => {
        facility.checked = false;
      });
      facilities_btn.classList.add('d-none');
      filter_btn.classList.add('d-none');
      fetch_halls();
    }

    window.onload = function () {
      fetch_halls();
    }

  </script>
  <?php require("includes/footer.php"); ?>

</body>

</html>