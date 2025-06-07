<!DOCTYPE html>
<html>

<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8"/>
  <title><?php echo $settings_r['site_title'] ?> - HOME</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
    .availablity-form {
      margin-top: -40px;
      z-index: 2;
      position: relative;
    }

    @media screen and(max-width: 575px) {
      .availablity-form {
        margin-top: 25px;
        padding: 0 35px;
      }
    }
  </style>
</head>

<body class="bg-light">
  <?php include("includes/header.php"); ?>

  <!-- Carousel -->
  <div class="container-fluid px-lg-4 mt-4">
    <div class="swiper swiper-container">
      <div class="swiper-wrapper">
        <?php
        $res = selectAll('carousel');
        while ($row = mysqli_fetch_assoc($res)) {
          $path = CAROUSEL_IMG_PATH;
          echo <<<data
            
            <div class="swiper-slide">
              <img src="$path$row[image]" class="w-100 d-block" height="420px">
            </div>
          data;
        }
        ?>
      </div>
    </div>
  </div>

  <!-- check availability form -->
  <div class="container availablity-form rounded-pill">
    <div class="row">
      <div class="col-lg-12 bg-white shadow p-4 rounded">
        <h5 class="mb-4">Check Booking availability</h5>
        <form action="halls.php" method="GET">
          <div class="row align-items-end">
            <div class="col-lg-4 mb-3">
              <label class="form-label" style="font-weight:500;">Check-in</label>
              <input type="date" class="form-control shadow-none" name="checkin" required>
            </div>
            <div class="col-lg-4 mb-3">
              <label class="form-label" style="font-weight:500;">Check-out</label>
              <input type="date" class="form-control shadow-none" name="checkout" required>
            </div>
            <div class="col-lg-3 mb-3">
              <label class="form-label" style="font-weight:500;">No. of Guests</label>
              <select class="form-select shadow-none" name="no_guests">
                <?php 
                  $guests_q = mysqli_query($con,"SELECT MAX(guests) AS `max_guests` FROM `halls` WHERE `status`='1' AND `removed`= '0'");
                  $guests_res = mysqli_fetch_assoc($guests_q);
                  for($i=1; $i<=$guests_res['max_guests']; $i++){
                    echo "<option value='$i'>$i</option>";
                  }
                ?>
              </select>
            </div>
            <input type="hidden" name="check_availability">
            <div class="col-lg-1 mb-lg-3 mt-2">
              <button type="submit" class="btn text-white shadow-none custom-bg">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!--Our Halls-->
  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold g-font">OUR HALLS</h2>
  <div class="container">
    <div class="row">
      <?php
      $hall_res = select("SELECT * FROM `halls` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 3", [1, 0], 'ii');
      while ($hall_data = mysqli_fetch_assoc($hall_res)) {
        //get Features of Hall
        $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
          INNER JOIN `hall_feature` hfea ON f.id=hfea.features_id
          WHERE hfea.hall_id='$hall_data[id]'");

        $features_data = "";
        while ($fea_row = mysqli_fetch_assoc($fea_q)) {
          $features_data .= "<span class='badge rounded-pill bg-light text-dark text-weap  me-1 mb-1'>
                $fea_row[name]
              </span>";
        }
        //get facilities of hall
      
        $fac_q = mysqli_query($con, "SELECT f.name FROM `facilities` f
           INNER JOIN `hall_facilities` hfac ON f.id=hfac.facilities_id
           WHERE hfac.hall_id='$hall_data[id]'");

        $facilities_data = "";
        while ($fac_row = mysqli_fetch_assoc($fac_q)) {
          $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-weap  me-1 mb-1'>
                 $fac_row[name]
               </span>";

        }
        //get thumbnail of img
        $hall_thumb = HALLS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM `hall_images` WHERE `hall_id`='$hall_data[id]' AND `thumb`=1");

        if (mysqli_num_rows($thumb_q) > 0) {
          $thumb_res = mysqli_fetch_assoc($thumb_q);
          $hall_thumb = HALLS_IMG_PATH . $thumb_res['image'];
        }

        $book_btn = "";
        if (!$settings_r['shutdown']) {
          $login = 0;
          if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
            $login = 1;
          }
          $book_btn = "<button onclick='checkLoginToBook($login,$hall_data[id])' class='btn btn-sm text-white custom-bg shadow-none'>Book Now</button>";
        }

        $rating_q = "SELECT AVG(rating) AS 'avg_rating' FROM `rating_review` WHERE `hall_id`='$hall_data[id]' ORDER BY `sr_no` DESC LIMIT 20";
        $rating_res = mysqli_query($con, $rating_q);
        $rating_fetch = mysqli_fetch_assoc($rating_res);
        $rating_data = "";
        if ($rating_fetch['avg_rating'] != NULL) {
            $rating_data = "<div class='rating mb-4'>
                  <h6 class='mb-1;>Rating</h6>
                  <span class='badge rounded-pill bg-light'>"; 
            for($i=1; $i<=$rating_fetch['avg_rating']; $i++) {
              $rating_data .= "<i class='bi bi-star-fill text-warning'></i> ";
            }
            $rating_data .= "</span>
            </div>";
        } 

        //print hall card
        echo <<<data
          <div class="col-lg-4 col-md-6 my-3">
            <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
              <img src="$hall_thumb" class="card-img-top" height="200px" width="100%">
              <div class="card-body">
                <h4 class="mb-4">$hall_data[name]</h4>
                <h6 class="mb-4">₹$hall_data[price] per Day</h6>
                <div class="features mb-4">
                  <h6 class="mb-1">Features</h6>
                  $features_data
                </div>
                <div class="facilities mb-4">
                  <h6 class="mb-1">Facilities</h6>
                  $facilities_data
                </div>
                <div class="guests mb-4">
                  <h6 class="mb-1">Guests</h6>
                  <span class="badge rounded-pill bg-light text-dark text-wrap">
                    $hall_data[guests]
                  </span>
                </div>
                $rating_data
                <div class="d-flex justify-content-evenly mb-2">
                  $book_btn
                  <a href="hall_details.php?id=$hall_data[id]" class="btn btn-sm btn-outline-dark shadow-none">More details</a>
                </div>
              </div>
            </div>
          </div>
        data;
      }
      ?>
      <div class="col-lg-12 text-center mt-5">
        <a href="halls.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Halls >>></a>
      </div>
    </div>
  </div>

  <!-- Our facilities -->
  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold g-font">OUR FACILITIES</h2>
  <div class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
      <?php
      $res = mysqli_query($con, "SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 5"); // Ensure this function is defined and works properly
      $path = FACILITIES_IMG_PATH; // Ensure this constant is defined and has the correct value
      
      while ($row = mysqli_fetch_assoc($res)) {
        $icon = htmlspecialchars($path . $row['icon'], ENT_QUOTES, 'UTF-8'); // Sanitize URL
        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); // Sanitize output
        $desc = htmlspecialchars($row['desc'], ENT_QUOTES, 'UTF-8'); // Sanitize description
      
        echo <<<HTML
        <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
          <img src="$icon" width="60px">
          <h5 class="mt-3">$name</h5>
        </div>
        HTML;
      }
      ?>
      <div class="col-lg-12 text-center mt-5">
        <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Facilities
          >>></a>
      </div>
    </div>
  </div>

  <!-- Our Testimonials -->
  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold g-font">TESTIMONIALS</h2>
  <div class="container mt-5">
    <div class="swiper swiper-testimonials">
      <div class="swiper-wrapper mb-5">
        <?php
        $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, h.name AS hname FROM `rating_review` rr
          INNER JOIN `user_cred` uc ON rr.user_id = uc.id
          INNER JOIN `halls` h ON rr.hall_id = h.id
          ORDER BY `sr_no` DESC LIMIT 6";

        $review_res = mysqli_query($con, $review_q); // Ensure this function is defined
        $img_path = USERS_IMG_PATH;
        if (mysqli_num_rows($review_res) == 0) {
          echo "No reviews yet";
        } else {
          while ($row = mysqli_fetch_assoc($review_res)) {
            $stars = "<i class='bi bi-star-fill text-warning'></i> ";
            for ($i = 1; $i < $row['rating']; $i++) {
              $stars .= " <i class='bi bi-star-fill text-warning'></i>";
            }
            echo <<<slides
              <div class="swiper-slide bg-white p-4">
                <div class="profile d-flex align-items-center mb-3">
                  <img src="$img_path$row[profile]" width="30px" loading="lazy" class="rounded-circle">
                  <h6 class="m-0 ms-2">$row[uname]</h6>
                </div>
                <p>
                  $row[review]
                </p>
                <div class="rating">
                  $stars
                </div>
              </div>
            slides;
          }
        }
        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <div class="col-lg-12 text-center mt-5">
      <a href="about.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Know More >>></a>
    </div>
  </div>

  <!-- Reach Us -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold g-font">REACH US</h2>
  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-md-8 mb-lg-0 mb-3 bg-white roundeds">
        <iframe class="w-100" height="320px" src="<?php echo $contact_r['iframe']; ?>" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="bg-white p-4 rounded mb-4">
          <h5>Call us</h5>
          <a href="tel: +<?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
            <i class="bi bi-telephone-fill"></i> +<?php echo $contact_r['pn1'] ?>
          </a>
          <br>
          <?php
          if (trim($contact_r['pn2']) != "") { // Using trim to handle possible spaces
            echo <<<DATA
              <a href="tel:+{$contact_r['pn2']}" class="d-inline-block mb-2 text-decoration-none text-dark">
                <i class="bi bi-telephone-fill"></i> +{$contact_r['pn2']}
              </a>
            DATA;
          }
          ?>
        </div>
        <div class="bg-white p-4 rounded mb-4">
          <h5>Follow us</h5>
          <?php
          if (trim($contact_r['yt']) != "") {
            $youtube_link = trim($contact_r['yt']); // Process the variable before the heredoc
            echo <<<DATA
              <a href="$youtube_link" class="d-inline-block mb-3" target="_blank">
                <span class="badge bg-light text-dark fs-6 p-2">
                  <i class="bi bi-youtube me-1"></i> Youtube
                </span>
              </a>
            DATA;
          }
          ?>
          <br>
          <?php
          if (trim($contact_r['fb']) != "") {
            $facebook_link = trim($contact_r['fb']); // Process the variable before the heredoc
            echo <<<DATA
              <a href="$facebook_link" class="d-inline-block mb-3" target="_blank">
                <span class="badge bg-light text-dark fs-6 p-2">
                  <i class="bi bi-facebook me-1"></i> Facebook
                </span>
              </a>
            DATA;
          }
          ?>
          <!-- <a href="<?php echo trim($contact_r['fb']) ?>" class="d-inline-block mb-3" target="_blank">
            <span class="badge bg-light text-dark fs-6 p-2">
              <i class="bi bi-facebook me-1"></i> Facebook
            </span>
          </a> -->
          <br>
          <?php
          if (trim($contact_r['insta']) != "") {
            $instagram_link = trim($contact_r['insta']); // Process the variable before the heredoc
            echo <<<DATA
              <a href="$instagram_link" class="d-inline-block mb-3" target="_blank">
                <span class="badge bg-light text-dark fs-6 p-2">
                  <i class="bi bi-instagram me-1"></i> Instagram
                </span>
              </a>
            DATA;
          }
          ?>
          <!-- <a href="<?php echo trim($contact_r['insta']) ?>" class="d-inline-block" target="_blank">
            <span class="badge bg-light text-dark fs-6 p-2">
              <i class="bi bi-instagram me-1"></i> Instagram
            </span>
          </a> -->
        </div>
      </div>
    </div>
  </div>

  <!-- Password reset Modal -->

  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="recovery-form" method="POST">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center"><i class="bi bi-shield-lock fs-3 me-2"></i> Set up New
              Password
            </h5>
          </div>
          <div class="modal-body">
            <div id="recovery-alert"></div>
            <div class="mb-4">
              <label class="form-label">New Password</label>
              <input type="password" name="pass" class="form-control shadow-none" required>
              <input type="hidden" name="email">
              <input type="hidden" name="token">
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <button type="submit" class="btn btn-dark shadow-none">SUBMIT</button>
              <button type="button" class="btn text-secondary shadow-none ms-2" data-bs-dismiss="modal">CANCEL</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require("includes/footer.php"); ?>

  <?php
  if (isset($_GET['account_recovery'])) {
    $data = filteration($_GET);

    $t_date = date("Y-m-d");

    $query = select("SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1", [$data['email'], $data['token'], $t_date], 'sss');

    if (mysqli_num_rows($query) == 1) {
      echo <<<showModal
          <script>
            var myModal = document.getElementById("recoveryModal");
            myModal.querySelector("input[name='email']").value = '$data[email]';
            myModal.querySelector("input[name='token']").value = '$data[token]';          

            var modal = bootstrap.Modal.getOrCreateInstance(myModal);
            modal.show();
          </script>
        showModal;
    } else {
      alert("error", "Invalid or Expired Link!");
    }
  }
  ?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    var swiper = new Swiper(".swiper-container", {
      spaceBetween: 30,
      effect: "fade",
      loop: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
    });

    var swiper = new Swiper(".swiper-testimonials", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      slidesPerView: "3",
      loop: true,
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: false,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });

    // recover account
    let recovery_form = document.getElementById('recovery-form');
    recovery_form.addEventListener('submit', (e) => {
      e.preventDefault();
      let data = new FormData();
      data.append('email', recovery_form.elements['email'].value);
      data.append('token', recovery_form.elements['token'].value);
      data.append('pass', recovery_form.elements['pass'].value);
      data.append('recover_user', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/login_register.php", true);

      xhr.onload = function () {
        if (this.responseText == 'failed') {
          alert('error', 'Account reset failed!', 'recovery-alert');
        }
        else {
          if (this.responseText == 1) {
            var myModal = document.getElementById("recoveryModal");
            var modal = bootstrap.Modal.getInstance(myModal);
            modal.hide();
            alert('success', 'Account Reset Successful!');
          }
        }
      }
      recovery_form.reset();
      xhr.send(data);
    });

  </script>
</body>

</html>