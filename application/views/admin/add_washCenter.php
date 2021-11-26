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
                            <i class="material-icons offset-md-5">people</i> ADD SERVICE CENTER
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

                        <form enctype='multipart/form-data' action="<?php echo base_url(); ?>admin/admin/WashCenterAdd" method="POST">
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Wash Center Name</label>
                              <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" id="center_name" name="center_name">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Center Description</label>
                              <div class="form-group bmd-form-group">
                                <textarea class="form-control" id="center_description" name="center_description">
                                </textarea>
                              </div>
                            </div>
                          </div>
                          <div class="row pb-3">
                            <div class="col-md-12">
                              <label class="bmd-label-floating">Center Logo</label>
                              <div class="form-group bmd-form-group">
                                <input type="file" style="position: unset;opacity: 1;" id="center_logo" name="center_logo">
                              </div>
                            </div>
                          </div>
                          <div class="row pb-2">
                            <div class="col-md-12">
                              <label class="bmd-label-floating" for="center_location">Location</label>
                              <div class="form-group bmd-form-group">
                                <input id="pac-input1" type="text" class="maps-complete form-control @error('center_location') is-invalid @enderror" name="center_location" value="" autocomplete="center_location" autofocus placeholder="">
                                <input class="maps-autocomplete-lat" type="hidden" id="center_lat" name="center_lat" value='' />
                                <input class="maps-autocomplete-lng" type="hidden" id="center_long" name="center_long" value='' />
                              </div>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary add_btn_center">ADD</button>
                          <div class="clearfix"></div>



                         
                          <script>
                            function initMultiComplete() {
                              $('.maps-complete').each(function() {
                                var id = jQuery(this).prop('id');
                                var $this = jQuery(this);
                                var parent = jQuery(this).parent('div');
                                var jautocomplete = new google.maps.places.Autocomplete(document.getElementById(id), {
                                  types: ['geocode']
                                });
                                jautocomplete.addListener('place_changed', function() {
                                  var place = jautocomplete.getPlace();
                                  var address = $this.val();
                                  var lat = place.geometry.location.lat();
                                  var lng = place.geometry.location.lng();
                                  $('.maps-autocomplete-lat', parent).val(lat);
                                  $('.maps-autocomplete-lng', parent).val(lng);
                                });
                              });
                            }
                          </script>
                          <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD68DIEkxvp0gQsXlc9cYexbLLOFZfApnQ&libraries=places&callback=initMultiComplete" async defer>
                          </script>







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