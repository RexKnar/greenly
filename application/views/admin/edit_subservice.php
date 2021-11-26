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
                            <i class="material-icons offset-md-5">people</i> Edit Sub Service
                            <div class="ripple-container"></div>
                          </a>
                        </li>

                      </ul>
                    </div>
                  </div>
                </div>
                <?php include('include/message.php'); ?>
                <div class="card-body">
                  <div class="col-md-12">
                    <div class="card">

                      <div class="card-content p-4">

                        <form method="POST" action="<?php echo base_url(); ?>admin/admin/updateSubservice/<?php echo $subserviceData[0]['sub_service_id']; ?>"  enctype="multipart/form-data">
                         
                          <div class="row pb-2">
                          <div class="col-md-6">
                          <label class="bmd-label-floating">Service Name</label>
                              <div class="form-group bmd-form-group">

                                <input type="text" class="form-control" id="sub_service_name" name="sub_service_name" value="<?php echo $subserviceData[0]['service_name']; ?>">
                                <input type="hidden" name="service_id" value="<?php echo $subserviceData[0]['service_id']; ?>" >
                                <input type="hidden" name="sub_service_id" value="<?php echo $subserviceData[0]['sub_service_id']; ?>" >
                              </div>
                            </div>
                            <div class="col-md-6">
                            <label class="bmd-label-floating">Service Price</label>
                              <div class="form-group bmd-form-group">

                                <input type="text" class="form-control" id="sub_service_price" name="sub_service_price" value="<?php echo $subserviceData[0]['service_price']; ?>">
                              </div>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary add_btn_center">Update</button>
                          <div class="clearfix"></div>
                        </form>
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