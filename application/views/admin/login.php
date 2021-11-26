<?php include('include/noAuth.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'include/css.php';?>
</head>

<body class="">
    				<div class="wrapper ">
							<div class="main-panel">
								<div class="content">
									<div class="container-fluid">
										<div class="row">
											<div class="col-lg-12 col-md-12">
                        <div class="row">
                      		<div class="col-lg-8 col-md-12">
													<?php echo validation_errors(); ?>
                    			<?php include('include/message.php'); ?>
															<div class="tab-pane pt-3 active" id="invite">
																<form name="loginForm" id="loginForm" method="POST" action="<?php echo site_url('admin/admin/login'); ?>">
																	<div class="col-md-8 mx-auto mb-4">
																		<div class="form-group text-center mt-5 mb-4">
																			<img src="assets/img/faces/logo1.png">
																		</div>
																	</div>
																	<div class="col-md-8 mx-auto mb-4">
																		<div class="form-group">
																			<label class="bmd-label-floating">E-mail</label>
																			<input  required type="text" class="form-control" name="txtEmail" id="txtEmail">
																		</div>
																	</div>
																	<div class="col-md-8 mx-auto mb-4">
																		<div class="form-group">
																			<label class="bmd-label-floating">Password</label>
																			<input required type="Password" class="form-control"name="txtPassword" id="txtPassword">
																		</div>
																	</div>
																	<div class="col-md-8 mx-auto mb-4">
																		<div class="form-group">
																			<button type="submit"  name="btnLogin" id="btnLogin" class="btn btn-primary mx-auto w-50 d-block my-5">Log in</button>
																		</div>
																	</div> 
																</form>
															</div>
                          </div >
                        </div >
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