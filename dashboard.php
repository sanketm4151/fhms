<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
// session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <?php require('inc/links.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-light">

    <?php
    require('inc/header.php');

    $is_shutdown = mysqli_fetch_assoc(mysqli_query($con, "SELECT shutdown FROM settings"));

    $current_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
                        COUNT(CASE WHEN booking_status='booked' AND arrival=0 THEN 1 END) AS 'new_bookings',
                        COUNT(CASE WHEN booking_status='cancelled' AND refund=0 THEN 1 END) AS 'refund_bookings'
                        FROM booking_order"));

    $unread_queries = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS 'count' FROM user_queries WHERE seen=0"));

    $unread_reviews = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS 'count' FROM rating_review WHERE seen=0"));

    $current_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
                        COUNT(id) AS 'total',
                        COUNT(CASE WHEN status=1 THEN 1 END) AS 'active',
                        COUNT(CASE WHEN status=0 THEN 1 END) AS 'inactive',
                        COUNT(CASE WHEN is_verified=0 THEN 1 END) AS 'unverified'
                        FROM user_cred"));
    ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3>DASHBOARD</h3>
                    <?php
                    if ($is_shutdown['shutdown'] == 1) {
                        echo <<<data
                            <h6 class="badge bg-danger py-2 px-3 rounded">Shutdown Mode is Active!</h6>
                        data;
                    }
                    ?>
                </div>

                <div class="row mb-4">
                    <!-- New Bookings Card -->
                    <div class="col-md-3 mb-4">
                        <a href="new_bookings.php" class="text-decoration-none">
                            <div class="card text-center p-3 text-success">
                                <h6>New Bookings</h6>
                                <h1 class="mt-2 mb-0"><?php echo $current_bookings['new_bookings']; ?></h1>
                            </div>
                        </a>
                    </div>
                    <!-- Refund Bookings Card -->
                    <div class="col-md-3 mb-4">
                        <a href="refund_bookings.php" class="text-decoration-none">
                            <div class="card text-center p-3 text-warning">
                                <h6>Refund Bookings</h6>
                                <h1 class="mt-2 mb-0"><?php echo $current_bookings['refund_bookings']; ?></h1>
                            </div>
                        </a>
                    </div>
                    <!-- User Queries Card -->
                    <div class="col-md-3 mb-4">
                        <a href="user_queries.php" class="text-decoration-none">
                            <div class="card text-center p-3 text-primary">
                                <h6>User Queries</h6>
                                <h1 class="mt-2 mb-0"><?php echo $unread_queries['count']; ?></h1>
                            </div>
                        </a>
                    </div>
                    <!-- Rating & Reviews Card -->
                    <div class="col-md-3 mb-4">
                        <a href="rate_review.php" class="text-decoration-none">
                            <div class="card text-center p-3 text-info">
                                <h6>Ratings & Reviews</h6>
                                <h1 class="mt-2 mb-0"><?php echo $unread_reviews['count']; ?></h1>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5>Booking Analytics</h5>
                    <select class="form-select shadow-none bg-light w-auto" onchange="booking_analytics(this.value)">
                        <option value="1">Past 30 Days</option>
                        <option value="2">Past 90 Days</option>
                        <option value="3">Past 180 Days</option>
                        <option value="4">Past 1 Year</option>
                        <option value="5">All Time</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <!-- Total Bookings Card and Amount -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-primary p-3">
                            <h6>Total Bookings</h6>
                            <h1 class="mt-2 mb-0" id="total_bookings">0</h1>
                            <h4 class="mt-2 mb-0" id="total_amt">₹0</h4>
                        </div>
                    </div>
                    <!-- Active Bookings Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-success p-3">
                            <h6>Active Bookings</h6>
                            <h1 class="mt-2 mb-0" id="active_bookings">0</h1>
                            <h4 class="mt-2 mb-0" id="active_amt">₹0</h4>
                        </div>
                    </div>
                    <!-- Cancelled Bookings Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-danger p-3">
                            <h6>Cancelled Bookings</h6>
                            <h1 class="mt-2 mb-0" id="cancelled_bookings">0</h1>
                            <h4 class="mt-2 mb-0" id="cancelled_amt">₹0</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Booking Distribution</h5>
                        <canvas id="bookingChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h5>Amount Distribution</h5>
                        <canvas id="amountChart"></canvas>
                    </div>
                </div>
                <br>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5>User, Queries, Reviews Analytics</h5>
                    <select class="form-select shadow-none bg-light w-auto" onchange="user_analytics(this.value)">
                        <option value="1">Past 30 Days</option>
                        <option value="2">Past 90 Days</option>
                        <option value="3">Past 180 Days</option>
                        <option value="4">Past 1 Year</option>
                        <option value="5">All Time</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <!-- New Registration Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-success p-3">
                            <h6>New Registration</h6>
                            <h1 class="mt-2 mb-0" id="total_new_reg">0</h1>
                        </div>
                    </div>
                    <!-- Queries Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-primary p-3">
                            <h6>Queries</h6>
                            <h1 class="mt-2 mb-0" id="total_queries">0</h1>
                        </div>
                    </div>
                    <!-- Reviews Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-info p-3">
                            <h6>Reviews</h6>
                            <h1 class="mt-2 mb-0" id="total_reviews">0</h1>
                        </div>
                    </div>

                </div>

                <h5>Users</h5>
                <div class="row mb-3">
                    <!-- Total Users Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-primary p-3">
                            <h6>Total</h6>
                            <h1 class="mt-2 mb-0"><?php echo $current_users['total']; ?></h1>
                        </div>
                    </div>
                    <!-- Active Users Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-success p-3">
                            <h6>Active</h6>
                            <h1 class="mt-2 mb-0"><?php echo $current_users['active']; ?></h1>
                        </div>
                    </div>
                    <!-- Inactive Users Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-warning p-3">
                            <h6>Inactive</h6>
                            <h1 class="mt-2 mb-0"><?php echo $current_users['inactive']; ?></h1>
                        </div>
                    </div>
                    <!-- Unverified Users Card -->
                    <div class="col-md-3 mb-4">
                        <div class="card text-center text-danger p-3">
                            <h6>Unverified</h6>
                            <h1 class="mt-2 mb-0"><?php echo $current_users['unverified']; ?></h1>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            booking_analytics(1); // Load default data for the past 30 days
        });

        let bookingChart;  // Stores the bookings pie chart
        let amountChart;   // Stores the amount pie chart

        function booking_analytics(period = 1) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/dashboard.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (this.status === 200) {
                    try {
                        let data = JSON.parse(this.responseText);

                        // Update the Booking Cards
                        document.getElementById('total_bookings').textContent = data.total_bookings || 0;
                        document.getElementById('total_amt').textContent = '₹' + (data.total_amt || 0);

                        document.getElementById('active_bookings').textContent = data.active_bookings || 0;
                        document.getElementById('active_amt').textContent = '₹' + (data.active_amt || 0);

                        document.getElementById('cancelled_bookings').textContent = data.cancelled_bookings || 0;
                        document.getElementById('cancelled_amt').textContent = '₹' + (data.cancelled_amt || 0);

                        // Update the Pie Charts
                        updateBookingChart(data.active_bookings, data.cancelled_bookings);
                        updateAmountChart(data.active_amt, data.cancelled_amt);
                    } catch (error) {
                        console.error("Error parsing JSON response:", error);
                    }
                } else {
                    console.error("Error fetching data:", this.status);
                }
            };

            xhr.send("booking_analytics&period=" + period);
        }

        // Function to Update Bookings Pie Chart
        function updateBookingChart(activeBookings, cancelledBookings) {
            const ctx = document.getElementById("bookingChart");

            if (!ctx) {
                console.error("Canvas element for Booking Chart not found.");
                return;
            }

            if (bookingChart) {
                bookingChart.destroy(); // Destroy old chart before creating a new one
            }

            bookingChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ["Active Bookings", "Cancelled Bookings"],
                    datasets: [{
                        data: [activeBookings || 0, cancelledBookings || 0],
                        backgroundColor: ["#28a745", "#dc3545"]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom"
                        }
                    }
                }
            });
        }

        // Function to Update Amount Pie Chart
        function updateAmountChart(activeAmt, cancelledAmt) {
            const ctx = document.getElementById("amountChart");

            if (!ctx) {
                console.error("Canvas element for Amount Chart not found.");
                return;
            }

            if (amountChart) {
                amountChart.destroy(); // Destroy old chart before creating a new one
            }

            amountChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ["Active Amount", "Cancelled Amount"],
                    datasets: [{
                        data: [activeAmt || 0, cancelledAmt || 0],
                        backgroundColor: ["#17a2b8", "#ffc107"]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom"
                        }
                    }
                }
            });
        }
        // function booking_analytics(period=1) {
        //   let xhr = new XMLHttpRequest();
        //   xhr.open("POST", "ajax/dashboard.php", true);
        //   xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //   xhr.onload = function () {
        //     let data = JSON.parse(this.responseText);
        //     document.getElementById('total_bookings').textContent = data.total_bookings;
        //     document.getElementById('total_amt').textContent = '₹'+data.total_amt;

        //     document.getElementById('active_bookings').textContent = data.active_bookings;
        //     document.getElementById('active_amt').textContent = '₹'+data.active_amt;

        //     document.getElementById('cancelled_bookings').textContent = data.cancelled_bookings;
        //     document.getElementById('cancelled_amt').textContent = '₹'+data.cancelled_amt;
        //   };

        //   xhr.send("booking_analytics&period="+period);
        // }

        function user_analytics(period = 1) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/dashboard.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                let data = JSON.parse(this.responseText);
                document.getElementById('total_new_reg').textContent = data.total_new_reg;

                document.getElementById('total_queries').textContent = data.total_queries;

                document.getElementById('total_reviews').textContent = data.total_reviews;
            };

            xhr.send("user_analytics&period=" + period);
        }

        window.onload = function () {
            booking_analytics();
            user_analytics();
        };

    </script>

</body>

</html>