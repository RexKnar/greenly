<?php
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    public function index()
    {
        $this->load->view('admin/login.php');
    }

    public function driver_detail()
    {

        $this->load->view('admin/driver_detail.php');

    }
    public function updateStatus()
    {
        $user_status = $_REQUEST['status'];
        $this->admin_model->updateUserStatus($user_status, $_REQUEST['user_id']);
        $row_data = $this->user_model->check_data_by_user_id($_REQUEST['user_id']);
        $msg = 'Admin Approved Successfully';
        $date = date("Y-m-d H:i:s");
        $notify_type = 'admin-approved';
        $type = "User approved";
        $notificationData = array(
            ['user_id' => $row_data->user_id,
                'type' => $type,
                'title' => 'Register',
                'description' => $msg,
                'status' => 1,
                'created_at' => $date,
                'updated_at' => $date],
        );
        $this->user_model->insertNotification($notificationData);
        $message = "Your Profile has been admin Approved, login and start your services!";
        $subject = "Order Green - Approved Your profile!";
        $content['userName'] = $row_data->user_first_name;
        $content['content'] = $message;
        $this->user_model->emailSending($row_data->user_email, $subject, $content);
        $this->user_model->push_notify($row_data->device_token, $msg, $type, $notify_type, $row_data->user_type);
        $_SESSION['success'] = "Verified Successfully";
        redirect(site_url("admin/admin/manage_garage"));

    }
    public function acceptRequest($userId)
    {
        $driver_verified = 1;
        $this->admin_model->updateStatus($driver_verified, $userId);
        $_SESSION['success'] = "Verified Successfully";
        $getData['getData'] = $this->admin_model->driverData();
        if (!empty($getData)) {
            $this->load->view('admin/manage_driver.php', $getData);
        }
    }
    public function rejectRequest($userId)
    {
        $driver_verified = 2;
        $this->admin_model->updateStatus($driver_verified, $userId);
        $_SESSION['success'] = "Declined Successfully";
        $getData['getData'] = $this->admin_model->driverData();
        if (!empty($getData)) {
            $this->load->view('admin/manage_driver.php', $getData);
        }
    }

    public function manage_user()
    {
        $getData['getData'] = $this->admin_model->userData();
        if (!empty($getData)) {
            $this->load->view('admin/manage_user.php', $getData);
        }
    }

    public function manage_driver()
    {
        $getData['getData'] = $this->admin_model->driverData();
        if (!empty($getData)) {
            $this->load->view('admin/manage_driver.php', $getData);
        }
    }

    public function login()
    {
        $email = $this->input->post('txtEmail');
        $password = $this->input->post('txtPassword');
        $this->form_validation->set_rules('txtEmail', 'Email', 'required');
        $this->form_validation->set_rules('txtPassword', 'Password', 'required');
        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = "All fields are required";
            $this->session->mark_as_temp('error', 3);
            $this->load->view('admin/index');
        } else {
            $data = $this->admin_model->login($email, $password);
            if ($data) {
                $session_data = array(
                    'admin_id' => $data->admin_id,
                    'admin_email' => $data->admin_email,
                );
                $this->session->set_userdata('logged_in', $session_data);
                redirect('admin/admin/manage_user');
            } else {
                print_r($data);
                die();
                $_SESSION['error'] = "Invalid Credentials";
                $this->session->mark_as_temp('error', 3);
                $this->load->view('admin/login.php');
            }
        }
    }

    public function showUser($userId)
    {
        $getProfileData['getProfileData']['getProfile'] = $this->admin_model->getUserProfile($userId);
        if (!empty($getProfileData)) {
            $this->load->view('admin/user_detail.php', $getProfileData);
        }
    }

    public function showDriver($userId)
    {
        $getProfileData['getProfileData']['getProfile'] = $this->admin_model->getDriverProfile($userId);
        $getProfileData['getProfileData']['getCertificate'] = $this->admin_model->getDriverCertificate($userId);
        //var_dump( $getProfileData);
        if (!empty($getProfileData)) {
            $this->load->view('admin/driver_detail.php', $getProfileData);
        }
    }

    public function deleteUser($userId)
    {
        $user_status = 0;
        $user_id = $userId;
        $this->admin_model->update_status($user_status, $userId);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_user/"));
    }
    public function deletePromo($id)
    {
        $status = 0;
        $this->admin_model->update_promo_status($status, $id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_promo/"));
    }
    public function deleteDriver($userId)
    {
        $user_status = 0;
        $user_id = $userId;
        $this->admin_model->update_status($user_status, $userId);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_driver/"));
    }

    public function logout()
    {
        $this->load->view('admin/logout.php');
    }

    public function manage_cars()
    {
        $getCarData['getCarData'] = $this->admin_model->getCarData();
        if (!empty($getCarData)) {
            $this->load->view('admin/view_cars.php', $getCarData);
        }
    }

    public function view_cars($make_id)
    {
        $getCarDataById['getCarDataById'] = $this->admin_model->getCarDataById($make_id);
        if (!empty($getCarDataById)) {
            $this->load->view('admin/edit_cars.php', $getCarDataById);
        }
    }

    public function deleteCar($id)
    {
        $this->admin_model->deleteCar($id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_cars"));
    }

    public function manage_garage()
    {
        $getGarageData['getGarageData'] = $this->admin_model->garageData();
        if (!empty($getGarageData)) {
            $this->load->view('admin/manage_garage.php', $getGarageData);
        }
    } 
    public function view_garage($user_id)
    {
        $getAllGarageData['user_id'] = $user_id;
        $getAllGarageData['getAllGarageData'] = $this->admin_model->getGarageData($user_id);
        $getAllGarageData['getGarageService'] = $this->admin_model->getGarageService(implode(',', json_decode($getAllGarageData['getAllGarageData'][0]['service_type'])));

        foreach ($getAllGarageData['getGarageService'] as $ServicesKey => $ServicesValue) {
            $getAllGarageData['getGarageService'][$ServicesKey]['sub_service_mapping'] = $this->admin_model->getSubServiceMapping($user_id,$ServicesValue['service_id']);
            $getAllGarageData['getGarageService'][$ServicesKey]['sub_services'] =$this->admin_model->getSubServices($ServicesValue['service_id']);
            
        }
        $getAllGarageData['getServiceMapping']=$this->admin_model->getServiceMapping($user_id);
        $getAllGarageData['getTypeofVehicle'] = [];

        if (!empty(json_decode($getAllGarageData['getAllGarageData'][0]['make_id']))) {
            $getAllGarageData['getTypeofVehicle'] = $this->admin_model->getTypeofVehicle(implode(',', json_decode($getAllGarageData['getAllGarageData'][0]['make_id'])));
        }
        if (!empty($getAllGarageData)) {
            $this->load->view('admin/view_garage.php', $getAllGarageData);
        }
    }
    public function edit_garage($user_id)
    {
        $getAllGarageData['user_id'] = $user_id;
        $getAllGarageData['getAllGarageData'] = $this->admin_model->getGarageData($user_id);
        $getAllGarageData['getGarageService'] = $this->admin_model->getGarageService(implode(',', json_decode($getAllGarageData['getAllGarageData'][0]['service_type'])));
       
        $getAllGarageData['getTypeofVehicle'] = [];
        $getAllGarageData['getServiceMapping']=$this->admin_model->getServiceMapping($user_id);
        foreach ($getAllGarageData['getGarageService'] as $ServicesKey => $ServicesValue) {
            $getAllGarageData['getGarageService'][$ServicesKey]['sub_service_mapping'] = $this->admin_model->getSubServiceMapping($user_id,$ServicesValue['service_id']);
            $getAllGarageData['getGarageService'][$ServicesKey]['sub_services'] =$this->admin_model->getSubServices($ServicesValue['service_id']);
            
        }
        if (!empty(json_decode($getAllGarageData['getAllGarageData'][0]['make_id']))) {
            $getAllGarageData['getTypeofVehicle'] = $this->admin_model->getTypeofVehicle(implode(',', json_decode($getAllGarageData['getAllGarageData'][0]['make_id'])));
        }
        if (!empty($getAllGarageData)) {
            $this->load->view('admin/edit_garage.php', $getAllGarageData);
        }
    }


    public function delete_service_mapping($mapping_id,$user_id)
    {
        if($this->admin_model->deleteServiceMapping($mapping_id))
        {
            $_SESSION['success'] = "Delete Successfully";
        }
        else{
            $_SESSION['error'] = "OOPS! Something went wrong.";
        }
        redirect(site_url("admin/admin/edit_garage/".$user_id));
    }

    public function updateGarage()
    {
        $Data['garage_name'] = $_POST['garage_name'];
        $Data['garage_location'] = $_POST['garage_location'];
        $Data['user_phone'] = $_POST['user_phone'];
        $Data['user_email'] = $_POST['user_email'];
        $Data['user_first_name'] = $_POST['user_first_name'];
        $Data['user_last_name'] = $_POST['user_last_name']; 
        $Data['garage_name'] = $_POST['garage_name'];
        $Data['user_id'] = $_POST['user_id']; 
        $user_id=$_POST['user_id'];
        

        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        $this->form_validation->set_rules('user_phone', 'user_phone', 'trim|required');
        $this->form_validation->set_rules('garage_name', 'garage_name', 'trim|required');
        $this->form_validation->set_rules('garage_location', 'garage_location', 'trim|required');
        // if (isset($_FILES['garage_logo']['name']) && !empty($_FILES['garage_logo']['name'])) {
        //     $fileName = $_FILES['garage_logo']['name'];
        //     $tmpName = $_FILES['garage_logo']['tmp_name'];
        //     $uploadPath = './uploads/garage_logo/';
        //     $imageName = $uploadPath . $fileName;
        //     $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        //     $actualFileName = time() . "." . $fileExtension;
        //     $image_dir = './uploads/garage_logo/';
        //     $imgConfig['upload_path'] = $image_dir;
        //     $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
        //     $imgConfig['file_name'] = $actualFileName;
        //     $this->load->library('upload', $actualFileName);
        //     $this->upload->initialize($imgConfig);
        //     if ($this->upload->do_upload('garage_logo')) {
        //         $profileImage = $this->upload->data();
        //         $Data['garage_logo'] = $profileImage['file_name'];
        //     } else {
        //         $error = array('error' => $this->upload->display_errors());
        //     }
        // }

        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/edit_garage/".$user_id));
        } else {

            $this->admin_model->user_update($Data,$user_id);

            $_SESSION['success'] = "Garage Successfully Updated";
            redirect(site_url("admin/admin/view_garage/" . $user_id));

        }
    }

    public function update_service_details()
    {
        $Data['user_id'] = $_POST['user_id'];
        $Data['service_id'] = $_POST['service_id'];
        $Data['service_amount'] = $_POST['service_amount'];
        $user_id=$_POST['user_id'];


        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        $this->form_validation->set_rules('service_id', 'service_id', 'trim|required');
        $this->form_validation->set_rules('service_amount', 'service_amount', 'trim|required');

        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/edit_garage/".$user_id));
        } else {

            if($this->admin_model->map_service($Data))
            {
                $_SESSION['success'] = "Service Details Successfully Updated";
            redirect(site_url("admin/admin/edit_garage/".$user_id));
            }
            else{
                $_SESSION['error'] = "Service Details Not Added";
                redirect(site_url("admin/admin/edit_garage/".$user_id));
            }

            

        }
    }


    public function add_sub_service_mapping()
    {
        $Data['user_id'] = $_POST['user_id'];
        $Data['sub_service_id'] = $_POST['sub_service_id'];
        $Data['service_price'] = $_POST['service_price'];
        $user_id=$_POST['user_id'];


        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        $this->form_validation->set_rules('sub_service_id', 'Sub Service', 'trim|required');
        $this->form_validation->set_rules('service_price', 'Service Amount', 'trim|required');

        if ($this->form_validation->run() == false) {
            die();
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/edit_garage/".$user_id));
        } else {
            
            if($this->admin_model->map_sub_service($Data))
            {
                $_SESSION['success'] = "Service Details Successfully Updated";
            redirect(site_url("admin/admin/edit_garage/".$user_id));
            }
            else{
                
                $_SESSION['error'] = "Service Details Not Added";
                redirect(site_url("admin/admin/edit_garage/".$user_id));
            }

            

        }
    }

    public function manage_category()
    {
        $getCategoryData['getCategoryData'] = $this->admin_model->getCategoryData();
        if (!empty($getCategoryData)) {
            $this->load->view('admin/manage_category.php', $getCategoryData);
        }
    }
    public function addCategory()
    {
        if (isset($_POST['category'])) {
            $Data['category'] = $_POST['category'];
            $Data['description'] = $_POST['description'];
            $this->form_validation->set_data($Data);
            $this->form_validation->set_rules('category', 'category', 'trim|required');
            if ($this->form_validation->run() == false) {
                $_SESSION['error'] = strip_tags(validation_errors());
                redirect(site_url("admin/admin/addCategory"));
            } else {
                $date = date("Y-m-d H:i:s");
                $Data['created_at'] = $date;
                $Data['updated_at'] = $date;
                $this->admin_model->addCategory($Data);
                $_SESSION['success'] = "Category Inserted Successfully";
                redirect(site_url("admin/admin/manage_category"));

            }
        }
        $this->load->view('admin/add_category.php');

    }

    public function edit_category($categoryId)
    {
        $getCategoryData['categoryData'] = $this->admin_model->getCategoryById($categoryId);
        if (!empty($getCategoryData)) {
            $this->load->view('admin/edit_category', $getCategoryData);
        }
    }
    public function updateCategory()
    {
        if (isset($_POST['category'])) {
            $Data['category'] = $_POST['category'];
            $Data['description'] = $_POST['description'];
            $this->form_validation->set_data($Data);
            $this->form_validation->set_rules('category', 'category', 'trim|required');
            if ($this->form_validation->run() == false) {
                $_SESSION['error'] = strip_tags(validation_errors());
                redirect(site_url("admin/admin/edit_ategory/".$_POST['id']));
            } else {
                $this->admin_model->updateCategory($Data,$_POST['id']);
                $_SESSION['success'] = "Category Updated Successfully";
                redirect(site_url("admin/admin/manage_category"));

            }
        }
        $this->load->view('admin/add_category.php');

    }
    public function deleteCategory($id)
    {
        $this->admin_model->deleteCategory($id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_category"));
    }
    public function manage_subcategory()
    {
        $getsubCategoryData['getsubCategoryData'] = $this->admin_model->getsubCategoryData();
        if (!empty($getsubCategoryData)) {
            $this->load->view('admin/manage_subcategory.php', $getsubCategoryData);
        }
    }
    public function addsubCategory()
    {
        if (isset($_POST['category_id'])) {
            $Data['category_id'] = $_POST['category_id'];
            $Data['sub_category'] = $_POST['sub_category'];
            $Data['description'] = $_POST['description'];
            $this->form_validation->set_data($Data);
            $this->form_validation->set_rules('category_id', 'category id', 'trim|required');
            $this->form_validation->set_rules('sub_category', 'sub_category', 'trim|required');
            if ($this->form_validation->run() == false) {
                $_SESSION['error'] = strip_tags(validation_errors());
                redirect(site_url("admin/admin/addsubCategory"));
            } else {
                $date = date("Y-m-d H:i:s");
                $Data['created_at'] = $date;
                $Data['updated_at'] = $date;
                $this->admin_model->addsubCategory($Data);
                $_SESSION['success'] = "Sub Category Inserted Successfully";
                redirect(site_url("admin/admin/manage_subcategory"));

            }
        }
        $getCategoryData['getCategoryData'] = $this->admin_model->getCategoryData();

        $this->load->view('admin/add_subcategory.php', $getCategoryData);

    }
    public function edit_subcategory($subcategoryId)
    {
        $getSubCategoryData['subcategoryData'] = $this->admin_model->getSingleSubCategoryById($subcategoryId);
        $getSubCategoryData['getCategoryData'] = $this->admin_model->getCategoryData();
        if (!empty($getSubCategoryData)) {
            $this->load->view('admin/edit_subcategory', $getSubCategoryData);
        }
        else{
            redirect(site_url("admin/admin/manage_subcategory/"));
        }
    }
    public function updatesubCategory()
    {
        if (isset($_POST['category_id'])) {
            $Data['category_id'] = $_POST['category_id'];
            $Data['id'] = $_POST['id'];
            $Data['sub_category'] = $_POST['sub_category'];
            $Data['description'] = $_POST['description'];
            $this->form_validation->set_data($Data);
            $this->form_validation->set_rules('category_id', 'category id', 'trim|required');
            $this->form_validation->set_rules('sub_category', 'sub_category', 'trim|required');
            if ($this->form_validation->run() == false) {
                $_SESSION['error'] = strip_tags(validation_errors());
                redirect(site_url("admin/admin/edit_subCategory/".$_POST['id']));
            } else {
                $this->admin_model->updatesubCategory($Data,$_POST['id']);
                $_SESSION['success'] = "Sub Category Updated Successfully";
                redirect(site_url("admin/admin/manage_subcategory"));

            }
        }
        $getCategoryData['getCategoryData'] = $this->admin_model->getCategoryData();

        $this->load->view('admin/add_subcategory.php', $getCategoryData);

    }
   
    public function deleteSubcategory($id)
    {
        $status = 0;
        $this->admin_model->deleteSubcategory($id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_subcategory/"));
    }
    public function manage_product()
    {
        $getProductData['getProductData'] = $this->admin_model->getProductData();
        if (!empty($getProductData)) {
            $this->load->view('admin/manage_product.php', $getProductData);
        }
    }
    public function addProduct()
    {
        if (isset($_POST['category_id'])) {
            $Data['category_id'] = $_POST['category_id'];
            $Data['sub_category_id'] = $_POST['sub_category_id'];
            $Data['name'] = $_POST['name'];
            $Data['price'] = $_POST['price'];
            $Data['qty'] = $_POST['qty'];
            $Data['description'] = $_POST['description'];
            $this->form_validation->set_data($Data);
            $this->form_validation->set_rules('category_id', 'category id', 'trim|required');
            $this->form_validation->set_rules('sub_category_id', 'sub_category', 'trim|required');
            $this->form_validation->set_rules('name', 'name', 'trim|required');
            $this->form_validation->set_rules('price', 'price', 'trim|required');
            $this->form_validation->set_rules('qty', 'qty', 'trim|required');
            if ($this->form_validation->run() == false) {
                $_SESSION['error'] = strip_tags(validation_errors());
                redirect(site_url("admin/admin/addProduct"));
            } else {

                if (isset($_FILES['product_image']['name']) && !empty($_FILES['product_image']['name'])) {
                    $fileName = $_FILES['product_image']['name'];
                    $tmpName = $_FILES['product_image']['tmp_name'];
                    $uploadPath = './uploads/product_image/';
                    $imageName = $uploadPath . $fileName;
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $actualFileName = time() . "." . $fileExtension;
                    $image_dir = './uploads/product_image/';
                    $imgConfig['upload_path'] = $image_dir;
                    $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
                    $imgConfig['file_name'] = $actualFileName;
                    $this->load->library('upload', $actualFileName);
                    $this->upload->initialize($imgConfig);
                    if ($this->upload->do_upload('product_image')) {
                        $profileImage = $this->upload->data();
                        $Data['product_image'] = $profileImage['file_name'];
                    } else {
                        $error = array('error' => $this->upload->display_errors());
                    }
                }

                $date = date("Y-m-d H:i:s");
                $Data['created_at'] = $date;
                $Data['updated_at'] = $date;
                $this->admin_model->addProduct($Data);
                $_SESSION['success'] = "Product Inserted Successfully";
                redirect(site_url("admin/admin/manage_product"));

            }
        }
        $getCategoryData['getCategoryData'] = $this->admin_model->getCategoryData();

        $this->load->view('admin/add_product.php', $getCategoryData);

    }

    public function deleteProduct($id)
    {
        $status = 0;
        $this->admin_model->deleteProduct($id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_product/"));
    }
    public function getSubcategoryAjax()
    {
        $category = $_GET['category'];
        $categoryList = $this->admin_model->getsubCategoryById($category);
        echo json_encode($categoryList);
    }
    
    public function manage_garage_service($garage_id)
    {
        $getServicesData['garage_id'] = $garage_id;
        $getServicesData['getGarageData'] = $this->admin_model->GarageServicesData($garage_id);
        if (!empty($getServicesData)) {
            $this->load->view('admin/view_garage_service.php', $getServicesData);
        }
    }
    
    public function manage_WashCenter()
    {
        $getWashCenterData['getWashCenterData'] = $this->admin_model->CarWashCenterData();
        if (!empty($getWashCenterData)) {
            $this->load->view('admin/manage_WashCenter.php', $getWashCenterData);
        }
    }

    public function manage_Service($center_id)
    {
        $getServicesData['center_id'] = $center_id;
        $getServicesData['getServicesData'] = $this->admin_model->ServicesData($center_id);
        if (!empty($getServicesData)) {
            $this->load->view('admin/view_service.php', $getServicesData);
        }
    }

    public function manage_Services()
    {
        $getServicesData['getServicesData'] = $this->admin_model->getAllGarageServices();
        if (!empty($getServicesData)) {
            $this->load->view('admin/view_services.php', $getServicesData);
        }
    }

    public function addSubservice($service_id)
    {
        $data['service_id']=$service_id;
        $this->load->view('admin/add_sub_service.php', $data);
    }

    public function insertSubservice()
   {
    $Data['service_id'] = $_POST['service_id'];
    $Data['service_name'] = $_POST['sub_service_name'];
    $Data['service_price'] = $_POST['sub_service_price'];
    $this->form_validation->set_data($Data);
    $this->form_validation->set_rules('sub_service_name', 'Service Name', 'trim|required');
    $this->form_validation->set_rules('sub_service_price', 'Service Price', 'trim|required');
    if ($this->form_validation->run() == false && $Data['service_name'] !='' && $Data['service_price']!='') {
        
            if ($this->admin_model->addSubServices($Data)) {

                $_SESSION['success'] = "Service Inserted Successfully";
                redirect(site_url("admin/admin/listSubservice/".$Data['service_id']));
            } else {
                
                $_SESSION['error'] = strip_tags('Insert Error');
                redirect(site_url("admin/admin/addSubservice/".$_POST['service_id']));
               
            }
        
    }
    else{
        $_SESSION['error'] = strip_tags(validation_errors());
        redirect(site_url("admin/admin/addSubservice/".$_POST['service_id']));
    }
   }

   public function listSubService($service_id)
   {
       $data['getServicesData']=$this->admin_model->getSubserviceList($service_id);
       $data['service_id']=$service_id;
       $this->load->view('admin/list_subservice.php', $data);

   }
   public function editSubService($sub_service_id)
   {
       $data['subserviceData']=$this->admin_model->getSubserviceById($sub_service_id);
       $this->load->view('admin/edit_subservice.php', $data);

   }
   public function updateSubservice()
   {
    if (isset($_POST['sub_service_id'])) {
        $Data['service_name'] = $_POST['sub_service_name'];
        $Data['service_price'] = $_POST['sub_service_price'];
        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('service_name', 'service_name', 'trim|required');
        $this->form_validation->set_rules('service_price', 'service_price', 'trim|required');
        if ($this->form_validation->run() == false) {
            
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/editSubService/".$_POST['sub_service_id']));
        } else {
           
            if($this->admin_model->updateSubServices($Data,$_POST['sub_service_id']))
            {
                $_SESSION['success'] = "Updated Successfully";
                redirect(site_url("admin/admin/listSubservice/".$_POST['service_id']));
                
            }
            else{
                $_SESSION['error'] =  "OOPS! Try Again Later";
                redirect(site_url("admin/admin/editSubService/".$_POST['sub_service_id']));
            }
            

        }
    }
    else{
        $_SESSION['error'] = 'Please fill all fields';
                redirect(site_url("admin/admin/editSubService/".$_POST['sub_service_id']));
    }
   }
   public function deleteSubservice($subservice_id,$service_id)
   {
       if($this->admin_model->deleteSubservices($subservice_id))
       {
       $_SESSION['success'] = "Delete Successfully";
       redirect(site_url("admin/admin/listSubservice/".$service_id));
       }
       else{
        $_SESSION['error'] = "OOPS! Try Again Later";
        redirect(site_url("admin/admin/listSubservice/".$service_id));
       }
   }
    public function deleteWashCenter($center_id)
    {
        $center_id = $center_id;
        $this->admin_model->deleteWashCenter($center_id);
        $this->admin_model->deleteWashServices($center_id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_WashCenter/"));
    }

    public function deleteGarage($garage_id, $make_id)
    {
        $make_id = $make_id;
        $garage_id = $garage_id;
        $this->admin_model->deleteGarage($garage_id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/view_garage/$make_id"));
    }

    public function deleteServices($service_id, $center_id)
    {
        $center_id = $center_id;
        $service_id = $service_id;
        $this->admin_model->deleteServices($service_id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_Service/$center_id"));
    }

    public function deleteService($service_id)
    {
        $this->admin_model->deleteMainService($service_id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_Services"));
    }
    public function deleteGarageServices($service_id, $garage_id)
    {
        $garage_id = $garage_id;
        $service_id = $service_id;
        $this->admin_model->deleteGarageServices($service_id);
        $_SESSION['success'] = "Delete Successfully";
        redirect(site_url("admin/admin/manage_Service/$garage_id"));
    }
    public function addWashCenter()
    {
        $this->load->view('admin/add_washCenter.php');
    }

    public function addGarage($id)
    {
        $data['make_id'] = $id;
        $this->load->view('admin/add_garage.php', $data);
    }
    public function addContent()
    {
        $this->load->view('admin/add_content.php');
    }
    public function manage_content()
    {
        $getContentData['getContentData'] = $this->admin_model->getContentData();
        if (!empty($getContentData)) {
            $this->load->view('admin/manage_content.php', $getContentData);
        }
    }
    public function contentAdd()
    {
        $Data['title'] = $_POST['content_title'];
        $Data['content'] = $_POST['content'];
        $Data['language'] = $_POST['language'];
        $Data['type'] = strtolower(str_replace(' ', '_', $Data['title']));

        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('title', 'title', 'trim|required');
        $this->form_validation->set_rules('content', 'content', 'trim|required');
        $date = date("Y-m-d H:i:s");
        $Data['created_at'] = $date;
        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            // redirect(site_url("admin/admin/addGarage"));
            $this->load->view('admin/admin/addContent');
        } else {

            $this->admin_model->addContent($Data);

            $_SESSION['success'] = "Content Inserted Successfully";
            redirect(site_url("admin/admin/manage_content"));

        }
    }
    public function editContent($id)
    {
        $data['contentData'] = $this->admin_model->getContentDataByID($id);
        $this->load->view('admin/edit_content.php', $data);
    }
    public function contentEdit()
    {
        $Data['title'] = $_POST['content_title'];
        $Data['content'] = $_POST['content'];
        $Data['language'] = $_POST['language'];
        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('title', 'title', 'trim|required');
        $this->form_validation->set_rules('content', 'content', 'trim|required');
        $date = date("Y-m-d H:i:s");
        $Data['created_at'] = $date;
        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            // redirect(site_url("admin/admin/addGarage"));
            $this->load->view('admin/admin/editContent');
        } else {
            $this->admin_model->editContent($Data, $_POST['content_id']);
            $_SESSION['success'] = "Content edit Successfully";
            redirect(site_url("admin/admin/manage_content"));

        }
    }
    public function addServices($center_id)
    {
        $data['center_id'] = $center_id;
        $this->load->view('admin/add_services.php', $data);
    }

    public function addMainService()
    {
        $this->load->view('admin/add_main_service.php');
    }

    public function insertService()
    {

        $Data['service_type'] = $_POST['service_name'];
        // $Data['service_price'] = $_POST['service_price'];
        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('service_name', 'service_name', 'trim|required');
        $this->form_validation->set_rules('service_price', 'service_price', 'trim|required');
        if ($this->form_validation->run() == false) {
            
            if (isset($_FILES['service_image']['name']) && !empty($_FILES['service_image']['name'])) {
                $fileName = $_FILES['service_image']['name'];
                $tmpName = $_FILES['service_image']['tmp_name'];
                $uploadPath = './uploads/service_images/';
                $imageName = $uploadPath . $fileName;
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $actualFileName = time() . "." . $fileExtension;
                $image_dir = './uploads/service_images/';
                $imgConfig['upload_path'] = $image_dir;
                $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
                $imgConfig['file_name'] = $actualFileName;
                $this->load->library('upload', $actualFileName);
                $this->upload->initialize($imgConfig);
                if ($this->upload->do_upload('service_image')) {
                    $profileImage = $this->upload->data();
                    $Data['service_image'] = $profileImage['file_name'];
                    $this->admin_model->addMainServices($Data);

                    $_SESSION['success'] = "Service Inserted Successfully";
                    redirect(site_url("admin/admin/manage_Services/"));
                } else {
                    
                    $error = array('error' => $this->upload->display_errors());
                    $_SESSION['error'] = strip_tags($error);
                    redirect(site_url("admin/admin/addMainService/"));
                   
                }
            }
            else{
                $_SESSION['error'] = strip_tags('File upload Error');
            redirect(site_url("admin/admin/addMainService/"));
            }
        }
        else{
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/addMainService/"));
        }
    }
    public function addGarageServices($garage_id)
    {
        $data['garage_id'] = $garage_id;
        $this->load->view('admin/add_garage_services.php', $data);
    }

    public function ServiceAdd()
    {
        $Data['service_name'] = $_POST['service_name'];
        $Data['service_price'] = $_POST['service_price'];
        $Data['center_id'] = $_POST['center_id'];
        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('service_name', 'service_name', 'trim|required');
        $this->form_validation->set_rules('service_price', 'service_price', 'trim|required');
        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/addMainService"));
        } else {
            $this->admin_model->addMainServices($Data);

            $_SESSION['success'] = "Service Inserted Successfully";
            redirect(site_url("admin/admin/manage_Services/"));

        }
    }
    public function garageServiceAdd()
    {
        $Data['service_name'] = $_POST['service_name'];
        $Data['service_price'] = $_POST['service_price'];
        $Data['garage_id'] = $_POST['garage_id'];
        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('service_name', 'service_name', 'trim|required');
        $this->form_validation->set_rules('service_price', 'service_price', 'trim|required');
        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            redirect(site_url("admin/admin/addGarageServices"));
        } else {
            $this->admin_model->addGarageServices($Data);

            $_SESSION['success'] = "Service Inserted Successfully";
            redirect(site_url("admin/admin/manage_garage_service/" . $Data['garage_id']));

        }
    }

    public function GarageAdd()
    {
        $Data['garage_name'] = $_POST['garage_name'];
        $Data['garage_location'] = $_POST['garage_location'];
        $Data['garage_lat'] = $_POST['garage_lat'];
        $Data['garage_long'] = $_POST['garage_long'];
        $Data['garage_description'] = $_POST['garage_description'];
        $Data['make_id'] = $_POST['make_id'];

        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('garage_name', 'garage_name', 'trim|required');
        $this->form_validation->set_rules('garage_location', 'garage_location', 'trim|required');
        if (isset($_FILES['garage_logo']['name']) && !empty($_FILES['garage_logo']['name'])) {
            $fileName = $_FILES['garage_logo']['name'];
            $tmpName = $_FILES['garage_logo']['tmp_name'];
            $uploadPath = './uploads/garage_logo/';
            $imageName = $uploadPath . $fileName;
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $actualFileName = time() . "." . $fileExtension;
            $image_dir = './uploads/garage_logo/';
            $imgConfig['upload_path'] = $image_dir;
            $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
            $imgConfig['file_name'] = $actualFileName;
            $this->load->library('upload', $actualFileName);
            $this->upload->initialize($imgConfig);
            if ($this->upload->do_upload('garage_logo')) {
                $profileImage = $this->upload->data();
                $Data['garage_logo'] = $profileImage['file_name'];
            } else {
                $error = array('error' => $this->upload->display_errors());
            }
        }

        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            // redirect(site_url("admin/admin/addGarage"));
            $this->load->view('admin/admin/addGarage');
        } else {

            $this->admin_model->addGarage($Data);

            $_SESSION['success'] = "Garage Inserted Successfully";
            redirect(site_url("admin/admin/view_garage/" . $Data['make_id']));

        }
    }

    public function WashCenterAdd()
    {
        $Data['center_name'] = $_POST['center_name'];
        $Data['center_location'] = $_POST['center_location'];
        $Data['center_lat'] = $_POST['center_lat'];
        $Data['center_long'] = $_POST['center_long'];
        $Data['center_description'] = $_POST['center_description'];

        $this->form_validation->set_data($Data);
        $this->form_validation->set_rules('center_name', 'center_name', 'trim|required');
        $this->form_validation->set_rules('center_location', 'center_location', 'trim|required');

        if (isset($_FILES['center_logo']['name']) && !empty($_FILES['center_logo']['name'])) {
            $fileName = $_FILES['center_logo']['name'];
            $tmpName = $_FILES['center_logo']['tmp_name'];
            $uploadPath = './uploads/center_logo/';
            $imageName = $uploadPath . $fileName;
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $actualFileName = time() . "." . $fileExtension;
            $image_dir = './uploads/center_logo/';
            $imgConfig['upload_path'] = $image_dir;
            $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
            $imgConfig['file_name'] = $actualFileName;
            $this->load->library('upload', $actualFileName);
            $this->upload->initialize($imgConfig);
            if ($this->upload->do_upload('center_logo')) {
                $profileImage = $this->upload->data();
                $Data['center_logo'] = $profileImage['file_name'];
            } else {
                $error = array('error' => $this->upload->display_errors());
            }
        }

        if ($this->form_validation->run() == false) {
            $_SESSION['error'] = strip_tags(validation_errors());
            // redirect(site_url("admin/admin/addGarage"));
            $this->load->view('admin/admin/addWashCenter');
        } else {
            $this->admin_model->addWashCenter($Data);

            $_SESSION['success'] = "Wash Center Inserted Successfully";
            redirect(site_url("admin/admin/manage_WashCenter/"));

        }
    }

    public function garage_booking_data()
    {
        $getData['getData'] = $this->admin_model->garage_booking_data();
        if (!empty($getData)) {
            $this->load->view('admin/garage_booking_data.php', $getData);
        }
    }

    public function services_booking_data()
    {
        $getServicesData['getServicesData'] = $this->admin_model->services_booking_data();
        if (!empty($getServicesData)) {
            $this->load->view('admin/services_booking_data.php', $getServicesData);
        }
    }

    public function garageBookingUpdate($bookingId)
    {
        $booking_id = $bookingId;
        $this->admin_model->garageBooking_status($bookingId);
        $_SESSION['success'] = "Pay Sucssesfully Successfully";
        redirect(site_url("admin/admin/garage_booking_data/"));
    }

    public function ServiceBookingUpdate($bookingId)
    {
        $booking_id = $bookingId;
        $this->admin_model->servicesBooking_status($bookingId);
        $_SESSION['success'] = "Pay Sucssesfully Successfully";
        redirect(site_url("admin/admin/services_booking_data/"));
    }
    public function manage_promo()
    {
        $getPromoData['getPromoData'] = $this->admin_model->getPromoData();
        if (!empty($getPromoData)) {
            $this->load->view('admin/manage_promo.php', $getPromoData);
        }
    }
    public function view_promo($id)
    {
        $getPromoData['getPromoData'] = $this->admin_model->getPromoDataByID($id);
        $getPromoData['ServiceType'] = $this->user_model->fetchServiceType();
        if (!empty($getPromoData)) {
            $this->load->view('admin/view_promo.php', $getPromoData);
        }
    }
    public function manage_booking_service()
    {
        $getPromoData['getPromoData'] = $this->admin_model->getPromoData();
        if (!empty($getPromoData)) {
            $this->load->view('admin/manage_promo.php', $getPromoData);
        }
    }
    public function addPromo()
    {
        if (isset($_POST['promo_code'])) {
            $Data['promo_code'] = $_POST['promo_code'];
            $Data['percentage'] = $_POST['percentage'];
            $Data['expiry_date'] = $_POST['expiry_date'];
            $Data['description'] = $_POST['description'];
            $Data['service_type'] = json_encode($_POST['service_type']);
            $this->form_validation->set_data($Data);
            $this->form_validation->set_rules('promo_code', 'Promocode', 'trim|required');
            $this->form_validation->set_rules('percentage', 'Discount Percentage', 'trim|required');
            $this->form_validation->set_rules('expiry_date', 'End Date', 'trim|required');
            $this->form_validation->set_rules('service_type', 'Service Type', 'trim|required');
            if ($this->form_validation->run() == false) {
                $_SESSION['error'] = strip_tags(validation_errors());
                redirect(site_url("admin/admin/addPromo"));
            } else {
                $date = date("Y-m-d H:i:s");
                $Data['expiry_date'] =  $Data['expiry_date'].' 23:59:59';
                $Data['created_at'] = $date;
                $Data['updated_at'] = $date;
                if(isset($_POST['promo_id'])){
                    $this->admin_model->editPromo($Data, $_POST['promo_id']);
                    $_SESSION['success'] = "Promotion Code Edit Successfully";
                } else {
                    $this->admin_model->addPromo($Data);
                    $_SESSION['success'] = "Promotion Code Inserted Successfully";
                }
                redirect(site_url("admin/admin/manage_promo"));
                

            }
        }
        $getServiceTypeData['ServiceType'] = $this->user_model->fetchServiceType();

        $this->load->view('admin/add_promo.php', $getServiceTypeData);

    }


    public function list_past_orders()
    {
       
            $responseData = array();
            $rowData = $this->admin_model->past_Order();
            if (!empty($rowData)) {
                foreach ($rowData as $orderKey => $orderValue) {
                    $reviewData = $this->user_model->fetchReviewByBookingId($orderValue['booking_id']);
                    if (!empty($reviewData)) {
                        $responseData[$orderKey]['review_star'] = isset($reviewData->review_star) && trim($reviewData->review_star) != null ? $reviewData->review_star : "";
                        $responseData[$orderKey]['review'] = isset($reviewData->review) && trim($reviewData->review) != null ? $reviewData->review : "";
                    }
                    $responseData[$orderKey]['user_id'] = $orderValue['userId'];
                    $responseData[$orderKey]['user_name'] = $orderValue['userFirstName'].' '.$orderValue['userLastName'];
                    $responseData[$orderKey]['provider_id'] = $orderValue['providerId'];
                    $responseData[$orderKey]['provider_name'] = $orderValue['providerFirstName'].' '.$orderValue['providerLastName'];
                    $responseData[$orderKey]['make'] = $orderValue['make'];
                    $responseData[$orderKey]['model'] = $orderValue['model'];
                    $responseData[$orderKey]['invoice_no'] = $orderValue['invoice_no'];
                    $responseData[$orderKey]['booking_id'] = $orderValue['booking_id'];
                    $responseData[$orderKey]['total_amount'] = $orderValue['total_amount'];
                    $responseData[$orderKey]['vat_amount'] = $orderValue['vat_amount'];
                    $responseData[$orderKey]['plan_id'] = $orderValue['plan_id'];
                    $responseData[$orderKey]['instruction'] = $orderValue['instruction'];
                    $responseData[$orderKey]['location'] = isset($orderValue['location']) && trim($orderValue['location']) != null ? $orderValue['location'] : '';
                    $responseData[$orderKey]['location_lat'] = isset($orderValue['location_lat']) && trim($orderValue['location_lat']) != null ? $orderValue['location_lat'] : '';
                    $responseData[$orderKey]['location_long'] = isset($orderValue['location_long']) && trim($orderValue['location_long']) != null ? $orderValue['location_long'] : '';
                    $responseData[$orderKey]['booking_status'] = $orderValue['booking_status'];
                    $responseData[$orderKey]['booked_on'] = date('d/m/Y', $orderValue['booked_on']);
                    $responseData[$orderKey]['booking_date_time'] = isset($orderValue['booking_date_time']) && trim($orderValue['booking_date_time']) != null ? $orderValue['booking_date_time'] : "";
                    if ($orderValue['booking_code'] != null) {
                        $responseData[$orderKey]['booking_code'] = $orderValue['booking_code'];
                    }
                    $responseData[$orderKey]['service_name'] = $orderValue['serviceName'];
                    // echo $orderValue['service_type'].'<br>';
                    $responseData[$orderKey]['sub_service_id'] = $orderValue['sub_service_id'];
                    $responseData[$orderKey]['invoice_path'] = ($orderValue['invoice_path'] != null) ? base_url() . 'uploads/invoices/' . $orderValue['invoice_path'] : null;
                }

            }
            $getOrderList['orderList']=$responseData;
            if (!empty($responseData)) {
                $this->load->view('admin/past_order_list.php', $getOrderList);
            }
       
    }

    public function get_upcoming_order()
    {
                $responseData = array();
                $rowData = $this->admin_model->upcoming_Order();
                
                $requestOrders = $this->admin_model->getRequestRecoveryOrders();
                        foreach ($requestOrders as $reqOrder) {
                            array_push($rowData, $reqOrder);
                        }
                if (!empty($rowData)) {
                    foreach ($rowData as $orderKey => $orderValue) {
                        $responseData[$orderKey]['user_id'] = $orderValue['userId'];
                    $responseData[$orderKey]['user_name'] = $orderValue['userFirstName'].' '.$orderValue['userLastName'];
                    $responseData[$orderKey]['provider_id'] = $orderValue['providerId'];
                    $responseData[$orderKey]['provider_name'] = $orderValue['providerFirstName'].' '.$orderValue['providerLastName'];
                        $responseData[$orderKey]['garage_id'] = $orderValue['garage_id'];
                        $responseData[$orderKey]['make'] = $orderValue['make'];
                        $responseData[$orderKey]['model'] = $orderValue['model'];
                        $responseData[$orderKey]['invoice_no'] = $orderValue['invoice_no'];
                        $responseData[$orderKey]['booking_id'] = $orderValue['booking_id'];
                        $responseData[$orderKey]['total_amount'] = $orderValue['total_amount'];
                        $responseData[$orderKey]['paid_amount'] = $orderValue['paid_amount'];
                        $responseData[$orderKey]['instruction'] = $orderValue['instruction'];
                        $responseData[$orderKey]['vat_amount'] = $orderValue['vat_amount'];
                        $responseData[$orderKey]['balance_amount'] = $orderValue['total_amount'] - $orderValue['paid_amount'];
                        $responseData[$orderKey]['plan_id'] = $orderValue['plan_id'];
                        $responseData[$orderKey]['is_paid'] = $orderValue['is_paid'];
                        $responseData[$orderKey]['payment_method'] = $orderValue['payment_method'];
                        $responseData[$orderKey]['location'] = isset($orderValue['location']) && trim($orderValue['location']) != null ? $orderValue['location'] : '';
                        $responseData[$orderKey]['location_lat'] = isset($orderValue['location_lat']) && trim($orderValue['location_lat']) != null ? $orderValue['location_lat'] : '';
                        $responseData[$orderKey]['location_long'] = isset($orderValue['location_long']) && trim($orderValue['location_long']) != null ? $orderValue['location_long'] : '';
                         if ($orderValue['booking_status'] == 'open' && $orderValue['user_type'] == 2) {
                            $responseData[$orderKey]['booking_status'] = 'pending';
                         } else {

                            $responseData[$orderKey]['booking_status'] = $orderValue['booking_status'];
                         }
                        $responseData[$orderKey]['booked_on'] = date('d/m/Y', $orderValue['booked_on']);
                        $responseData[$orderKey]['service_name'] = $orderValue['serviceName'];
                        $responseData[$orderKey]['booking_date_time'] = isset($orderValue['booking_date_time']) && trim($orderValue['booking_date_time']) != null ? $orderValue['booking_date_time'] : "";
                        if ($orderValue['booking_code'] != null) {
                            $responseData[$orderKey]['booking_code'] = $orderValue['booking_code'];
                        }
                        if ($orderValue['service_id'] == 2) {
                            $responseData[$orderKey]['half_amount'] = $orderValue['total_amount'] / 2;
                            $getQuotationStatus = $this->user_model->getQuotationStatus($orderValue['booking_id'], $orderValue['user_type'] );
                            $getInvoiceStatus = $this->user_model->getInvoiceStatus($orderValue['booking_id']);
                            if (!empty($getQuotationStatus)) {
                                $responseData[$orderKey]['quotation_status'] = $getQuotationStatus->status;
                            } else {
                                $responseData[$orderKey]['quotation_status'] = 0;
                            }
                            if (!empty($getInvoiceStatus) || $orderValue['is_invoice'] == 1) {
                                $responseData[$orderKey]['invoice_status'] = 1;
                                $responseData[$orderKey]['quotation_status'] = 'accept';
                            } else {

                                $responseData[$orderKey]['invoice_status'] = 0;
                            }
                        }
                        $responseData[$orderKey]['sub_service_id'] = $orderValue['sub_service_id'];
                        $responseData[$orderKey]['invoice_path'] = ($orderValue['invoice_path'] != null) ? base_url() . 'uploads/invoices/' . $orderValue['invoice_path'] : null;

                    }

                }

                $getOrderList['orderList']=$responseData;
                if (!empty($responseData)) {
                    $this->load->view('admin/upcoming_order_list.php', $getOrderList);
                }
              
            
        
    }

}