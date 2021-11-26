<?php include 'include/auth.php';?>
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
                          <a class="nav-link active show" href="#manage_garage" data-toggle="tab">
                            <i class="material-icons offset-md-5">people</i> Add New Promo Code
                            <div class="ripple-container"></div>
                          </a>
                        </li>

                      </ul>
                    </div>
                  </div>
                </div>
                <?php include 'include/message.php';?>
                <div class="card-body">
                  <div class="col-md-12">
                    <div class="card">

                      <div class="card-content p-4">

                        <form enctype='multipart/form-data' action="<?php echo base_url(); ?>admin/admin/addPromo" method="POST">
                        
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Promotion Code</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="promo_code" name="promo_code">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">End Date</label>
                              <div class="form-group bmd-form-group">
                              <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Percentage</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="percentage" name="percentage">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Promo Description</label>
                              <div class="form-group bmd-form-group">
                                <textarea class="form-control" id="description" name="description"></textarea>
                              </div>
                            </div>
                          </div>
                          
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Service Type</label>
                              <div class="form-group bmd-form-group">
                                <?php foreach($ServiceType as $servicetype) { ?>
                                  <input type="checkbox" id="service_type_<?php echo $servicetype['service_id'] ?>" name="service_type[]" value="<?php echo $servicetype['service_id'] ?>">
                                  <label for="service_type_<?php echo $servicetype['service_id'] ?>"> <?php echo $servicetype['service_type'] ?></label><br>
                                <?php  }?> 
                             </div>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary add_btn_center">ADD</button>
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
  <?php include 'include/script.php';?>
  <script>
  $( document ).ready(function() {
      var category = $( "#category option:selected" ).val();
      if(category != ""){
        getSubcategory(category);
      }
  });
  function getSubcategory(category){
        var url = "<?php echo base_url(); ?>";
        $.ajax({
            url :  url+"/admin/admin/getSubcategoryAjax?category="+category,
            type:'GET',
            success: function(response) {
              var data = JSON.parse(response);
              var toAppend = '';
              $('#sub_category_id').find('option').remove();
              $.each(data,function(index,value){
                toAppend += '<option value='+value.id+'>'+value.sub_category+'</option>';
              });
              $('#sub_category_id').append(toAppend);
             }
        });
  }
</script>
</body>

</html>