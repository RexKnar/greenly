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
                          <a class="nav-link active show" href="#manage_garage" data-toggle="tab">
                            <i class="material-icons offset-md-5">people</i> Manage Promotion Code
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

                        <a class="btn btn-success  active show" href="<?php echo site_url('admin/admin/addPromo'); ?>"> Add Promotion</a>
                        <div class="table-responsive">
                          <table class="table table-hover table-striped" id="user_table">
                            <thead class="">
                              <tr>
                                <th>S.No</th>
                                <th> Promotion Code</th>
                                <th> Description </th>
                                <th> Service Type </th>
                                <th> Discount Percentage(%) </th>
                                <th> Valid Till </th>
                                <th> Status </th>
                                <th> Action </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $i = 0;
                              foreach ($getPromoData as $row) {
                                $i++;
                              ?>
                                <tr>
                                  <td><?php echo $i; ?></td>
                                  <td><?php echo $row['promo_code']; ?></td>
                                  <td><?php echo $row['description']; ?></td>
                                  <td><?php echo $row['service_type']; ?></td>
                                  <td><?php echo $row['percentage']; ?></td>
                                  <td><?php echo $row['expiry_date']; ?></td>
                                  <td><?php echo (($row['status']) == 1) ? "Active" : "Deactive"; ?></td>
                                   <td> <a href="<?php echo site_url('admin/admin/view_promo/'.$row['id']); ?>"><i class="material-icons text-warning">edit</i></a> 
                                   <a href="<?php echo site_url('admin/admin/deletePromo/' . $row['id']); ?>"><i class="material-icons text-danger">delete</i></a>
                                  </td>
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