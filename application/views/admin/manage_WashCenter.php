<?php include('include/auth.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'include/css.php';?>
</head>

<body class="">
    <div class="wrapper ">
        <div class="sidebar" data-color="purple" data-background-color="white" data-image="assets/img/sidebar-1.jpg">
        <?php include 'include/side_nav.php';?>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
            <?php include 'include/top_nav.php';?>
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
                                                    <a class="nav-link active show" href="#manage_WashCenter" data-toggle="tab">
                                                        <i class="material-icons offset-md-5">people</i> Manage Car Wash Center
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
                                    <?php
                                    // $get_data = $make_id;
                                    // if(!empty($getAllGarageData)) 
                                    // {
                                    // $data= $getAllGarageData;
                                    
                                    // } 
                                    ?>
                                        <div class="col-md-12">
                                        <a href="<?php echo site_url('admin/admin/addWashCenter'); ?>" class="float-right add_btn">ADD WASH CENTER</a>
                                        </div>
                                    </div>
                                        <div class="tab-content">

                                        <div class="tab-pane active" id="manage_user">

                                            <div class="bacckk">

                                                <!--  <h2 class="ass">Assigne project</h2> -->

                                                <div class="table-responsive">
                                                    <table class="table table-hover table-striped" id="user_table">
                                                        <thead class="">
                                                        <tr>
                                                                <th>S.No</th>
                                                                <th> Center Name </th>
                                                                <th> location</th>
                                                                <th> View Services</th>
                                                                <th> Delete</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php   
                                                            $i=0;
                                                            foreach($getWashCenterData as $row)
                                                            {
                                                                $i++;
                                                        ?>
                                                            <tr>
                                                            <td><?php echo $i;?></td>
                                                            <td><?php echo $row['center_name']; ?></td>
                                                            <td><?php echo $row['center_location']; ?></td>
                                                            <td><a href="<?php echo site_url('admin/admin/manage_Service/'.$row['center_id']); ?>"><i class="material-icons text-warning">visibility</i></a></td>
                                                            <td><a href="<?php  echo site_url('admin/admin/deleteWashCenter/'.$row['center_id']);?>"><i class="material-icons">delete</i></a></td>
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
    <?php include 'include/script.php';?>
</body>

</html>