<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php'); // Include your database connection


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fetch_all']) && $_POST['fetch_all'] === 'true') {
        // Fetch all services
        $query = "SELECT id, name, price FROM services";
        $result = mysqli_query($con, $query);

        if (!$result) {
            echo "<p>Failed to retrieve services. Please try again later.</p>";
            error_log('Query Error: ' . mysqli_error($con)); // Log the error for debugging
            exit;
        }

        if (mysqli_num_rows($result) > 0) {
            while ($service = mysqli_fetch_assoc($result)) {
                echo "
                  <div class='form-check col-md-12 mb-1'>
                    <input class='form-check-input' type='checkbox' name='services[]' value='{$service['id']}'>
                    <label class='form-check-label'>
                      {$service['name']} (Price: {$service['price']})
                    </label>
                  </div>
                ";
            }
        } else {
            echo "<p>No services available.</p>";
        }
    } elseif (isset($_POST['event_id'])) {
        $event_id = intval($_POST['event_id']);

        // Fetch services associated with the event
        $query = "
          SELECT services.id, services.name, services.price 
          FROM event_services 
          JOIN services ON event_services.services_id = services.id 
          WHERE event_services.event_id = '$event_id'
        ";
        $result = mysqli_query($con, $query);

        if (!$result) {
            echo "<p>Failed to retrieve services. Please try again later.</p>";
            error_log('Query Error: ' . mysqli_error($con)); // Log the error for debugging
            exit;
        }

        if (mysqli_num_rows($result) > 0) {
            while ($service = mysqli_fetch_assoc($result)) {
                echo "
                  <div class='form-check col-md-12 mb-1'>
                    <input class='form-check-input' type='checkbox' name='services[]' value='{$service['id']}'>
                    <label class='form-check-label'>
                      {$service['name']} (Price: {$service['price']})
                    </label>
                  </div>
                ";
            }
        } else {
            echo "<p>No services available for this event.</p>";
        }
    } else {
        echo "<p>Invalid request.</p>";
    }
}

?>
