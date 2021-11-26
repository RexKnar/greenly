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
                            <i class="material-icons offset-md-5">people</i> Add New Product
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

                        <form enctype='multipart/form-data' action="<?php echo base_url(); ?>admin/admin/addProduct" method="POST">
                        <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Category</label>
                              <select class="form-control" id="category" name="category_id" onchange="getSubcategory($(this).val())">
                                <?php foreach ($getCategoryData as $data) {
                                  echo "<option value=" . $data['id'] . ">" . $data['category'] . "</option>";
                                }?>
                              </select>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Sub Category</label>
                              <div class="form-group bmd-form-group">
                              <select class="form-control" id="sub_category_id" name="sub_category_id">
                                
                              </select>                              
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Name</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="name" name="name">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Price</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="price" name="price">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Quantity</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="qty" name="qty">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Garage Description</label>
                              <div class="form-group bmd-form-group">
                                <textarea class="form-control" id="description" name="description"></textarea>
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Product Image</label>
                              <div class="form-group bmd-form-group">
                                <input type="file" style="position: unset;opacity: 1;" id="product_image" name="product_image">
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