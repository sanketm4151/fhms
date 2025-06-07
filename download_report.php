<?php
require('inc/essentials.php');
require('inc/db_config.php');
require_once('inc/pdf/vendor/autoload.php');

adminLogin();

use Mpdf\Mpdf;

    $fdate = $_POST['fromdate'];
    $tdate = $_POST['todate'];

    $mpdf = new Mpdf();
    $mpdf->SetTitle("Booking Report - $fdate to $tdate");

    // PDF Header
    $html = '<h2 style="text-align:center;">Company Name</h2>';
    $html .= '<h4 style="text-align:center;">Booking Report</h4>';
    $html .= '<p style="text-align:center;">From: <strong>' . $fdate . '</strong> To: <strong>' . $tdate . '</strong></p>';

    // Fetch Booking Data
    $sql = "SELECT * FROM booking_order WHERE DATE(datetime) BETWEEN '$fdate' AND '$tdate'";
    $results = mysqli_query($con, $sql);
    
    $total_amount = 0;
    $refund_amount = 0;
    
    // Table Header
    $html .= '<table border="1" style="width:100%; border-collapse: collapse; text-align:center;">
                <thead>
                    <tr style="background-color: #4CAF50; color: white;">
                        <th>#</th>
                        <th>Booking ID</th>
                        <th>User ID</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';

    $cnt = 1;
    while ($row = mysqli_fetch_assoc($results)) {
        $amount = $row['trans_amt'];
        if ($row['booking_status'] === 'cancelled') {
            $refund_amount += $amount;
        } else {
            $total_amount += $amount;
        }

        $html .= '<tr>
                    <td>' . $cnt++ . '</td>
                    <td>' . htmlentities($row['booking_id']) . '</td>
                    <td>' . htmlentities($row['user_id']) . '</td>
                    <td>' . htmlentities($row['check_in']) . '</td>
                    <td>' . htmlentities($row['check_out']) . '</td>
                    <td>' . htmlentities($row['datetime']) . '</td>
                    <td>' . htmlentities($row['booking_status']) . '</td>
                    <td style="font-weight:bold; color:' . ($row['booking_status'] === 'cancelled' ? 'red' : 'green') . ';">
                        ₹' . htmlentities($amount) . '
                    </td>
                  </tr>';
    }

    // Table Footer (Totals)
    $html .= '</tbody>
              <tfoot>
                <tr style="background-color: #f2f2f2;">
                    <td colspan="7" style="text-align:right; font-weight:bold;">Total Amount:</td>
                    <td style="font-weight:bold; color:green;">₹' . number_format($total_amount, 2) . '</td>
                </tr>
                <tr style="background-color: #f2f2f2;">
                    <td colspan="7" style="text-align:right; font-weight:bold;">Refund Amount:</td>
                    <td style="font-weight:bold; color:red;">₹' . number_format($refund_amount, 2) . '</td>
                </tr>
              </tfoot>
            </table>';

    // Add Content to PDF
    $mpdf->WriteHTML($html);

    // Output PDF (Download)
    $filename = "Booking_Report_{$fdate}_to_{$tdate}.pdf";
    $mpdf->Output($filename, "D"); // 'D' forces download
?>
