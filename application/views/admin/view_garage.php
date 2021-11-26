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
               
                <div class="card-body">
                  <div class="col-md-12">
                    <div class="card">

                      <div class="card-content p-4">
       
                        <form>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Company Name</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b> <?php echo $getAllGarageData[0]['garage_name']; ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">User Name</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getAllGarageData[0]['user_first_name'] ?> <?php echo $getAllGarageData[0]['user_last_name']; ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Email</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getAllGarageData[0]['user_email'] ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Contact No</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getAllGarageData[0]['user_phone'] ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Address</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getAllGarageData[0]['garage_location'] ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Company Logo</label>
                            </div>
                            <div class="col-md-3">
                              <label class="bmd-label-floating"><b><?php echo $getAllGarageData[0]['garage_logo'] ?? "" ?></b></label>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Providing Services</label>
                            </div>
                          
                            
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-3">
                              <label class="bmd-label-floating">Type of Vehicle</label>
                            </div>
                            <div class="col-md-3">
                              <?php 
                                  if($getTypeofVehicle){ 

                                foreach($getTypeofVehicle as $vehicle){
                              ?>
                                <label class="bmd-label-floating"><b><?php echo $vehicle['make'] ?? "" ?> </b></label> <br>
                              <?php       
                                } }
                              ?>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <div class="table-responsive">
                                <hr>
                              <?php 
                           
                           foreach($getGarageService as $services)
                           { ?>
                           <h4><?php echo $services['service_type']; ?></h4>
                              <table   class="table table-hover table-striped" >
                                <thead>
                                <tr>
                                  <th>Sub Service Name</th><th>Amount</th><th>Default Amount</th><th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php 
                               
                                foreach($services['sub_service_mapping'] as $mapping)
                                { ?>
                                <tr>
                                  <td><?php echo $mapping['service_name'];  ?></td><td><?php echo $mapping['service_price'];  ?></td><td><?php echo $mapping['default_price'];  ?></td><td></td>
                                </tr>
                                <?php }  ?>
                                </tbody>
                              </table>
                             <?php } ?>
                              
        
                             
                            </div>
                          </div>
                          </div>
                        </form>
                          <div class="row pb-12">
                          <?php 
                          
                          if($getAllGarageData[0]['user_status'] != 1){

                           ?>  
                          <div class="col-md-1">
                            <button type="submit" onclick="event.preventDefault(); document.getElementById('frm-status-update').submit();" class="btn btn-primary add_btn_center">Accept</button>
                            <form id="frm-status-update" action="<?php echo base_url(); ?>admin/admin/updateStatus" method="POST" style="display: none;">
                              <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                              <input type="hidden" value="1" name="status">
                            </form>
                          </div>
                            <div class="col-md-2">
                            <button type="submit" class="btn btn-warning add_btn_center" onclick="event.preventDefault(); document.getElementById('frm-status-reject').submit();">Reject</button>
                            <form id="frm-status-reject" action="<?php echo base_url(); ?>admin/admin/updateStatus" method="POST" style="display: none;">
                              <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                              <input type="hidden" value="0" name="status">
                            </form>
                            </div>
                         <?php 
                        } ?>  
                          <div class="col-md-1">
                            <button type="submit" onclick="event.preventDefault(); document.getElementById('frm-status-update').submit();" class="btn btn-info add_btn_center">Block </button>
                            <form id="frm-status-update" action="<?php echo base_url(); ?>admin/admin/updateStatus" method="POST" style="display: none;">
                              <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                              <input type="hidden" value="2" name="status">
                            </form>
                            </div>
                            <div class="col-md-2">
                            <button type="submit" class="btn btn-danger add_btn_center" onclick="event.preventDefault(); document.getElementById('frm-status-reject').submit();">Delete</button>
                            <form id="frm-status-reject" action="<?php echo base_url(); ?>admin/admin/updateStatus" method="POST" style="display: none;">
                              <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                              <input type="hidden" value="3" name="status">
                            </form>
                            </div>
                            <div class="col-md-1">
                            <a class="btn btn-success add_btn_center" href="<?php echo site_url('admin/admin/edit_garage/'.$user_id); ?>">Edit</a>
                            </div>
                          </div>
                        
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