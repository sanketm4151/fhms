<!DOCTYPE html>
<html lang="en">

<head>
  <?php require("includes/links.php"); ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $settings_r['site_title'] ?> - Facilities</title>

  <style>
    /* General Page Styling */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    /* Section Title */
    .section-title {
      font-size: 2.5rem;
      font-weight: bold;
      text-transform: uppercase;
      color: #333;
      margin-bottom: 10px;
    }

    .underline {
      width: 80px;
      height: 4px;
      background: #2ec1ac;
      margin: 0 auto;
    }

    /* Facilities Cards */
    .facility-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      height: 100%;
      padding: 20px;
      border-radius: 10px;
      background: #fff;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .facility-card:hover {
      transform: scale(1.05);
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
    }

    .facility-icon {
      width: 50px;
      height: 50px;
      object-fit: contain;
    }

    .facility-desc {
      color: #555;
      font-size: 1rem;
      line-height: 1.6;
      text-align: center;
    }

    /* Ensuring Equal Card Heights */
    .facility-wrapper {
      display: flex;
      flex-wrap: wrap;
    }

    .facility-column {
      display: flex;
      flex-grow: 1;
    }
  </style>
</head>

<body>

  <?php include("includes/header.php"); ?>

  <!-- Page Header -->
  <div class="container text-center my-5">
    <h2 class="section-title">Our Facilities</h2>
    <div class="underline"></div>
    <p class="mt-3 text-muted">
      We offer a range of top-notch facilities to ensure a comfortable and enjoyable experience for our guests.
    </p>
  </div>

  <!-- Facilities Section -->
  <div class="container">
    <div class="row facility-wrapper">
      <?php
      $res = selectAll('facilities'); 
      $path = FACILITIES_IMG_PATH;
      
      while ($row = mysqli_fetch_assoc($res)) {
        $icon = htmlspecialchars($path . $row['icon'], ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($row['desc'], ENT_QUOTES, 'UTF-8');

        echo <<<HTML
        <div class="col-lg-4 col-md-6 mb-4 facility-column">
          <div class="facility-card text-center">
            <img src="$icon" class="facility-icon mb-3" alt="$name Icon">
            <h5 class="fw-bold">$name</h5>
            <p class="facility-desc">$desc</p>
          </div>
        </div>
        HTML;
      }
      ?>
    </div>
  </div>

  <?php require("includes/footer.php"); ?>

</body>

</html>
