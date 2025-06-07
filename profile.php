<!DOCTYPE html>
<html>

<head>
  <?php require("includes/links.php"); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8"/>
  <title><?php echo $settings_r['site_title'] ?> - PROFILE</title>
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
  require("includes/header.php");
  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect("index.php");
  }

  $u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], 'i');
  if (mysqli_num_rows($u_exist) == 0) {
    redirect("index.php");
  }
  $u_fetch = mysqli_fetch_assoc($u_exist);
  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">PROFILE</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">PROFILE</a>
        </div>
      </div>

      <div class="col-6 mb-5 px-6">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm">
          <form id="info-form">
            <h5 class="mb-5 fw-bold">Basic Information</h5>
            <div class="row mb-3">
              <div class="col-md-4 mb-5">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="<?php echo $u_fetch['name'] ?>" class="form-control shadow-none"
                  required>
              </div>
              <div class="col-md-4 mb-5">
                <label class="form-label">Phone Number</label>
                <input type="number" name="phonenum" value="<?php echo $u_fetch['phonenum'] ?>"
                  class="form-control shadow-none" oninput="limitLength1(this)" required>
              </div>
              <div class="col-md-4 mb-5">
                <label class="form-label">Date of birth</label>
                <input type="date" name="dob" value="<?php echo $u_fetch['dob'] ?>" class="form-control shadow-none"
                  required>
              </div>
              <div class="col-md-8 mb-5">
                <label class="form-label">Address</label>
                <textarea class="form-control shadow-none" name="address" rows="1"
                  required><?php echo $u_fetch['address'] ?></textarea>
              </div>
              <div class="col-md-4 mb-6">
                <label class="form-label">Pincode</label>
                <input type="number" name="pincode" value="<?php echo $u_fetch['pincode'] ?>"
                  class="form-control shadow-none" oninput="limitLength2(this)" required>
              </div>

            </div>
            <button type="submit" class="btn text-white custom-bg shadow-none">SAVE CHANGES</button>
          </form>
        </div>
      </div>

      <div class="col-md-3 mb-5 px-4">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm">
          <form id="profile-form">
            <h5 class="mb-3 fw-bold">Picture</h5>
            <center><img src="<?php echo USERS_IMG_PATH . $u_fetch['profile'] ?>" class="img-fluid rounded-circle" width="150px" height="110px"></center><br>
            <label class="form-label mb-1">New Picture</label>
            <input type="file" name="profile" accept=".jpg, .jpeg, .png, .webp" class="mb-4 form-control shadow-none"
              required>
            <button type="submit" class="btn text-white custom-bg shadow-none">UPDATE</button>
          </form>
        </div>
      </div>

      <div class="col-md-3 mb-5 px-4">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm">
          <form id="pass-form">
            <h5 class="mb-5 fw-bold">Change Password</h5>
              <div class="col-md-12 mb-5">
                <label class="form-label">New Password</label>
                <input name="new_pass" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-md-12 mb-5">
                <label class="form-label">Confirm Password</label>
                <input name="confirm_pass" type="password" class="form-control shadow-none" required>
              </div>
            
            <button type="submit" class="btn text-white custom-bg shadow-none mb-3">UPDATE</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  <?php require("includes/footer.php"); ?>

  <script>
    function alertP(type, msg, position = 'body') {
    let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
    let element = document.createElement('div');
    element.innerHTML = `
            <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
                <strong class="me-3">${msg}</strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

    if (position == 'body') {
      document.body.append(element);
      element.classList.add('custom-alert');
    }
    else {
      document.getElementById(position).appendChild(element);
    }

    setTimeout(remAlert, 3000);
  }
    function limitLength1(input) {
      if (input.value.length > 10) {
        input.value = input.value.slice(0, 10);
      }
    }
    function limitLength2(input) {
      if (input.value.length > 6) {
        input.value = input.value.slice(0, 6);
      }
    }
    let info_form = document.getElementById('info-form');
    info_form.addEventListener('submit', function (e) {
      e.preventDefault();
      let data = new FormData();
      data.append('info_form', '');
      data.append('name', info_form.elements['name'].value);
      data.append('phonenum', info_form.elements['phonenum'].value);
      data.append('dob', info_form.elements['dob'].value);
      data.append('address', info_form.elements['address'].value);
      data.append('pincode', info_form.elements['pincode'].value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/profile.php", true);
      xhr.onload = function () {
        if (this.responseText == 'phone_already') {
          alertP('error', 'Phone number is already registered!');
        }
        else if (this.responseText == 0) {
          alertP('error', 'No Changes Made!');
        }
        else {
          alertP('success', 'Changes Saved Successfully!');
        }
      };

      xhr.send(data);
    });

    let profile_form = document.getElementById('profile-form');
    profile_form.addEventListener('submit', function (e) {
      e.preventDefault();
      let data = new FormData();
      data.append('profile_form', '');
      data.append('profile', profile_form.elements['profile'].files[0]);

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/profile.php", true);
      xhr.onload = function () {
        if (this.responseText == 'inv_img') {
          alertP('error', 'Only JPG, WEBP, & PNG images are allowed!');
        }
        else if (this.responseText == 'upd_failed') {
          alertP('error', 'Image upload failed!');
        }
        else if(this.responseText == 0){
          alertP('error', 'Updation failed!');
        }
        else {
          window.location.href = window.location.pathname;
        }
      };

      xhr.send(data);
    });

    let pass_form = document.getElementById('pass-form');
    pass_form.addEventListener('submit', function (e) {
      e.preventDefault();
      let new_pass = pass_form.elements['new_pass'].value;
      let confirm_pass = pass_form.elements['confirm_pass'].value;

      if(new_pass != confirm_pass){
        alertP('error', 'Passwords do not match!');
        return false;
      }

      let data = new FormData();
      data.append('pass_form', '');
      data.append('new_pass', new_pass);
      data.append('confirm_pass', confirm_pass);

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/profile.php", true);
      xhr.onload = function () {
        if (this.responseText == 'mismatch') {
          alertP('error', 'Password do not match!');
        }
        else if(this.responseText == 0){
          alertP('error', 'Updation failed!');
        }
        else {
          alertP('success','Password updated');
          pass_form.reset();
        }
      };

      xhr.send(data);
    });
  </script>

</body>

</html>