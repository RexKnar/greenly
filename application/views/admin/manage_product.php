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
                            <i class="material-icons offset-md-5">people</i> Manage Product
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

                        <a class="btn btn-success  active show" href="<?php echo site_url('admin/admin/addProduct'); ?>"> Add Product</a>
                        <div class="table-responsive">
                          <table class="table table-hover table-striped" id="user_table">
                            <thead class="">
                              <tr>
                                <th>S.No</th>
                                <th> Product Name</th>
                                <th> Description </th>
                                <th> Product Image </th>
                                <th> Product Price </th>
                                <th> Product Quantity </th>
                                <th> Action </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $i = 0;
                              foreach ($getProductData as $row) {
                                $i++;
                              ?>
                                <tr>
                                  <td><?php echo $i; ?></td>
                                  <td><?php echo $row['name']; ?></td>
                                  <td><?php echo $row['description']; ?></td>
                                  <td><?php echo $row['product_image']; ?></td>
                                  <td><?php echo $row['price']; ?></td>
                                  <td><?php echo $row['qty']; ?></td>
                                  <td><?php echo (($row['status']) == 1) ? "Active" : "Deactive"; ?></td>
                                   <td> 
                                     <a class="delete-btn" 
                                        data-toggle="modal" 
                                        data-target="#confirm" 
                                        data-url="<?php echo site_url('admin/admin/deleteProduct').'/'.$row['id']; ?>" 
                                        href="javascript:void(0);">
                                                <i class="material-icons text-danger ">delete</i>
                                      </a></td>
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