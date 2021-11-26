<?php include('include/auth.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'include/css.php';?>
</head>

<body class="">
    <div class="wrapper ">
        <div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
        <?php include 'include/side_nav.php';?>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
            <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <div class="navbar-form">
                            <div class="input-group no-border">
                                <a href="<?php echo site_url('admin/admin/manage_user'); ?>" class="btn btn-primary" role="button">back</a>
                            </div>
                        </div>
                    </div>
                    <?php include 'include/topnav_detail.php';?> 
                </div>
            </nav>

            <!-- End Navbar -->
            <?php
                $getProfileDetail=$getProfileData['getProfile'];
            ?>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-md-8 offset-2">
                            <div class="card card-profile">
                                <div class="card-avatar">
                                        <img class="img" src="<?php echo isset($getProfileDetail->user_image) && trim($getProfileDetail->user_image)!=null?'uploads/profile_images/'.$getProfileDetail->user_image:''; ?> ">
                                </div>

                                <div class="card-body">
                                    <div class="col-md-12 mx-auto ">
                                        <div class="form-group">
                                            <div class="detail-user">First name :<span class="first"><?php echo isset($getProfileDetail->user_first_name) && trim($getProfileDetail->user_first_name)!=null?$getProfileDetail->user_first_name:''; ?></span></div>

                                        </div>
                                    </div>
                                    <div class="col-md-12 mx-auto mb-">
                                        <div class="form-group">
                                            <div class="detail-user">Last name :<span class="first"><?php echo isset($getProfileDetail->user_last_name) && trim($getProfileDetail->user_last_name)!=null?$getProfileDetail->user_last_name:''; ?></span></div>

                                        </div>
                                    </div>    
                                    <div class="col-md-12 mx-auto mb-">
                                        <div class="form-group mail">
                                            <div class="detail-user">E-mail :<span class="first"><?php echo isset($getProfileDetail->user_email) && trim($getProfileDetail->user_email)!=null?$getProfileDetail->user_email:''; ?></span></div>

                                        </div>
                                    </div>
                                    <div class="col-md-12 mx-auto">
                                        <div class="form-group">
                                            <div class="detail-user">Phone No :<span class="first"><?php echo isset($getProfileDetail->user_phone) && trim($getProfileDetail->user_phone)!=null?$getProfileDetail->user_phone:''; ?></span></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           <!-- <div class="bbcck">
                <div class="col-md-12 mx-auto mb-4">
                    <div class="backk">
                        <h2 class="ass">Application</h2>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="user_table1">
                                <thead class="">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Real name</th>
                                        <th>Email</th>
                                        <th>Access Level</th>
                                        <th>Enabled</th>
                                        <th>Date Created</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota@mailinator.com</td>
                                        <td>Tester</td>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" value="" checked>
                                                    <span class="form-check-sign">
                                                                  <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>29-04-2019</td>

                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota@mailinator.com</td>
                                        <td>Tester</td>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" value="" checked>
                                                    <span class="form-check-sign">
                                                                  <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>29-04-2019</td>

                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota@mailinator.com</td>
                                        <td>Tester</td>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" value="" checked>
                                                    <span class="form-check-sign">
                                                                  <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>29-04-2019</td>

                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota Rice</td>
                                        <td>Dakota@mailinator.com</td>
                                        <td>Tester</td>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" value="" checked>
                                                    <span class="form-check-sign">
                                                                  <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>29-04-2019</td>

                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>-->
        </div>
        <div class="fixed-plugin">
            <div class="dropdown show-dropdown">
                <a href="#" data-toggle="dropdown">
                    <i class="fa fa-cog fa-2x"> </i>
                </a>
                <ul class="dropdown-menu">
                    <li class="header-title"> Sidebar Filters</li>
                    <li class="adjustments-line">
                        <a href="javascript:void(0)" class="switch-trigger active-color">
                            <div class="badge-colors ml-auto mr-auto">
                                <span class="badge filter badge-purple" data-color="purple"></span>
                                <span class="badge filter badge-azure" data-color="azure"></span>
                                <span class="badge filter badge-green" data-color="green"></span>
                                <span class="badge filter badge-warning" data-color="orange"></span>
                                <span class="badge filter badge-danger" data-color="danger"></span>
                                <span class="badge filter badge-rose active" data-color="rose"></span>
                            </div>
                            <div class="clearfix"></div>
                        </a>
                    </li>
                    <li class="header-title">Images</li>
                    <li class="active">
                        <a class="img-holder switch-trigger" href="javascript:void(0)">
                            <img src="../assets/img/sidebar-1.jpg" alt="">
                        </a>
                    </li>
                    <li>
                        <a class="img-holder switch-trigger" href="javascript:void(0)">
                            <img src="../assets/img/sidebar-2.jpg" alt="">
                        </a>
                    </li>
                    <li>
                        <a class="img-holder switch-trigger" href="javascript:void(0)">
                            <img src="../assets/img/sidebar-3.jpg" alt="">
                        </a>
                    </li>
                    <li>
                        <a class="img-holder switch-trigger" href="javascript:void(0)">
                            <img src="../assets/img/sidebar-4.jpg" alt="">
                        </a>
                    </li>
                    <li class="button-container">
                        <a href="https://www.creative-tim.com/product/material-dashboard" target="_blank" class="btn btn-primary btn-block">Free Download</a>
                    </li>
                    <!-- <li class="header-title">Want more components?</li>
            <li class="button-container">
                <a href="https://www.creative-tim.com/product/material-dashboard-pro" target="_blank" class="btn btn-warning btn-block">
                  Get the pro version
                </a>
            </li> -->
                    <li class="button-container">
                        <a href="https://demos.creative-tim.com/material-dashboard/docs/2.1/getting-started/introduction.html" target="_blank" class="btn btn-default btn-block">
            View Documentation
          </a>
                    </li>
                    <li class="button-container github-star">
                        <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star ntkme/github-buttons on GitHub">Star</a>
                    </li>
                    <li class="header-title">Thank you for 95 shares!</li>
                    <li class="button-container text-center">
                        <button id="twitter" class="btn btn-round btn-twitter"><i class="fa fa-twitter"></i> &middot; 45</button>
                        <button id="facebook" class="btn btn-round btn-facebook"><i class="fa fa-facebook-f"></i> &middot; 50</button>
                        <br>
                        <br>
                    </li>
                </ul>
            </div>
        </div>
        <!--   Core JS Files   -->
        <?php include 'include/script.php';?>
</body>

</html>