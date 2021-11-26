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
                            <i class="material-icons offset-md-5">people</i> Manage Cars
                            <div class="ripple-container"></div>
                          </a>
                        </li>

                      </ul>
                    </div>
                  </div>
                </div>
                <?php include('include/message.php'); ?>
                <div class="card-body">
                  <div class="row">
                     <div class="col-md-12">
                      <a href="<?php echo site_url('admin/admin/addGarage/'); ?>" class="float-right add_btn">ADD GARAGE</a>
                    </div>
                  </div>
                  <div class="tab-content">

                    <div class="tab-pane active" id="manage_user">

                      <div class="bacckk">

                        <!--  <h2 class="ass">Assigne project</h2> -->

                        <div class="table-responsive">
                          <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead class="">
                              <tr>
                                <th>S.No</th>
                                <th> Make Name </th>
                                <th> Make Logo </th>
                                <th> Action </th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              $i = 0;
                              foreach ($getCarData as $row) {
                                $i++;
                              ?>
                              <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row['make']; ?></td>
                                <td><?php echo $row['make_logo']; ?></td>
                                <td>
                                <a href="<?php echo site_url('admin/admin/view_cars/'.$row['make_id']); ?>"><i class="material-icons text-warning">visibility</i></a>

                                     <a class="delete-btn" 
                                        data-toggle="modal" 
                                        data-target="#confirm" 
                                        data-url="<?php echo site_url('admin/admin/deleteCar').'/'.$row['make_id']; ?>" 
                                        href="javascript:void(0);">
                                                <i class="material-icons text-danger ">delete</i>
                                      </a>
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