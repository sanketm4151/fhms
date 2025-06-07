<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

$fdate = $_POST['fromdate'] ?? date('Y-m-d');
$tdate = $_POST['todate'] ?? date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Booking Report</title>
    <?php require('inc/links.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
</head>

<body class="bg-light">
    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4">
                <h2 class="mb-4 text-center">Booking Report</h2>

                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Report From <?php echo $fdate; ?> to <?php echo $tdate; ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="report-table">
                            <thead class="table-dark">
                                <tr>
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
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM booking_order WHERE DATE(datetime) BETWEEN '$fdate' AND '$tdate'";
                                $results = mysqli_query($con, $sql);
                                $cnt = 1;
                                $total_amount = 0;
                                $refund_amount = 0;
                                while ($row = mysqli_fetch_assoc($results)) {
                                    $amount = $row['trans_amt'];
                                    $ref_amount=$row['ref_amount'];
                                    if ($row['booking_status'] === 'cancelled') {
                                        $refund_amount += $ref_amount;
                                    } 
                                     $total_amount += $amount;
                                     // Calculate remaining amount
                                     $remaining_amount = $total_amount - $refund_amount;

                                    ?>
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><?php echo htmlentities($row['booking_id']); ?></td>
                                        <td><?php echo htmlentities($row['user_id']); ?></td>
                                        <td><?php echo htmlentities($row['check_in']); ?></td>
                                        <td><?php echo htmlentities($row['check_out']); ?></td>
                                        <td><?php echo htmlentities($row['datetime']); ?></td>
                                        <td><?php echo htmlentities($row['booking_status']); ?></td>
                                        <td class="fw-bold <?php echo ($row['booking_status'] === 'Cancelled') ? 'text-danger' : 'text-success'; ?>">
                                            ₹<?php echo htmlentities($amount); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td colspan="7" class="text-end fw-bold">Total Amount:</td>
                                    <td class="fw-bold text-primary">₹<?php echo number_format($total_amount, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end fw-bold">Refund Amount:</td>
                                    <td class="fw-bold text-danger">₹<?php echo number_format($refund_amount, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end fw-bold">Remaining Amount:</td>
                                    <td class="fw-bold text-success">₹<?php echo number_format($remaining_amount, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="button" onclick="downloadPDF()" class="btn btn-success mt-3">
                            <i class="bi bi-file-earmark-arrow-down-fill"></i> Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('Booking Report', 105, 15, null, null, 'center');
    doc.setFontSize(12);
    doc.text('Report Duration: <?php echo $fdate; ?> to <?php echo $tdate; ?>', 105, 25, null, null, 'center');

    let element = document.getElementById("report-table");

    doc.autoTable({
        html: element,
        startY: 35,
        theme: 'grid',
        headStyles: { fillColor: [0, 0, 0], textColor: [255, 255, 255], fontStyle: 'bold' },
        styles: { fontSize: 10, cellPadding: 3, textColor: [0, 0, 0], lineColor: [0, 0, 0], lineWidth: 0.1 },
        alternateRowStyles: { fillColor: [240, 240, 240] },
        columnStyles: { 0: { cellWidth: 10 }, 7: { cellWidth: 25, fontStyle: 'bold' } },
        didParseCell: function (data) {
            if (data.column.index === 7 && data.section === 'body') {
                data.cell.text = [`₹${data.cell.text}`]; // Add ₹ symbol to the amount column
            }
        }
    });

    let totalPages = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(9);
        doc.text(`Page ${i} of ${totalPages}`, 200, 290, null, null, 'right');
        doc.text(`Generated on: ${new Date().toLocaleString()}`, 10, 290);
    }

    doc.save(`Booking_Report_<?php echo $fdate; ?>_to_<?php echo $tdate; ?>.pdf`);
}

</script>

    <?php require('inc/scripts.php'); ?>
</body>
</html>
