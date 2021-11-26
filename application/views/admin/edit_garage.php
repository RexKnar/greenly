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
                            <i class="material-icons offset-md-5">people</i> Manage Garage
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
       
                        <form  action="<?php echo base_url(); ?>admin/admin/updateGarage" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $getAllGarageData[0]['user_id']; ?>">
                          
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Garage Name</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="garageName" value="<?php echo $getAllGarageData[0]['garage_name']; ?>" name="garage_name">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-6">
                              <label class="bmd-label-floating">First Name</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="firstName" value="<?php echo $getAllGarageData[0]['user_first_name']; ?>" name="user_first_name">
                              </div>
                            </div>
                            <div class="col-md-6">
                            <label class="bmd-label-floating">Last Name</label>
                            <div class="form-group bmd-form-group">
                              <input type="text" class="form-control" id="lastName" value="<?php echo $getAllGarageData[0]['user_last_name']; ?>" name="user_last_name">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-6">
                              <label class="bmd-label-floating">Email</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="userEmail" value="<?php echo $getAllGarageData[0]['user_email']; ?>" name="user_email">
                              </div>
                            </div>
                            <div class="col-md-6">
                            <label class="bmd-label-floating">Contact No.</label>
                                <div class="form-group bmd-form-group">
                              <input type="text" class="form-control" id="contactNumber" value="<?php echo $getAllGarageData[0]['user_phone']; ?>" name="user_phone">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Address</label>
                              <div class="form-group bmd-form-group">
                              <input type="text" class="form-control" id="address" value="<?php echo $getAllGarageData[0]['garage_location']; ?>" name="garage_location">
                              </div>
                            </div>
                          </div>
                          
                          <!-- <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Company Logo</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getAllGarageData[0]['garage_logo'] ?? "" ?></b></label>
                            </div>
                          </div> -->
                          <button type="submit" class="btn btn-success add_btn_center" >Update</button>
                        </form>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                <?php 
                           
                           foreach($getGarageService as $services)
                           { ?>
                  <div class="card">
                    <div class="card-content p-4">
                      <div class="row  ">
                        <div class="col-md-12">
                          <form method="post" action="<?php echo base_url(); ?>admin/admin/add_sub_service_mapping">
                           <h3><?php echo $services['service_type']; ?></h3>
                              <input type="hidden" name="user_id" value="<?php echo $getAllGarageData[0]['user_id']; ?>">
                            
                            <div class="col-md-3">
                            
                            </div>
                            <div class="row pb-3">
                              <div class="col-md-4">
                                <label class="bmd-label-floating">Select Service</label>
                                <div class="form-group bmd-form-group">
                                  <select class="form-control" name="sub_service_id">
                                    <?php 
                                                           
                                                            foreach($services['sub_services'] as $sub_service)
                                                            { ?>
                                    <option value="<?php echo $sub_service['sub_service_id']; ?>"><?php echo $sub_service['service_name'];  ?> </option>
                                    <?php }  ?>
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-4">
                              <label class="bmd-label-floating">Enter Amount</label>
                              <div class="form-group bmd-form-group">
                                <input type="number" class="form-control"   name="service_price">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <button type="submit" class="btn btn-success" >Save</button>
                              </div>
                            </div>
                            
                            </form>
                            <br><br>
                        </div>
                        <div class="col-md-12">
                          <div class="table-responsive">
                          <table   class="table table-hover table-striped" >
                            <thead>
                            <tr>
                              <th>Sub Service Name</th><th>Amount</th><th>Default Price</th><th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            // print_r($services['sub_service_mapping']);
                            foreach($services['sub_service_mapping'] as $mapping)
                            { ?>
                            <tr>
                              <td><?php echo $mapping['service_name'];  ?></td>
                              <td><?php echo $mapping['service_price'];  ?></td>
                              <td><?php echo $mapping['default_price'];  ?></td>
                              
                              <td>
                                <a href="<?php echo site_url('admin/admin/delete_service_mapping/'.$mapping['service_mapping_id'].'/'.$getAllGarageData[0]['user_id']); ?>"><i class="material-icons text-danger">delete</i></a>
                               

                              </td>
                            </tr>
                            <?php }  ?>
                            </tbody>
                          </table>
                         
                          
    
                         
                        </div>
                      </div>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
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