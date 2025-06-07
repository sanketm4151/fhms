<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Booking Report</title>
    <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4">
                <h3 class="mb-4 text-center text-primary fw-bold">Booking Records</h3>

                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Generate Booking Report</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" name="bwdatesreport" action="booking_report_details.php" class="row g-3">
                            <div class="col-md-4">
                                <label for="fromdate" class="form-label fw-bold">From Date</label>
                                <input type="date" class="form-control border-success" id="fromdate" name="fromdate" required>
                            </div>

                            <div class="col-md-4">
                                <label for="todate" class="form-label fw-bold">To Date</label>
                                <input type="date" class="form-control border-success" id="todate" name="todate" required>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-search"></i> Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>

               
            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
    <script src="scripts/booking_report.js"></script>

</body>
</html>
