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
                                <a href="<?php echo site_url('admin/admin/manage_driver'); ?>" class="btn btn-primary" role="button">back</a>
                            </div>
                        </div>
                    </div>
                    <?php include 'include/topnav_detail.php';?> 
                </div>
            </nav>
            <?php
                    $getProfileDetail=$getProfileData['getProfile'];
                    $getCertificateDetail=$getProfileData['getCertificate'];
                     ?>
            <!-- End Navbar -->
            <div class="content">
            <?php include('include/message.php'); ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 offset-2">
                            <div class="card card-profile">
                                <div class="card-avatar">
                                    <img class="img" src="<?php echo isset($getProfileDetail->user_image) && trim($getProfileDetail->user_image)!=null?'uploads/profile_images/'.$getProfileDetail->user_image:''; ?>" />
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
                                            <div class="detail-user">Phone No :<span class="first"><?php echo isset($getProfileDetail->user_phone) && trim($getProfileDetail->user_phone)!=null?$getProfileDetail->user_phone:''; ?></span></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php
                                        if($getProfileDetail->driver_verified==0)
                                        {
                                            ?>
                                            <div class="col-md-6">
                                            <div class="decline"><a href="<?php  echo site_url('admin/admin/rejectRequest/'.$getProfileDetail->user_id);?>">DECLINE</a></div>
                                        </div>
                                        <div class="col-md-6">
                                        <div class="verify"><a href="<?php  echo site_url('admin/admin/acceptRequest/'.$getProfileDetail->user_id);?>">VERIFY</a></div>
                                        </div>
                                            <?php
                                        }
                                        ?>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bbcck">
                <div class="col-md-12 mx-auto mb-4">
                    <div class="backk">
                        <h2 class="ass">DRIVER PROOF</h2>
                        <div class="row" id="imageContainer">
                            <div class="col-md-12">
                            <?php 
                                    if(isset($getCertificateDetail[0]['license_front_photo']) && trim($getCertificateDetail[0]['license_front_photo'])!=null)
                                    {
                                    ?> 
                            <div class="iimage">
                                <h3 class="lience text-center">License Photo</h3>
                                <div class="row img-back">
                               
                                    <div class="col-md-6  text-center img-bottom">
                                    
                                        <img id="myImg" src="<?php echo isset($getCertificateDetail[0]['license_front_photo']) && trim($getCertificateDetail[0]['license_front_photo'])!=null?'uploads/document/'.$getCertificateDetail[0]['license_front_photo']:''; ?>" width="200" height="200">
                                    </div>
                                    <div class="col-md-6 text-center img-bottom">
                                        <img src="<?php echo isset($getCertificateDetail[0]['license_back_photo']) && trim($getCertificateDetail[0]['license_back_photo'])!=null?'uploads/document/'.$getCertificateDetail[0]['license_back_photo']:''; ?>"  width="200" height="200">
                                    </div>
                                </div>
                            </div>
                            <?php             
                                    }   
                                    if(isset($getCertificateDetail[0]['motorbike_license_front_photo']) && trim($getCertificateDetail[0]['motorbike_license_front_photo'])!=null)
                                    {
                                    ?> 
                                <div class="iimage">
                                    <h3 class="lience text-center"> Motorbike License Photo</h3>
                                    <div class="row img-back">
                                    
                                        <div class="col-md-6 text-center img-bottom">
                                            <img src="<?php echo isset($getCertificateDetail[0]['motorbike_license_front_photo']) && trim($getCertificateDetail[0]['motorbike_license_front_photo'])!=null?'uploads/document/'.$getCertificateDetail[0]['motorbike_license_front_photo']:''; ?>" width="200" height="200">
                                        </div>
                                        <div class="col-md-6  text-center img-bottom">
                                            <img src="<?php echo isset($getCertificateDetail[0]['motorbike_license_back_photo']) && trim($getCertificateDetail[0]['motorbike_license_back_photo'])!=null?'uploads/document/'.$getCertificateDetail[0]['motorbike_license_back_photo']:''; ?>" width="200" height="200">
                                        </div>
                                    </div>
                                </div>
                                <?php             
                                    }
                                    if(isset($getCertificateDetail[0]['national_id_front_photo']) && trim($getCertificateDetail[0]['national_id_front_photo'])!=null)
                                    {
                                    ?> 
                                <div class="iimage">
                                <h3 class="lience text-center"> National Id Photo</h3>
                                    <div class="row img-back">
                                    
                                    <div class="col-md-6  text-center img-bottom">
                                        <img src="<?php echo 'uploads/document/'.$getCertificateDetail[0]['national_id_front_photo']; ?>" width="200" height="200">
                                    </div>    <div class="col-md-6 text-center img-bottom">
                                            <img src="<?php echo isset($getCertificateDetail[0]['national_id_back_photo']) && trim($getCertificateDetail[0]['national_id_back_photo'])!=null?'uploads/document/'.$getCertificateDetail[0]['national_id_back_photo']:''; ?>" width="200" height="200">
                                        </div>
                                    
                                        
                                    </div>
                                </div>
                                <?php             
                                    }
                                        ?>
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