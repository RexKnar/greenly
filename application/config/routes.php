<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['home/terms_and_condition'] = 'welcome/home/terms_and_condition';
$route['home/privacy_policy'] = 'welcome/home/privacy_policy';

$route['admin']='admin/admin';
/*************Driver*************/
$route['driver/userRegister']='driver/driver/userRegister';
$route['driver/verify_driver']='driver/driver/verify_driver';
$route['driver/Add_detail']='driver/driver/Add_detail';
$route['driver/login']='driver/driver/login';
$route['driver/logout']='driver/driver/logout';
$route['driver/resend_otp']='driver/driver/resend_otp';
$route['driver/updateProfile']='driver/driver/updateProfile';
$route['driver/changePassword']='driver/driver/changePassword';
$route['driver/getProfile']='driver/driver/getProfile';
$route['driver/AcceptBooking']='driver/driver/AcceptBooking';
$route['driver/completeBooking']='driver/driver/completeBooking';
$route['driver/getOrderById']='driver/driver/getOrderById';
$route['driver/getWalletData']='driver/driver/getWalletData';
$route['driver/getDriverOrder']='driver/driver/getDriverOrder';
$route['driver/getTrasactionHistory']='driver/driver/getTrasactionHistory';
$route['driver/phoneExist']= 'driver/driver/phoneExist';
$route['driver/updatePassword']= 'driver/driver/updatePassword';


/*************User***********/
$route['user/userRegiser']= 'user/user/userRegiser';
$route['user/providerRegiser']= 'user/user/providerRegiser';
$route['user/userRegiser']= 'user/user/registerMobileNo';
$route['user/userVerify']= 'user/user/userVerify';
$route['user/resend_otp']= 'user/user/resend_otp';
$route['user/userLogin']= 'user/user/userLogin';
$route['user/logout']= 'user/user/logout';
$route['user/updateProfile']= 'user/user/updateProfile';
$route['user/changePassword']= 'user/user/changePassword';
$route['user/getProfile']= 'user/user/getProfile';
$route['user/getModel']= 'user/user/getModel';
$route['user/getMake']= 'user/user/getMake';
$route['user/getType']= 'user/user/getType';
$route['user/fetchModelByMakeId']= 'user/user/fetchModelByMakeId';
$route['user/addVehicle']= 'user/user/addVehicle';
$route['user/getVehicleByUserID']= 'user/user/getVehicleByUserID';
$route['user/getPlan']= 'user/user/getPlan';
$route['user/booking']= 'user/user/booking';
$route['user/cancelBooking']= 'user/user/cancelBooking';
$route['user/get_upcoming_order']= 'user/user/get_upcoming_order';
$route['user/get_past_order']= 'user/user/get_past_order';
$route['user/getOrderById']= 'user/user/getOrderById';
$route['user/notifyDriver']= 'user/user/notifyDriver';
$route['user/getFreeWashCount']= 'user/user/getFreeWashCount';
$route['user/addReview']= 'user/user/addReview';
$route['user/phoneExist']= 'user/user/phoneExist';
$route['user/updatePassword']= 'user/user/updatePassword';
$route['user/getNearestWashStation']= 'user/user/getNearestWashStation';
$route['user/getGarageByMakeId']= 'user/user/getGarageByMakeId';
$route['user/getServicesByCenterId']= 'user/user/getServicesByCenterId';
$route['user/BookGarageServices']= 'user/user/BookGarageServices';
$route['user/BookWashServices']= 'user/user/BookWashServices';
$route['user/BookGoEnQaZ']= 'user/user/BookGoEnQaZ';
$route['user/getGarageServices']= 'user/user/getGarageServices';
$route['user/addRating']= 'user/user/addRating';
$route['user/privacyPolicy']= 'user/user/privacyPolicy';
$route['user/termsConditions']= 'user/user/termsConditions';
$route['user/getProductCategory']= 'user/user/getProductCategory';
$route['user/getServiceType']= 'user/user/getServiceType';
$route['user/updateUserCurrentLocation']= 'user/user/updateUserCurrentLocation';


