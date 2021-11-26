<?php
class Driver Extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getDate()
    {
        return time();
    } 

    public function getHeaders()
    {
		$headers=apache_request_headers();
		return $headers;	
    }
        
    protected function chckToken()
    {
        $headerData=$this->getHeaders();
        if(isset($headerData['Usertoken']) && trim($headerData['Usertoken'])!=null)
        {
            $decodetoken= jwt::decode($headerData['Usertoken'],jwtKey);
            return  $decodetoken;
        }
        else 
        {
            echo json_encode(array("statusCode"=>400,"message"=>"Required userToken not sent."));
        }
    }

    private function user_authenticate($token)
	{
		if(!empty($token)){
			return $this->driver_model->get_authenticate($token);
		}
    }

    public function get_random_code()
    {
        $number= mt_rand(1000, 9999);
        $count=$this->driver_model->ResetCode_exists($number);
        if($count>0)
       {
           $number=$this->get_random_code();
           
       }
       return $number;
    }

   public function userRegister()
    {
       $userRegisterData=json_decode(trim(file_get_contents("php://input")),true);
       if(!empty($userRegisterData))
        {
            $this->form_validation->set_data($userRegisterData);
            $this->form_validation->set_rules('phone_number','Mobile no','trim|required|is_unique[user_master.user_phone]');
            $this->form_validation->set_rules('password','password','trim|required');
            $this->form_validation->set_rules('user_lat','user_lat','trim|required');
            $this->form_validation->set_rules('user_long','user_long','trim|required');
            $this->form_validation->set_rules('device_token','device_token','trim|required');
            if($this->form_validation->run()==FALSE)
            {
                echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
            }
            else
            {
                $verification_code =$this->get_random_code();
                $insertData['user_password']=md5($userRegisterData['password']);
                $insertData['user_phone']=$userRegisterData['phone_number'];
                $insertData['user_lat']=$userRegisterData['user_lat'];
                $insertData['user_long']=$userRegisterData['user_long'];
                $insertData['verification_code']= $verification_code;
                $insertData['device_token']=$userRegisterData['device_token'];
                $insertData['user_created_on']=$this->getDate();            
                $insertData['user_updated_on']=$this->getDate();
                $insertData['user_type']=0;
                $insertData['is_verified']=1;
                $getUserId=$this->driver_model->insert($insertData);

                $token= jwt::encode($getUserId,jwtKey);
                // $response=array(
                //    'verify_id'   =>$getUserId,                                     
                //    'verification_code' =>$verification_code,                      

                //    );
                $response=array(
                                'verify_id'=>$getUserId,
                                'user_phone'=>$userRegisterData['phone_number'],
                                'device_token'=>$userRegisterData['device_token'],	
                                'user_status'=>"1",
                                'user_updated_on'=>$insertData['user_created_on'],
                                'user_created_on'=>$insertData['user_created_on'],
                                'userToken'=>$token
                                );
                   echo json_encode(array("statusCode"=>200,"message"=>"Successfully register.","data"=>$response)); 
            }
        }
        else
        {
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Data"));
        }
    }

    public function verify_driver()
    {
        $getData=json_decode(trim(file_get_contents("php://input")),true);
        if(!empty($getData))
        {
            $this->form_validation->set_data($getData);
            $this->form_validation->set_rules('verify_id','verify_id','trim|required');
            $this->form_validation->set_rules('otp','otp','trim|required');
            if($this->form_validation->run()==FALSE)
            {
                echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
            }
            else
            {
                //$chkData=$this->user_model->user_authenticate($getData['verify_id']);
                if(!empty($getData))
                {
                    $verify_id=$getData['verify_id'];
                    $verification_code=$getData['otp'];
                    $rowData=$this->driver_model->verify_driver($verify_id,$verification_code); 
                    if(!empty($rowData))
                    {
                        $updateIsVerify['is_verified']='1'; 
                        $this->driver_model->user_update($updateIsVerify,$verify_id,'user_master');
                        $token= jwt::encode($rowData->user_id,jwtKey);
                        $response=array(
                                        'verify_id'=>$rowData->user_id,
                                        'user_phone'=>$rowData->user_phone,
                                        'device_token'=>$rowData->device_token,	
                                        'user_status'=>$rowData->user_status,
                                        'user_updated_on'=>$rowData->user_updated_on,
                                        'user_created_on'=>$rowData->user_created_on,
                                        'userToken'=>$token
                                        );
                            echo json_encode(array("statusCode"=>200,"data"=>$response,"message"=>"Verify successfully")); 
                    }
                    else
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>"invalid otp"));
                    } 
                    
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"User Not Exist"));
                }
            }
        }
    }

    public function resend_otp()
    {
        $getData=json_decode(trim(file_get_contents("php://input")),true);
        if(isset($getData['verification_id']) && trim($getData['verification_id']) != null)
        {
            $chckData = $this->driver_model->getDriver($getData['verification_id']);
            //var_dump($chckData);
            if(!empty($chckData))
            {
                $data=array();
                $data['verification_code']=$this->get_random_code();
                $this->driver_model->user_update($data,$chckData->user_id,'user_master');
                echo json_encode(array("statusCode"=>200,"message"=>"New otp send to your registered mobile number","data"=>$data));
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"verification ID is Not Valid"));
            }
        }
        else
        {
            echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
        }  
    }

   public function login()
    {
        
            $getData=json_decode(trim(file_get_contents("php://input")),true);
          
                if(!empty($getData))
                {
                    $this->form_validation->set_data($getData);
                    $this->form_validation->set_rules('user_phone','user_phone','trim|required');
                    $this->form_validation->set_rules('user_password','user_password','trim|required');
                    $this->form_validation->set_rules('device_token','device_token','trim|required');
                    $this->form_validation->set_rules('user_type','user_type','trim|required');
                    if($this->form_validation->run()==FALSE)
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                    }
                    else
                    {
                        $getuser_phone=$getData['user_phone'];
                        $getuser_password=md5($getData['user_password']);
                        $user_type=$getData['user_type'];
                        $rowData=$this->driver_model->userLogin($user_type,$getuser_phone,$getuser_password);
                        if(!empty($rowData))
                        {
                            $tokenData['device_token']=$getData['device_token'];
                            $this->driver_model->user_update($tokenData,$rowData->user_id,'user_master');

                            $token= jwt::encode($rowData->user_id,jwtKey);
							if($rowData->is_verified==1 )
							{
								if($rowData->driver_verified==1)
								{
								$response=array(
                                            'user_id'=>$rowData->user_id,
                                            'user_first_name'=>isset($rowData->user_first_name) && trim($rowData->user_first_name)!=null?$rowData->user_first_name:'',
                                            'user_last_name'=>isset($rowData->user_last_name) && trim($rowData->user_last_name)!=null?$rowData->user_last_name:'',
                                            'user_image'=>isset($rowData->user_image) && trim($rowData->user_image)!=null?base_url().'uploads/profile_images/'.$rowData->user_image:'',
                                            // 'user_email'=>$rowData->user_email,
                                            'driver_verified'=>isset($rowData->driver_verified) && trim($rowData->driver_verified)!=null?$rowData->driver_verified:'',
                                            'is_verified'=>isset($rowData->is_verified) && trim($rowData->is_verified)!=null?$rowData->is_verified:'',
                                            'user_phone'=>isset($rowData->user_phone) && trim($rowData->user_phone)!=null?$rowData->user_phone:'',
                                            'user_status'=>isset($rowData->user_status) && trim($rowData->user_status)!=null?$rowData->user_status:'',
                                            'userToken'=>$token
                                            );
											echo json_encode(array("statusCode"=>200,"data"=>$response,"message"=>"Login successfully"));
								}
								else
								{
									echo json_encode(array("statusCode"=>400,"message"=>"Driver is not verified from Admin end"));
								}								
							} 
							 else
							{
								echo json_encode(array("statusCode"=>400,"message"=>"Verification is not complete"));
							} 
                        }
                        else
                        {
                            echo json_encode(array("statusCode"=>400,"message"=>"Invalid User"));
                        }              
                    }
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                }
           
    }


    public function Add_detail()
	{
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $profile_Data = $this->input->post();
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                if(!empty($profile_Data))
                 { 
                    $this->form_validation->set_data($profile_Data);
                    $this->form_validation->set_rules('user_first_name','user_first_name','trim|required');
                    $this->form_validation->set_rules('user_last_name','user_last_name','trim|required');
                    if($this->form_validation->run()==FALSE)
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                    }
                    else
                    {
                       //user master
                      
                        $updateUserMaster['user_first_name']=$profile_Data['user_first_name'];
                        $updateUserMaster['user_last_name']=$profile_Data['user_last_name'];
                       $updateDocument['driver_id']=$token;

                       if(isset($_FILES['user_image']['name']) && trim($_FILES['user_image']['name'])!=null && isset($_FILES['license_front_photo']['name']) && trim($_FILES['license_front_photo']['name'])!=null && isset($_FILES['license_back_photo']['name']) && trim($_FILES['license_back_photo']['name'])!=null && isset($_FILES['motorbike_license_front_photo']['name']) && trim($_FILES['motorbike_license_front_photo']['name']) && isset($_FILES['motorbike_license_back_photo']['name']) && trim($_FILES['motorbike_license_back_photo']['name'])!=null && isset($_FILES['national_id_front_photo']['name']) && trim($_FILES['national_id_front_photo']['name'])!=null && isset($_FILES['national_id_back_photo']['name']) && trim($_FILES['national_id_back_photo']['name']) !=null)
                       {
                           /****************** profile Image**************** */
                           $fileName=$_FILES['user_image']['name'];
                           $tmpName=$_FILES['user_image']['tmp_name'];
                           $uploadPath='uploads/profile_images/';
                           $imageName=$uploadPath.$fileName;
                           $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                           $actualFileName=time().".".$fileExtension;
                           $image_dir	='uploads/profile_images';
                           $imgConfig['upload_path']	=	$image_dir;
                           $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                           $imgConfig['file_name']		=	$actualFileName;
                           $this->load->library('upload', $actualFileName); 
                           $this->upload->initialize($imgConfig);
                           if($this->upload->do_upload('user_image'))
                           { 
                               $profileImage= $this->upload->data();
                               $updateUserMaster['user_image']=$profileImage['file_name'];
                           } 
                           else
                           {
                               $error = array('error' => $this->upload->display_errors());
                               var_dump($error);
                           }

                            /****************** license Front Image**************** */
                            $fileName=$_FILES['license_front_photo']['name'];
                            $tmpName=$_FILES['license_front_photo']['tmp_name'];
                            $uploadPath='uploads/document/';
                            $imageName=$uploadPath.$fileName;
                            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                            $actualFileName=time().".".$fileExtension;
                            $image_dir	='uploads/document';
                            $imgConfig['upload_path']	=	$image_dir;
                            $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name']		=	$actualFileName;
                            $this->load->library('upload', $actualFileName); 
                            $this->upload->initialize($imgConfig);
                            if($this->upload->do_upload('license_front_photo'))
                            { 
                                $DocumentImage= $this->upload->data();
                                $updateDocument['license_front_photo']=$DocumentImage['file_name'];
                            } 
                            else
                            {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }

                            /****************** license Back photo**************** */
                            $fileName=$_FILES['license_back_photo']['name'];
                            $tmpName=$_FILES['license_back_photo']['tmp_name'];
                            $uploadPath='uploads/document/';
                            $imageName=$uploadPath.$fileName;
                            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                            $actualFileName=time().".".$fileExtension;
                            $image_dir	='uploads/document';
                            $imgConfig['upload_path']	=	$image_dir;
                            $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name']		=	$actualFileName;
                            $this->load->library('upload', $actualFileName); 
                            $this->upload->initialize($imgConfig);
                            if($this->upload->do_upload('license_back_photo'))
                            { 
                                $DocumentImage= $this->upload->data();
                                $updateDocument['license_back_photo']=$DocumentImage['file_name'];
                            } 
                            else
                            {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }

                            /****************** motorbike license front photo**************** */
                            $fileName=$_FILES['motorbike_license_front_photo']['name'];
                            $tmpName=$_FILES['motorbike_license_front_photo']['tmp_name'];
                            $uploadPath='uploads/document/';
                            $imageName=$uploadPath.$fileName;
                            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                            $actualFileName=time().".".$fileExtension;
                            $image_dir	='uploads/document';
                            $imgConfig['upload_path']	=	$image_dir;
                            $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name']		=	$actualFileName;
                            $this->load->library('upload', $actualFileName); 
                            $this->upload->initialize($imgConfig);
                            if($this->upload->do_upload('motorbike_license_front_photo'))
                            { 
                                $DocumentImage= $this->upload->data();
                                $updateDocument['motorbike_license_front_photo']=$DocumentImage['file_name'];
                            } 
                            else
                            {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }

                            /****************** motorbike license Back photo**************** */
                            $fileName=$_FILES['motorbike_license_back_photo']['name'];
                            $tmpName=$_FILES['motorbike_license_back_photo']['tmp_name'];
                            $uploadPath='uploads/document/';
                            $imageName=$uploadPath.$fileName;
                            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                            $actualFileName=time().".".$fileExtension;
                            $image_dir	='uploads/document';
                            $imgConfig['upload_path']	=	$image_dir;
                            $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name']		=	$actualFileName;
                            $this->load->library('upload', $actualFileName); 
                            $this->upload->initialize($imgConfig);
                            if($this->upload->do_upload('motorbike_license_back_photo'))
                            { 
                                $DocumentImage= $this->upload->data();
                                $updateDocument['motorbike_license_back_photo']=$DocumentImage['file_name'];
                            } 
                            else
                            {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }

                            /****************** national id front photo**************** */
                            $fileName=$_FILES['national_id_front_photo']['name'];
                            $tmpName=$_FILES['national_id_front_photo']['tmp_name'];
                            $uploadPath='uploads/document/';
                            $imageName=$uploadPath.$fileName;
                            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                            $actualFileName=time().".".$fileExtension;
                            $image_dir	='uploads/document';
                            $imgConfig['upload_path']	=	$image_dir;
                            $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name']		=	$actualFileName;
                            $this->load->library('upload', $actualFileName); 
                            $this->upload->initialize($imgConfig);
                            if($this->upload->do_upload('national_id_front_photo'))
                            { 
                                $DocumentImage= $this->upload->data();
                                $updateDocument['national_id_front_photo']=$DocumentImage['file_name'];
                            } 
                            else
                            {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }

                            /****************** national id Back photo**************** */
                            $fileName=$_FILES['national_id_back_photo']['name'];
                            $tmpName=$_FILES['national_id_back_photo']['tmp_name'];
                            $uploadPath='uploads/document/';
                            $imageName=$uploadPath.$fileName;
                            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                            $actualFileName=time().".".$fileExtension;
                            $image_dir	='uploads/document';
                            $imgConfig['upload_path']	=	$image_dir;
                            $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name']		=	$actualFileName;
                            $this->load->library('upload', $actualFileName); 
                            $this->upload->initialize($imgConfig);
                            if($this->upload->do_upload('national_id_back_photo'))
                            { 
                                $DocumentImage= $this->upload->data();
                                $updateDocument['national_id_back_photo']=$DocumentImage['file_name'];
                            } 
                            else
                            {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }
                            $this->driver_model->user_update($updateUserMaster,$get_authenticate->user_id,'user_master');
                            $this->driver_model->insert_certificate($updateDocument);
                            $response=array(
                                'user_first_name'=>$updateUserMaster['user_first_name'],            
                                'user_last_name'=>$updateUserMaster['user_last_name'],                                                                                
                                'user_image'=> isset($updateUserMaster['user_image']) && trim($updateUserMaster['user_image'])!=null?base_url().'uploads/profile_images/'.$updateUserMaster['user_image']:'',                    
                                );
                            echo json_encode(array("statusCode"=>200,"message"=>"Add Detail successfully.","data"=>$response)); 
                       }
                       else
                       {
                            echo json_encode(array("statusCode"=>400,"message"=>"All images are required"));
                       }
                      
                    }
                            
                 }
                 else
                 {
                     echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                 }  
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function logout()
	{
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $get_authenticate = $this->user_authenticate($token);
            //  var_dump($get_authenticate);
            if(!empty($get_authenticate))
            {
                $update_data['device_token']="";
                $this->driver_model->user_update($update_data,$get_authenticate->user_id,'user_master');
                echo json_encode(array("statusCode"=>"200","message"=>"Driver logout successfully"));    
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function updateProfile()
    {
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $profile_Data = $this->input->post();
            $get_authenticate = $this->user_authenticate($token);
            if(!empty($get_authenticate))
            {
                if(!empty($profile_Data))
                { 
                    $updateData['user_first_name']=$get_authenticate->user_first_name;
                    $updateData['user_last_name']=$get_authenticate->user_last_name;
                    $updateData['user_lat']=$get_authenticate->user_lat;
                    $updateData['user_long']=$get_authenticate->user_long;

                    
                    if(isset($profile_Data['user_first_name']) && trim($profile_Data['user_first_name'])!=null)
                    {
                        $updateData['user_first_name']=$profile_Data['user_first_name'];
                    }
                    if(isset($profile_Data['user_last_name']) && trim($profile_Data['user_last_name'])!=null)
                    {
                        $updateData['user_last_name']=$profile_Data['user_last_name'];
                    }
                    if(isset($profile_Data['user_lat']) && trim($profile_Data['user_lat'])!=null)
                    {
                        $updateData['user_lat']=$profile_Data['user_lat'];
                    }
                    if(isset($profile_Data['user_long']) && trim($profile_Data['user_long'])!=null)
                    {
                        $updateData['user_long']=$profile_Data['user_long'];
                    }

                    $updateData['user_image']=$get_authenticate->user_image;
                    if(isset($_FILES['user_image']['name']) && !empty($_FILES['user_image']['name']))
                    {
                        $fileName=$_FILES['user_image']['name'];
                        $tmpName=$_FILES['user_image']['tmp_name'];
                        $uploadPath='uploads/profile_images/';
                        $imageName=$uploadPath.$fileName;
                        $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
                        $actualFileName=time().".".$fileExtension;
                        $image_dir	='uploads/profile_images/';
                        $imgConfig['upload_path']	=	$image_dir;
                        $imgConfig['allowed_types']	=	'gif|jpg|png|jpeg|PNG';
                        $imgConfig['file_name']		=	$actualFileName;
                        $this->load->library('upload', $actualFileName); 
                        $this->upload->initialize($imgConfig);
                        if($this->upload->do_upload('user_image'))
                        { 
                            $profileImage= $this->upload->data();
                            $updateData['user_image']=$profileImage['file_name'];
                        } 
                        else
                        {
                            $error = array('error' => $this->upload->display_errors());
                        }
                    }

                    $this->driver_model->user_update($updateData,$get_authenticate->user_id,'user_master');
                    $response=array();
                    $rowData=$this->driver_model->getDriver($get_authenticate->user_id);
                        if(!empty($rowData))
                        {
                            $response=array(
                            'user_id'=>$rowData->user_id,
                            'user_first_name'=>isset($rowData->user_first_name) && trim($rowData->user_first_name)!=null?$rowData->user_first_name:"",
                            'user_last_name'=>isset($rowData->user_last_name) && trim($rowData->user_last_name)!=null?$rowData->user_last_name:"",
                            'user_phone'=>isset($rowData->user_phone) && trim($rowData->user_phone)!=null?$rowData->user_phone:"",
                            'user_lat'=>isset($rowData->user_lat) && trim($rowData->user_lat)!=null?$rowData->user_lat:"",
                            'user_long'=>isset($rowData->user_long) && trim($rowData->user_long)!=null?$rowData->user_long:"",
                            'user_image'=>isset($rowData->user_image) && trim($rowData->user_image)!=null?base_url().'uploads/profile_images/'.$rowData->user_image:"",
                            'device_token'=>isset($rowData->device_token) && trim($rowData->device_token)!=null?$rowData->device_token:"",
                            'user_status'=>$rowData->user_status,
                            'user_created_on'=>$rowData->user_created_on,
                            'user_updated_on'=>$rowData->user_updated_on
                            );
                        }

                    echo json_encode(array("statusCode"=>200,"message"=>"Profile updated successfully."));
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                } 
                
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function changePassword()
	{
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $change_password = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                if(!empty($change_password))
                { 
                    $this->form_validation->set_data($change_password);
                    $this->form_validation->set_rules('old_password','old_password','trim|required');
                    $this->form_validation->set_rules('new_password','new_password','trim|required');
                   if($this->form_validation->run()==FALSE)
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                    }
                    else
                    {
                        $check_old_password = $this->driver_model->old_password_authenticate($token,$change_password['old_password'] );
                        if($check_old_password == false)
                        {
                            echo json_encode(array("statusCode"=>400,"message"=>"old password does not matched."));
                        }
                        else
                        {
                            if($get_authenticate->user_password!=md5($change_password['new_password']))
                            {
                                $changePassword['user_password']= md5($change_password['new_password']);
                                $this->driver_model->user_update($changePassword,$get_authenticate->user_id,'user_master');
                                echo json_encode(array("statusCode"=>200,"message"=>"new Password update successfully."));
                            }
                            else
                            {
                                echo json_encode(array("statusCode"=>400,"message"=>"New Password should not match with old password"));
                            }
                        }
                    }  
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                }  
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function getProfile()
    {
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            //$profileData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                        $response=array();
                        $rowData=$this->driver_model->getDriver($get_authenticate->user_id);
                        if(!empty($rowData))
                        {
                            $response=array(
                            'user_id'=>$rowData->user_id,
                            'user_first_name'=>isset($rowData->user_first_name) && trim($rowData->user_first_name)!=null?$rowData->user_first_name:"",
                            'user_last_name'=>isset($rowData->user_last_name) && trim($rowData->user_last_name)!=null?$rowData->user_last_name:"",
                            'user_phone'=>isset($rowData->user_phone) && trim($rowData->user_phone)!=null?$rowData->user_phone:"",
                            'user_lat'=>isset($rowData->user_lat) && trim($rowData->user_lat)!=null?$rowData->user_lat:"",
                            'user_long'=>isset($rowData->user_long) && trim($rowData->user_long)!=null?$rowData->user_long:"",
                            'user_image'=>isset($rowData->user_image) && trim($rowData->user_image)!=null?base_url().'uploads/profile_images/'.$rowData->user_image:"",
                            'device_token'=>isset($rowData->device_token) && trim($rowData->device_token)!=null?$rowData->device_token:"",
                            'user_status'=>$rowData->user_status,
                            'user_created_on'=>$rowData->user_created_on,
                            'user_updated_on'=>$rowData->user_updated_on
                            );
                        }               
                       echo json_encode(array("statusCode"=>200,"data"=>$response,"message"=>"Profile listed successfully")); 
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

   public function AcceptBooking()
	{
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                if(!empty($status_Data))
                { 
                    $this->form_validation->set_data($status_Data);
                    // $this->form_validation->set_rules('booking_status','booking_status','trim|required');
                    $this->form_validation->set_rules('booking_id','booking_id','trim|required');
                    $this->form_validation->set_rules('driver_lat','driver_lat','trim|required');
                    $this->form_validation->set_rules('driver_long','driver_long','trim|required');

                    if($this->form_validation->run()==FALSE)
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                    }
                    else
                    {
                        $bookingData=$this->user_model->booked_id($status_Data['booking_id']);
                        if(!empty($bookingData))
                        {
                            if($bookingData->booking_status=='PN')
                            {
                                $updateData['user_lat']=$status_Data['driver_lat'];
                                $updateData['user_long']=$status_Data['driver_long'];
                                $this->driver_model->user_update($updateData,$get_authenticate->user_id,'user_master');
                                $updateStatus['booking_status']="AC"; 
                                $updateStatus['booked_to']=$get_authenticate->user_id; 
                                $this->driver_model->status_update($status_Data['booking_id'],$token,$updateStatus);
                                //send notification to user
                                $rowData=$this->user_model->check_data_by_user_id($bookingData->booked_by);
                                if(!empty($rowData))
                                {
                                    if(isset($rowData->device_token) && trim($rowData->device_token)!=null)
                                    {
                                    
                                        $data['sender_id'] 		= $get_authenticate->user_id;
                                        $data['location_lat'] 	= $bookingData->location_lat;
                                        $data['location_long'] 	= $bookingData->location_long;
                                        $data['receiver_id'] 	= $bookingData->booked_by;
                                        $data['driver_lat'] 	= $status_Data['driver_lat'];
                                        $data['driver_long'] 	= $status_Data['driver_long'];
                                        $data['booking_id'] 	= $status_Data['booking_id'];
                                        $data['created_on'] 	= date('Y-m-d');
                                        $data['updated_on'] 	= date('Y-m-d');
                                        $msg=$get_authenticate->user_first_name."accepted your order.";
                                        $notify_type="acceptBooking";

                                        $clickAction=".MainActivity";
                                        $resultArray=$this->push_notify($rowData->device_token,$data,$msg,$notify_type);
                                        //  var_dump($resultArray);
                                    }
                                }
                                

                                echo json_encode(array("statusCode"=>200,"message"=>"Booking accepted Successfully"));
                            }
                            else if($bookingData->booking_status=='CL')
                            {
                                echo json_encode(array("statusCode"=>400,"message"=>"Order is cancelled bu user."));
                            }
                            else
                            {
                                echo json_encode(array("statusCode"=>400,"message"=>"Order is already accepted."));
                            }
                        }
                        else
                        {
                            echo json_encode(array("statusCode"=>400,"message"=>"No booking found for this id"));
                        }
                        
                    } 
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                }  
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function completeBooking()
	{
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                if(!empty($status_Data))
                { 
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('booking_id','booking_id','trim|required');

                    if($this->form_validation->run()==FALSE)
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                    }
                    else
                    {
                        $bookingData=$this->user_model->booked_id($status_Data['booking_id']);
                        if(!empty($bookingData))
                        {
                            if($bookingData->booking_status=='AC')
                            {
                                
                                $updateStatus['booking_status']="CP"; 
                                $this->driver_model->status_update($status_Data['booking_id'],$token,$updateStatus);

                                //add amount for eavery single car wash to driver wallet
                                $perCarWashAmount['driver_wallet']= $get_authenticate->driver_wallet+perCarCash;
                                $this->driver_model->user_update($perCarWashAmount,$get_authenticate->user_id,'user_master');

                                //single wallet history
                                $historyData['is_added']=1;
                                $historyData['history_text']="Reward for single car wash";
                                $historyData['amount']=perCarCash;
                                $historyData['booking_id']=$status_Data['booking_id'];
                                $historyData['driver_id']=$get_authenticate->user_id;
                                $historyData['history_on']=$this->getDate();
                                $this->driver_model->insertWalletHistory($historyData);

                                //check for 10 complete car wash 
                                $amountAvail=$this->driver_model->driverBookingAmountAvail($get_authenticate->user_id);
                                if($amountAvail==10)
                                {
                                     //add amount to driver wallet after completing 10 car wash
                                    $tenCarWashAmount['driver_wallet']=perTenCarCash+$perCarWashAmount['driver_wallet'];
                                    $this->driver_model->user_update($tenCarWashAmount,$get_authenticate->user_id,'user_master');

                                    //after completing 10 wallet history
                                    $historyData['is_added']=1;
                                    $historyData['history_text']="Reward for completing 10 car wash";
                                    $historyData['amount']=perTenCarCash;
                                    $historyData['booking_id']=$status_Data['booking_id'];
                                    $historyData['driver_id']=$get_authenticate->user_id;
                                    $historyData['history_on']=$this->getDate();
                                    $this->driver_model->insertWalletHistory($historyData);
                                    
                                    //update is_avail to 1 after 10 complete booking
                                    $availdata['is_amount_avail']=1;
                                    $this->driver_model->is_avail_update($get_authenticate->user_id,$availdata);

                                }
                                
                                //notification to user
                                $rowData=$this->user_model->check_data_by_user_id($bookingData->booked_by);
                                if(!empty($rowData))
                                {
                                    if(isset($rowData->device_token) && trim($rowData->device_token)!=null)
                                    {
                                    
                                        $data['sender_id'] 		= $get_authenticate->user_id;
                                        $data['receiver_id'] 	= $bookingData->booked_by;
                                        $data['booking_id'] 	= $status_Data['booking_id'];
                                        $data['created_on'] 	= date('Y-m-d');
                                        $data['updated_on'] 	= date('Y-m-d');
                                        $msg=$get_authenticate->user_first_name."mark as complete your order.";
                                        $notify_type="completeBooking";

                                        $clickAction=".MainActivity";
                                        $resultArray=$this->push_notify($rowData->device_token,$data,$msg,$notify_type);
                                        //  var_dump($resultArray);
                                    }
                                }



                                echo json_encode(array("statusCode"=>200,"message"=>"Booking completed Successfully"));
                            }
                            else
                            {
                                echo json_encode(array("statusCode"=>400,"message"=>"Only accepted order can be complete."));
                            }
                        }
                        else
                        {
                            echo json_encode(array("statusCode"=>400,"message"=>"No booking found for this id"));
                        }
                        
                    } 
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                }  
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function getOrderById()
    {
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $profileData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                 if(!empty($profileData))
                 { 
                    $this->form_validation->set_data($profileData);
                     $this->form_validation->set_rules('booking_id','booking_id','trim|required');
                    if($this->form_validation->run()==FALSE)
                     {
                         echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                     }
                    else
                     {
                        $responseData=array();
                        $rowData=$this->user_model->fetchOrderById($profileData['booking_id']);
                        if(!empty($rowData))
                        {

                            foreach($rowData as $BookingKey => $BookingValue)
                            {
                                if(isset($BookingValue['booked_by']) && trim($BookingValue['booked_by'])!=null)
                                {
                                    $bookedToData=$this->user_model->check_data_by_user_id($BookingValue['booked_by']);
                                    if(!empty($bookedToData))
                                    {
                                        $responseData[$BookingKey]['user_first_name']=isset($bookedToData->user_first_name) && trim($bookedToData->user_first_name)!=null?$bookedToData->user_first_name:"";
                                        $responseData[$BookingKey]['user_last_name']=isset($bookedToData->user_last_name) && trim($bookedToData->user_last_name)!=null?$bookedToData->user_last_name:"";
                                        $responseData[$BookingKey]['user_phone']=isset($bookedToData->user_phone) && trim($bookedToData->user_phone)!=null?$bookedToData->user_phone:"";
                                        $responseData[$BookingKey]['user_image']=isset($bookedToData->user_image) && trim($bookedToData->user_image)!=null?base_url().'uploads/profile_images/'.$bookedToData->user_image:"";
                                    }

                                }
                                $responseData[$BookingKey]['booking_id']=$BookingValue['booking_id'];
                                $responseData[$BookingKey]['booked_by']=$BookingValue['booked_by'];
                                $responseData[$BookingKey]['booked_to']=isset($BookingValue['booked_to']) && trim($BookingValue['booked_to'])!=null?$BookingValue['booked_to']:"";
                                $responseData[$BookingKey]['plan_id']=$BookingValue['plan_id'];
                                $responseData[$BookingKey]['location']=$BookingValue['location'];
                                $responseData[$BookingKey]['location_lat']=$BookingValue['location_lat'];
                                $responseData[$BookingKey]['location_long']=$BookingValue['location_long'];
                                $responseData[$BookingKey]['payment_method']=$BookingValue['payment_method'];
                                $responseData[$BookingKey]['total_amount']=$BookingValue['total_amount'];
                                $responseData[$BookingKey]['booked_on']=$BookingValue['booked_on'];
                                $responseData[$BookingKey]['booking_status']=$BookingValue['booking_status'];
                               
                            }
                        }
                       

                       echo json_encode(array("statusCode"=>200,"data"=>$responseData,"message"=>"Order listed successfully")); 
                    }  
                }
                 else
                 {
                     echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                 }  
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function getWalletData()
    {
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            //$profileData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                        $response=array();
                        $rowData=$this->driver_model->getDriver($get_authenticate->user_id);
                        if(!empty($rowData))
                        {
                            $response=array(
                            'driver_wallet'=>$rowData->driver_wallet,
                            'point_counter'=>$this->driver_model->driverBookingAmountAvail($get_authenticate->user_id)
                            );
                        }               
                       echo json_encode(array("statusCode"=>200,"data"=>$response,"message"=>"Wallet listed successfully")); 
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }

    public function getDriverOrder()
    {
         $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $orderData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if(!empty($get_authenticate))
            {
                        $responseData=array();
                        $rowData=$this->driver_model->getOrders($token);
                          $todayOrderCount=$this->driver_model->getTodayOrderCount($token);
                          $yesterdayOrderCount=$this->driver_model->getYestedayOrderCount($token);
                          $thisWeekOrderCount=$this->driver_model->getthisWeekOrderCount($token);
                          $lastWeekOrderCount=$this->driver_model->getLastWeekOrderCount($token);
                          $currentMonthOrderCount=$this->driver_model->getCurrentMonthOrderCount($token);
                          $lastMonthOrderCount=$this->driver_model->getLastMonthOrderCount($token);


                        // var_dump($todayOrderCount);
                        // die;
                        if(!empty($rowData))
                        {
                            foreach($rowData as $orderKey => $orderValue)
                            {
                                $bookedToData=$this->user_model->check_data_by_user_id($orderValue['booked_to']);
                                if(!empty($bookedToData))
                                {
                                    $responseData[$orderKey]['user_first_name']=isset($bookedToData->user_first_name) && trim($bookedToData->user_first_name)!=null?$bookedToData->user_first_name:"";
                                    $responseData[$orderKey]['user_last_name']=isset($bookedToData->user_last_name) && trim($bookedToData->user_last_name)!=null?$bookedToData->user_last_name:"";
                                    $responseData[$orderKey]['user_phone']=isset($bookedToData->user_phone) && trim($bookedToData->user_phone)!=null?$bookedToData->user_phone:"";
                                    $responseData[$orderKey]['user_lat']=isset($bookedToData->user_lat) && trim($bookedToData->user_lat)!=null?$bookedToData->user_lat:"";
                                    $responseData[$orderKey]['user_long']=isset($bookedToData->user_long) && trim($bookedToData->user_long)!=null?$bookedToData->user_long:"";
                                }

                                
                                $responseData[$orderKey]['plan_name']=$orderValue['plan_name'];
                                $responseData[$orderKey]['make']=$orderValue['make'];
                                $responseData[$orderKey]['model']=$orderValue['model'];
                                $responseData[$orderKey]['booking_id']=$orderValue['booking_id'];
                                $responseData[$orderKey]['total_amount']=$orderValue['total_amount'];
                                $responseData[$orderKey]['plan_id']=$orderValue['plan_id'];
                                $responseData[$orderKey]['location']=isset($orderValue['location']) && trim($orderValue['location'])!=null?$orderValue['location']:'';
                                $responseData[$orderKey]['location_lat']=isset($orderValue['location_lat']) && trim($orderValue['location_lat'])!=null?$orderValue['location_lat']:'';
                                $responseData[$orderKey]['location_long']=isset($orderValue['location_long']) && trim($orderValue['location_long'])!=null?$orderValue['location_long']:'';
                                $responseData[$orderKey]['booking_status']=$orderValue['booking_status'];
                                $responseData[$orderKey]['booked_on']=$orderValue['booked_on'];
                                $responseData[$orderKey]['booking_date_time']=isset($orderValue['booking_date_time']) && trim($orderValue['booking_date_time'])!=null?$orderValue['booking_date_time']:"";
                            }

                        }
                       echo json_encode(array("statusCode"=>200,"todayOrderCount"=>$todayOrderCount,"yesterdayOrderCount"=>$yesterdayOrderCount,"thisWeekOrderCount"=>$thisWeekOrderCount,"lastWeekOrderCount"=>$lastWeekOrderCount,"currentMonthOrderCount"=>$currentMonthOrderCount,"lastMonthOrderCount"=>$lastMonthOrderCount,"message"=>"Past order listed successfully.","data"=>$responseData)); 
            }    
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            } 
        }   
        else
        {
        echo json_encode(array("statusCode"=>400,"message"=>"invalid token"));
        }
    }

    public function getTrasactionHistory()
    {
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $orderData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if(!empty($get_authenticate))
            {
                $responseData=array();
                $rowData=$this->driver_model->getWalletHistory($token);
                    
                if(!empty($rowData))
                {
                    foreach($rowData as $orderKey => $orderValue)
                    {
                        $responseData[$orderKey]['is_added']=$orderValue['is_added'];
                        $responseData[$orderKey]['history_text']=$orderValue['history_text'];
                        $responseData[$orderKey]['amount']=$orderValue['amount'];
                        $responseData[$orderKey]['booking_id']=$orderValue['booking_id'];
                        $responseData[$orderKey]['history_on']=$orderValue['history_on'];
                    }

                }
                echo json_encode(array("statusCode"=>200,"message"=>"Wallet history listed sucessfully.","data"=>$responseData)); 
            }    
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            } 
        }   
        else
        {
        echo json_encode(array("statusCode"=>400,"message"=>"invalid token"));
        }
    }
    public function phoneExist()
    {
        $status_Data = json_decode(trim(file_get_contents('php://input')), true);
        if(!empty($status_Data))
        { 
            $this->form_validation->set_data($status_Data);
            $this->form_validation->set_rules('user_phone','user_phone','trim|required');
            if($this->form_validation->run()==FALSE)
            {
                echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
            }
            else
            {
                $userData=$this->driver_model->checkPhone($status_Data['user_phone']);
                // var_dump($userData);
                // die;
                if(!empty($userData))
                {
                    $token= jwt::encode($userData->user_id,jwtKey);
                    echo json_encode(array("statusCode"=>200,"message"=>"Phone Number Exist","token"=>$token));
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Phone number not exist."));
                }
                
            } 
        }
        else
        {
            echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
        }  
    }

    public function updatePassword()
	{
        $token=$this->chckToken();
        if(trim($token)!=null)
        {
            $change_password = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if(!empty($get_authenticate))
            {
                if(!empty($change_password))
                { 
                    $this->form_validation->set_data($change_password);
                    $this->form_validation->set_rules('new_password','new_password','trim|required');
                   if($this->form_validation->run()==FALSE)
                    {
                        echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                    }
                    else
                    {
                        if($get_authenticate->user_password!=md5($change_password['new_password']))
                        {
                            $changePassword['user_password']	= md5($change_password['new_password']);
                            $this->user_model->update_user_by_id($get_authenticate->user_id,$changePassword);
                            echo json_encode(array("statusCode"=>200,"message"=>"new Password update successfully."));
                        }
                        else
                        {
                            echo json_encode(array("statusCode"=>400,"message"=>"New Password should not match with old password"));
                        }
                    }  
                }
                else
                {
                    echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                }  
            }
            else
            {
                echo json_encode(array("statusCode"=>400,"message"=>"Not Authenticate."));
            }
        }   
        else 
        {    
            echo json_encode(array("statusCode"=>400,"message"=>"Invalid Token."));
        }
    }
    public function push_notify($token, $load,$msg,$notify_type)
	{

        $url = 'https://fcm.googleapis.com/fcm/send';
		$key = 'AAAACkht3HE:APA91bEDvQgRKOs5V2TWMvpHVSAPqg_uRXVqhcBIviwqFhKOqg62mPd2OX51G5OUbxRV-rWzaYXtEN0NLEd0dQDOLSalerypyUoP5dT_ovDWDIMY0gMTQcy4tja8AZQLkw-M4x5Sydfp';

		$data = array(
			'notify_type' => $notify_type,
			"dataarray"=>$load,
		);
		$notification=array(
			"click_action" => ".DashboardActivity", 
			'title' => 'Order Green',
			'body' =>  $msg ,
			"dataarray"=>$load,
			'sound'=>'Default',
			'badge'=>1,
			'image'=>'Notification Image'
		);
            $fields = array(
            'to' => $token,
            'notification' => $notification,
			'data'=> $data
		);

		// print_r($fields);

        $headers = array(
            'Authorization: key=' . $key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, true));
        $result = curl_exec($ch);
        //echo $result; die;
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
       // echo $token;
    //    print_r($result);
    //    echo  json_encode(array("response"=>$result));
        return $result;
    }
}

?>