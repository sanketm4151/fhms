<!DOCTYPE html>
<html>

<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8"/>
  <title><?php echo $settings_r['site_title'] ?> - Contact</title>
  <!-- <style>
    .pop:hover {
      border-top-color: #2ec1ac !important;
      transform: scale(1.03);
      transition: all 0.3s;
    }
  </style> -->
</head>

<body>
  <?php include("includes/header.php"); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">Contact Us</h2>
    <div class="h-line bg-dark"> </div>
    <p class="text-center mt-3">
      Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad fuga sapiente minus pariatur,<br>
      quis maiores atque saepe ducimus rerum natus quae nesciunt? Quo similique officia maxime temporibus rem fugiat
      sit?
    </p>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 mb-5 px-4">
        <div class="bg-white rounded shadow p-4">
          <iframe class="w-100 rounded mb-4" src="<?php echo $contact_r['iframe'] ?>"></iframe>
          <h5>Address</h5>
          <a href="<?php echo $contact_r['gmap'] ?>" target="_blank"
            class="d-inline-block text-decoration-none text-dark mb-2">
            <i class="bi bi-geo-alt-fill"></i> <?php echo $contact_r['address'] ?>
          </a>

          <h5 class="mt-4">Call Us</h5>
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

          <h5 class="mt-4">Email</h5>
          <a href="mailto: <?php echo $contact_r['email'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
            <i class="bi bi-envelope-fill"></i> <?php echo $contact_r['email'] ?>
          </a>

          <h5 class="mt-4">Follow Us</h5>
          <?php
          if (trim($contact_r['yt']) != "") {
            $youtube_link = trim($contact_r['yt']); // Process the variable before the heredoc
            echo <<<DATA
              <a href="$youtube_link" class="d-inline-block text-dark fs-5 me-2" target="_blank">
                <span class="badge bg-light text-dark fs-6 p-2">
                  <i class="bi bi-youtube me-1"></i> 
                </span>
              </a>
            DATA;
          }
          ?>
          <?php
          if (trim($contact_r['fb']) != "") {
            $facebook_link = trim($contact_r['fb']); // Process the variable before the heredoc
            echo <<<DATA
              <a href="$facebook_link" class="d-inline-block text-dark fs-5 me-2" target="_blank">
                <span class="badge bg-light text-dark fs-6 p-2">
                  <i class="bi bi-facebook me-1"></i> 
                </span>
              </a>
            DATA;
          }
          ?>
          <?php
          if (trim($contact_r['insta']) != "") {
            $instagram_link = trim($contact_r['insta']); // Process the variable before the heredoc
            echo <<<DATA
              <a href="$instagram_link" class="d-inline-block text-dark fs-5 me-2" target="_blank">
                <span class="badge bg-light text-dark fs-6 p-2">
                  <i class="bi bi-instagram me-1"></i> 
                </span>
              </a>
            DATA;
          }
          ?>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 mb-5 px-4">
        <div class="bg-white rounded shadow p-4">
          <form method="POST">
            <h5>Send a message</h5>
            <div class="mb-4">
              <label class="form-label">Name</label>
              <input type="text" class="form-control shadow-none" name="name" required>
            </div>
            <div class="mb-4">
              <label class="form-label">Email</label>
              <input type="text" class="form-control shadow-none" name="email" required>
            </div>
            <div class="mb-4">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control shadow-none" name="subject" required>
            </div>
            <div class="mb-4">
              <label class="form-label">Message</label>
              <textarea class="form-control shadow-none" rows="4" name="message" required></textarea>
            </div>
            <button type="submit" class="btn text-white custom-bg mt-4" name="send">SEND</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php

    if(isset($_POST['send'])){
      $frm_data = filteration($_POST);

      $q = "INSERT INTO `user_queries`(`name`, `email`, `subject`, `message`) VALUES (?,?,?,?)";
      $values = [$frm_data['name'],$frm_data['email'],$frm_data['subject'],$frm_data['message']];

      $res = insert($q,$values,'ssss');
      if($res == 1){
        alert('success','Mail sent!');
      }
      else{
        alert('error','Server Down! Try again later.');
      }
    }
  
  ?>

  <?php require("includes/footer.php"); ?>

</body>

</html>