<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

if(isset($_GET['seen'])){
    $frm_data = filteration($_GET);

    if($frm_data['seen']=='all'){
        $q= "UPDATE `rating_review` SET `seen`=?";
        $values = [1];
        if(update($q,$values,'i')){
            alert('success','Marked all as seen!');
        }
        else{
            alert('error','Operation failed!');
        }
    }
    else{
        $q= "UPDATE `rating_review` SET `seen`=? WHERE `sr_no`=?";
        $values = [1,$frm_data['seen']];
        if(update($q,$values,'ii')){
            alert('success','Marked as seen!');
        }
        else{
            alert('error','Operation failed!');
        }
    }
}

if(isset($_GET['del'])){
    $frm_data = filteration($_GET);

    if($frm_data['del']=='all'){
        $q= "DELETE FROM `rating_review`";
        if(mysqli_query($con,$q)){
            alert('success','All data deleted!');
        }
        else{
            alert('error','Operation failed!');
        }
    }
    else{
        $q= "DELETE FROM `rating_review` WHERE `sr_no`=?";
        $values = [$frm_data['del']];
        if(delete($q,$values,'i')){
            alert('success','Data deleted!');
        }
        else{
            alert('error','Operation failed!');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Ratings & Reviews</title>
    <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4 ">RATINGS & REVIEWS</h3>

                <div class="card border-0 shadow-md mb-4">
                    <div class="card-body">
                        <div class="text-end mb-4">
                            <a href="?seen=all" class="btn btn-primary rounded-pill shadow-none btn-sm"> 
                                <i class="bi bi-check-all"></i> Mark all Seen
                            </a>
                            <a href="?del=all" class="btn btn-danger rounded-pill shadow-none btn-sm">
                                <i class="bi bi-trash"></i> Delete all
                            </a>
                        </div>
                        <div class="table-responsive-md">
                            <table class="table table-hover border">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col">#</th>
                                        <th scope="col" width="18%">Hall Name</th>
                                        <th scope="col" width="18%">User Name</th>
                                        <th scope="col" widht="15%">Rating</th>
                                        <th scope="col" width="30%">Review</th>
                                        <th scope="col" width="10%">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $q = "SELECT rr.*, uc.name AS uname, h.name AS hname FROM `rating_review` rr
                                            INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                                            INNER JOIN `halls` h ON rr.hall_id = h.id
                                            ORDER BY `sr_no` DESC";
                                        $data = mysqli_query($con,$q);
                                        $i = 1;
                                        while($row = mysqli_fetch_assoc($data)){
                                            $date = date('d-m-Y',strtotime($row['datetime']));
                                            $seen = '';
                                            if($row['seen']!=1){
                                                $seen = "<a href=' ? seen=$row[sr_no]' class='btn btn-sm rounded-pill btn-primary mb-2'>Seen</a><br>";
                                            }
                                            $seen.= "<a href=' ? del=$row[sr_no]' class='btn btn-sm rounded-pill btn-danger'>Delete</a>";
                                            echo<<<query
                                                <tr>
                                                    <td>$i</td>
                                                    <td>$row[hname]</td>
                                                    <td>$row[uname]</td>
                                                    <td>$row[rating]</td>
                                                    <td>$row[review]</td>
                                                    <td>$date</td>
                                                    <td>$seen</td>
                                                </tr>
                                            query;
                                            $i++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
</body>

</html>