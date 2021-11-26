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
               
                <div class="card-body">
                  <div class="col-md-12">
                    <div class="card">

                      <div class="card-content p-4">
       
                        <form>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Make Name</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getCarDataById[0]['make'] ?> <?php echo $getAllGarageData[0]['user_last_name'] ?? "" ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Make Logo</label>
                            </div>
                            <div class="col-md-3">
                            <img width="150px" class="img" src="uploads/cars_logo/<?php echo $getCarDataById[0]['make_logo']; ?>">
                            </div>
                          </div>
                        </form>
                          <div class="clearfix"></div>
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