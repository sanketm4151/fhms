<!DOCTYPE html>
<html lang="en">
<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title><?php echo $settings_r['site_title'] ?> - About</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .box {
      border-top: 4px solid #2ec1ac !important;
      transition: transform 0.3s ease-in-out;
    }
    .box:hover {
      transform: scale(1.05);
    }
    .swiper-button-next, .swiper-button-prev {
      color: #2ec1ac;
    }
    .team-img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <?php include("includes/header.php"); ?>

  <div class="container text-center my-5">
    <h2 class="fw-bold">About Us</h2>
    <hr class="w-25 mx-auto border-2 border-dark">
  </div>

  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h3><?php echo $settings_r['site_title']; ?></h3>
        <p><?php echo $settings_r['site_about']; ?></p>
      </div>
      <div class="col-lg-6 text-center">
        <img src="img/about.jpeg" class="img-fluid rounded shadow-lg" height="300">
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <div class="row g-4">
      <?php
      $stats = [
        ["icon" => "img/halls/img-1.png", "query" => "SELECT COUNT(*) AS count FROM `halls` WHERE `removed`=0", "label" => "Halls"],
        ["icon" => "img/halls/img-2.png", "query" => "SELECT COUNT(*) AS count FROM `user_cred`", "label" => "Users"],
        ["icon" => "img/halls/img-3.png", "query" => "SELECT COUNT(*) AS count FROM `rating_review`", "label" => "Reviews"],
        ["icon" => "img/halls/img-4.png", "query" => "SELECT COUNT(*) AS count FROM `team_details`", "label" => "Staffs"]
      ];
      foreach ($stats as $stat) {
        $query = mysqli_query($con, $stat["query"]);
        $data = mysqli_fetch_assoc($query);
        $count = $data["count"];
        echo <<<HTML
          <div class="col-lg-3 col-md-6">
            <div class="bg-white rounded shadow p-4 text-center box">
              <img src="{$stat['icon']}" width="70">
              <h4 class="mt-3">{$count}+ {$stat['label']}</h4>
            </div>
          </div>
        HTML;
      }
      ?>
    </div>
  </div>

  <div class="container text-center my-5">
    <h3 class="fw-bold">OUR STAFF</h3>
    <div class="swiper teamSwiper">
      <div class="swiper-wrapper">
        <?php
        $team_members = selectAll('team_details');
        $path = ABOUT_IMG_PATH;
        foreach ($team_members as $member) {
          echo <<<HTML
          <div class="swiper-slide text-center p-3">
            <div class="card border-0 shadow-lg">
              <img src="{$path}{$member['picture']}" class="team-img">
              <div class="card-body">
                <h5 class="fw-bold">{$member['name']}</h5>
                <p class="text-muted"><i class="bi bi-telephone-fill"></i> {$member['number']}</p>
                <span class="badge bg-success">{$member['department']}</span>
              </div>
            </div>
          </div>
          HTML;
        }
        ?>
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </div>

  <?php require("includes/footer.php"); ?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    var swiper = new Swiper(".teamSwiper", {
      slidesPerView: 1,
      spaceBetween: 20,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
      }
    });
  </script>
</body>
</html>
