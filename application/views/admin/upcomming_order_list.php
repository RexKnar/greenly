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
                            <i class="material-icons offset-md-5">people</i> Upcomming Orders
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
                                <th>S.No</th>
                                <th> Order #</th>
                                <th>Date</th>
                                <th>User Name </th>
                                <th>Vehicle(model)</th>
                                <th>Service</th>
                                <th>Sub Service</th>
                                <th> Provider Name </th>
                                <th>Amount </th>
                                <th>Paid </th>
                                <th>Balance </th>
                                <th>Paid Status</th>
                                <th>Method</th>
                                <th> Status </th>
                                <th> Invoice</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $i = 0;
                              foreach ($orderList as $row) {
                                $i++;
                                if($row['sub_service_id'])
                                {
                                $sub_service_array = array();
                                $sub_services = json_decode($row['sub_service_id'] ,true);
                                $sub_service_list = $this->db->select('service_name')
                                ->from('sub_services')
                                ->where_in('sub_service_id', $sub_services)
                                ->get();
                                $sub_service_array = [];
                                foreach($sub_service_list->result_array() as $ret){
                                    
                                    array_push($sub_service_array, $ret['service_name']);
                                }
                            }
                            else{
                                $sub_service_array='';
                            }
                              ?>
                                <tr>
                                  <td><?php echo $i; ?></td>
                                  <td><?php echo $row['invoice_no']; ?></td>
                                  <td><?php echo $row['booked_on']; ?></td>
                                  <td><?php echo $row['user_name']; ?></td>
                                  <td><?php echo $row['make'].'('.$row['model'].')'; ?></td>
                                  <td><?php echo $row['service_name']; ?></td>
                                
                                  <td><?php 
                                  if($sub_service_array)
                                  {
                                      echo implode(',', $sub_service_array); }
                                      else{
                                          echo '';
                                      }?></td>
                                  <td><?php echo $row['provider_name']; ?></td>
                                  <td><?php echo $row['total_amount'].' + '.$row['vat_amount'].''; ?></td>
                                  <td><?php echo $row['paid_amount']; ?></td>
                                  <td><?php echo $row['balance_amount']; ?></td>
                                  <td><?php if($row['is_paid']==1){
                                      echo 'Paid' ;
                                  }
                                  else{
                                      echo 'No';
                                  }  ?></td>
                                  <td><?php echo $row['payment_method']; ?></td>
                                  <td>
                                      <?php
                                      if($row['booking_status']=='pending')
                                      {
                                          echo '<span class="badge badge-info">Pending</span>';
                                      }elseif($row['booking_status']=='reject')
                                      {
                                        echo '<span class="badge badge-danger">Rejected</span>';
                                      }
                                      else{
                                        echo '<span class="badge badge-danger">Canceled</span>';
                                      }
                                      ?></td>
                                  <td>
                                  <?php if($row['invoice_path'])
                                  {
                                      ?>
                                      <a href="<?php echo $row['invoice_path']; ?>" target="_blank"><i class="material-icons text-info">download</i></a>
                                      <?php
                                  } ?>
                                  </td>
                                  <!-- <td><a href="<?php echo site_url('admin/admin/view_garage/'.$row['user_id']); ?>"><i class="material-icons text-warning">visibility</i></a></td>
                                   <td> <a href="<?php echo site_url('admin/admin/edit_garage/'.$row['user_id']); ?>"><i class="material-icons text-info">edit</i></a></td> -->
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