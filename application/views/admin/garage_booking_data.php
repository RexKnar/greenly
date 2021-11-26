<?php include('include/auth.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'include/css.php'; ?>
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="purple" data-background-color="white" data-image="assets/img/sidebar-1.jpg">
      <?php include 'include/side_nav.php'; ?>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <?php include 'include/top_nav.php'; ?>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <div class="card">
                <div class="card-header card-header-tabs card-header-primary">
                  <div class="nav-tabs-navigation">
                    <div class="nav-tabs-wrapper">
                      <ul class="nav nav-tabs" data-tabs="tabs">

                        <li class="nav-item offset-md-5">
                          <a class="nav-link active show" href="#manage_user" data-toggle="tab">
                            <i class="material-icons offset-md-5">people</i> Manage Users
                            <div class="ripple-container"></div>
                          </a>
                        </li>

                      </ul>
                    </div>
                  </div>
                </div>
                <?php include('include/message.php'); ?>
                <div class="card-body">
                  <div class="tab-content">

                    <div class="tab-pane active" id="manage_user">

                      <div class="bacckk">

                        <!--  <h2 class="ass">Assigne project</h2> -->

                        <div class="table-responsive">
                          <table class="table table-hover table-striped" id="user_table">
                            <thead class="">
                              <tr>
                                <th> S.No </th>
                                <th> Booking Id </th>
                                <th> user Image</th>
                                <th> user Name</th>
                                <th> Garage Name</th>
                                <th> Phone No </th>
                                <th> Booking Code </th>
                                <th> Booking Status </th>
                                <th> Payment status</th>
                                <th> Action </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $i = 0;
                              foreach ($getData as $row) {
                                $i++;
                              ?>
                                <tr>
                                  <td><?php echo $i; ?></td>
                                  <td><?php echo $row['booking_id']; ?></td>
                                  <td> <?php echo trim($row['user_image']) != null ? '<img class="img-thumbnail" src="uploads/profile_images/' . $row['user_image'] . '">' : ''; ?> </td>
                                  <td><?php echo $row['user_first_name']." ".$row['user_last_name']; ?></td>
                                  <td><?php echo $row['garage_name']; ?></td>
                                  <td><?php echo $row['user_phone']; ?></td>
                                  <td><?php echo $row['booking_code']; ?></td>
                                  <?php
                                  $booking_status = "";
                                  if (isset($row['booking_status']) && trim($row['booking_status']) == 'open') 
                                  {
                                    $booking_status = "Pending";
                                  ?>
                                    <td><?php echo $booking_status; ?></td>
                                  <?php
                                  } elseif (isset($row['booking_status']) && trim($row['booking_status']) == 'completed') 
                                  {
                                    $booking_status = "Completed";
                                  ?>
                                    <td><?php echo $booking_status; ?></td>

                                  <?php
                                  } 
                                  else 
                                  {
                                  ?>
                                    <td><?php echo $booking_status; ?></td>

                                  <?php
                                  }
                                  $is_paid="UnPaid";
                                  if($row['payment_method']=="cash")
                                  {
                                    $is_paid="Paid";
                                  ?>
                                    <td class="text-success"><?php echo $is_paid; ?></td>
                                  <?php
                                  }
                                  else
                                  {
                                  ?>
                                    <td class="text-danger"><?php echo $is_paid; ?></td>
                                  <?php
                                  }
                                  if(empty($row['payment_method']))
                                  {
                                  ?>
                                    <td><a href="<?php echo site_url('admin/admin/garageBookingUpdate/'. $row['booking_id']); ?>" class="float-right add_btn"> Paid</a></td>
                                  <?php
                                  }
                                  else
                                  {

                                  }
                                  ?> 
                                  
                                </tr>
                              <?php
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
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <?php include 'include/script.php'; ?>
</body>

</html>