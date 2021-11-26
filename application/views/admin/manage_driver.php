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
                            <i class="material-icons offset-md-5">people</i> Manage Driver
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

                        <!-- 	<h2 class="ass">Assigne project</h2> -->

                        <div class="table-responsive">
                          <table class="table table-hover table-striped" id="user_table">
                            <thead class="">
                              <tr>
                                <th> S.No</th>
                                <th> Photo </th>
                                <th> First Name</th>
                                <th> Last Name</th>
                                <th> Phone No </th>
                                <th> Mobile no. Verification</th>
                                <th>Admin Verification</th>
                                <th> Delete</th>
                                <th> View</th>
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
                                  <td>
                                    <?php echo trim($row['user_image']) != null ? '<img class="img-thumbnail" src="uploads/profile_images/' . $row['user_image'] . '">' : ''; ?>
                                  </td>
                                  <td> <?php echo $row['user_first_name']; ?></td>
                                  <td><?php echo $row['user_last_name']; ?></td>
                                  <td><?php echo $row['user_phone']; ?></td>

                                  <?php
                                  $is_verified = "";
                                  if (isset($row['is_verified']) && trim($row['is_verified']) == '1') {
                                    $is_verified = "Verified";
                                  ?>
                                    <td><?php echo $is_verified; ?></td>
                                  <?php
                                  } elseif (isset($row['is_verified']) && trim($row['is_verified']) == '0') {
                                    $is_verified = "Not Verified";
                                  ?>
                                    <td><?php echo $is_verified; ?></td>

                                  <?php
                                  } else {
                                  ?>
                                    <td><?php echo $is_verified; ?></td>

                                  <?php
                                  }

                                  ?>

                                  <?php
                                  $driver_verified = "";
                                  if (isset($row['driver_verified']) && trim($row['driver_verified']) == '1') {
                                    $driver_verified = "Verified";
                                  ?>
                                    <td><?php echo $is_verified; ?></td>
                                  <?php
                                  } elseif (isset($row['driver_verified']) && trim($row['driver_verified']) == '0') {
                                    $driver_verified = "Not Verified";
                                  ?>
                                    <td><?php echo $driver_verified; ?></td>

                                  <?php
                                  } else {
                                  ?>
                                    <td><?php echo $driver_verified; ?></td>

                                  <?php
                                  }

                                  ?>

                                  <td><a href="<?php echo site_url('admin/admin/deleteDriver/' . $row['user_id']); ?>"><i class="material-icons">delete</i></a></td>
                                  <td><a href="<?php echo site_url('admin/admin/showDriver/' . $row['user_id']); ?>"><i class="material-icons">visibility</i></a></td>
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