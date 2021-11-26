<?php
class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');

    }

    public function getDate()
    {
        return time();
    }

    public function getHeaders()
    {
        $headers = apache_request_headers();
        return $headers;
    }
    protected function user_authenticate($token)
    {
        if (!empty($token)) {
            return $this->user_model->get_authenticate($token);
        }
    }
    public function generateNumber()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < 8; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
    protected function chckToken()
    {
        $headerData = $this->getHeaders();
        $decodetoken = "";
        if (isset($headerData['Usertoken']) && trim($headerData['Usertoken']) != null) {
            $decodetoken = jwt::decode($headerData['Usertoken'], jwtKey);
            return $decodetoken;
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Required userToken not sent."));
        }
    }
    public function sendSms($sms_message, $to)
    {

        $this->load->library('twilio');
        $from = '+12058807108'; //trial account twilio number
        $response = $this->twilio->sms($from, $to, $sms_message);
        if ($response->IsError) {
            return $response->IsError;
        } else {
            return 'OTP SMS Has been sent';
        }
    }
    public function userRegiser()
    {
        $getData = json_decode(trim(file_get_contents("php://input")), true);
        if (!empty($getData)) {
            $this->form_validation->set_data($getData);
            if (isset($getData['provider_type'])) {
                $this->form_validation->set_rules('provider_type', 'provider_type', 'trim|required');
                $this->form_validation->set_rules('provider_key', 'provider_key', 'trim|required|is_unique[user_master.provider_key]', 'Enteds dsd');
                $getData['user_password'] = 'dummy123456';
            } else {
                $this->form_validation->set_rules('user_last_name', 'user_last_name', 'trim|required');
                $this->form_validation->set_rules('user_password', 'user_password', 'trim|required');
                $this->form_validation->set_rules('user_email', 'Email', 'trim|required|is_unique[user_master.user_email]');
                $this->form_validation->set_rules('user_phone', 'Mobile no', 'trim|required|is_unique[user_master.user_phone]');
            }
            $this->form_validation->set_rules('user_first_name', 'user_first_name', 'trim|required');
            $this->form_validation->set_rules('device_token', 'device_token', 'trim|required');
            $this->form_validation->set_message('is_unique', 'Your account is already registered please login your account.');
            if ($this->form_validation->run() == false) {
                echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
            } else {
                $response = array();
                $data = array();
                if (isset($getData['invite_code']) && trim($getData['invite_code']) != null) {
                    $inviteCodeCount = $this->user_model->checkInvitedCode($getData['invite_code']);
                    if (!empty($inviteCodeCount) && isset($inviteCodeCount->user_id) && trim($inviteCodeCount->user_id) > 0) {
                        $invited_by = $inviteCodeCount->user_id;
                        $invitefriendsCount = $this->user_model->countInvitedCode($getData['invite_code']);
                        if ($invitefriendsCount == 1) {
                            $data['is_avail'] = 1;
                            //set invitees avail to 1
                            $this->user_model->updateUsersAvail($getData['invite_code']);
                            //add free body wash to invited by account
                            $userData['free_body_wash'] = $inviteCodeCount->free_body_wash + 1;
                            $this->user_model->update_user_by_id($inviteCodeCount->user_id, $userData);
                        }
                        //add free body wash to invitees by account
                        $data['invited_by_code'] = $getData['invite_code'];
                        $data['free_body_wash'] = 1;
                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "Invalid Invitation code."));
                        exit;
                    }

                }
                $data['user_first_name'] = htmlspecialchars($getData['user_first_name']);
                $data['user_last_name'] = htmlspecialchars($getData['user_last_name'] ?? "");
                $data['user_email'] = htmlspecialchars($getData['user_email']);
                $data['user_password'] = md5($getData['user_password']);
                $data['device_token'] = htmlspecialchars($getData['device_token']);
                if (isset($getData['provider_type'])) {
                    $data['provider_type'] = htmlspecialchars($getData['provider_type']);
                    $data['provider_key'] = htmlspecialchars($getData['provider_key']);
                }

                if (isset($getData['user_phone'])) {
                    $data['user_phone'] = htmlspecialchars($getData['user_phone']);
                    //$data['verification_code'] = '123456';
                    $data['verification_code'] = mt_rand(1000, 9999);
                    //$sms_message = "Welcome to Greenly, Your OTP Code is: " . $data['verification_code'] . ", Please Do not share it with anyone, Thank You, Greenly Team.";
                    $sms_message = "Welcome to Greenly, Your OTP Code is: " . $data['verification_code'] . ", Please Do not share it with anyone, Thank You, Greenly Team.";
                    $otpRespose = $this->sendSms($sms_message, $data['user_phone']);                    
                }
                $data['is_verified'] = 0;   
                if(isset($getData['platform']) && $getData['platform'] == 'ios'){
                    $data['is_verified'] = 1;
                }
                $data['invitation_code'] = $getData['user_first_name'] . $getData['user_last_name'] . mt_rand(1000, 9999);
                $data['user_type'] = 1;
                $data['user_language'] = $getData['user_language'] ?? 1;
                $data['user_created_on'] = $this->getDate();
                $data['user_updated_on'] = $this->getDate();
                $varify_data = $this->user_model->insert_user_data($data);
                $row_data = $this->user_model->check_data_by_user_id($varify_data);
                $token = jwt::encode($varify_data, jwtKey);
                $response = array(
                    'user_id' => $row_data->user_id,
                    'invitation_code' => isset($row_data->invitation_code) && trim($row_data->invitation_code) != null ? $row_data->invitation_code : "",
                    'user_first_name' => isset($row_data->user_first_name) && trim($row_data->user_first_name) != null ? $row_data->user_first_name : "",
                    'user_last_name' => isset($row_data->user_last_name) && trim($row_data->user_last_name) != null ? $row_data->user_last_name : "",
                    'user_phone' => $row_data->user_phone,
                    'user_email' => $row_data->user_email,
                    'user_image' => isset($row_data->user_image) && trim($row_data->user_image) != null ? base_url() . 'uploads/profile_images/' . $row_data->user_image : "",
                    'usertoken' => $token,
                    'verification_code' => $data['verification_code'] ?? "",
                    'is_verified' => $row_data->is_verified,
                    'otp_respose' => $otpRespose ?? "",
                    'user_type' => $row_data->user_type,
                    'user_language' => $row_data->user_language,
                );
                $msg = 'Thank you for Registration Order Green';

                if (isset($getData['user_phone'])) {
                    $msg = 'Thank you for Registration Order Green - OTP';
                    $content['userName'] = $data['user_first_name'];
                    $content['content'] = $sms_message;
                    $this->user_model->emailSending($data['user_email'], $msg, $content);
                }

                echo json_encode(array("statusCode" => 200, "message" => $msg, "data" => $response));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
        }
    }
    public function providerRegiser()
    {
        $getData = json_decode(trim(file_get_contents("php://input")), true);

        if (!empty($getData)) {
            $this->form_validation->set_data($getData);

            $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
            $this->form_validation->set_rules('provider_name', 'Provider Name', 'trim|required');
            $this->form_validation->set_rules('provider_password', 'Provider Password', 'trim|required');
            $this->form_validation->set_rules('provider_address', 'Provider Address', 'trim|required');
            $this->form_validation->set_rules('provider_email', 'Email', 'trim|required|is_unique[user_master.user_email]');
            $this->form_validation->set_rules('provider_phone', 'Mobile no', 'trim|required|is_unique[user_master.user_phone]');
            $this->form_validation->set_rules('device_token', 'Device Token', 'trim|required');
            $this->form_validation->set_rules('service_type[]', 'Service Type', 'trim|required');
            $this->form_validation->set_message('is_unique', 'Your account is already registered please login your account.');
            if ($this->form_validation->run() == false) {
                echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
            } else {
                $response = array();
                $data = array();

                $data['user_first_name'] = htmlspecialchars($getData['provider_name']);
                $data['user_email'] = htmlspecialchars($getData['provider_email']);
                $data['user_password'] = md5($getData['provider_password']);
                $data['device_token'] = htmlspecialchars($getData['device_token']);
                $data['service_type'] = json_encode($getData['service_type']);
                if (isset($getData['provider_phone'])) {
                    $data['user_phone'] = htmlspecialchars($getData['provider_phone']);
                    $data['verification_code'] = mt_rand(1000, 9999);
                    $sms_message = "Welcome to Greenly, Your OTP Code is: " . $data['verification_code'] . ", Please Do not share it with anyone, Thank You, Greenly Team.";
                    $otpRespose = $this->sendSms($sms_message, $data['user_phone']);
                }
                $data['is_verified'] = 0;
                $data['invitation_code'] = $getData['provider_name'] . $getData['company_name'] . mt_rand(1000, 9999);
                $data['user_type'] = 2;
                $data['user_status'] = 0;
                $data['user_created_on'] = $this->getDate();
                $data['user_updated_on'] = $this->getDate();
                $data['garage_name'] = $getData['company_name'];
                $data['make_id'] = json_encode($getData['make_id']);
                $data['garage_location'] = $getData['provider_address'];
                $data['user_lat'] = $getData['provider_lat'];
                $data['user_long'] = $getData['provider_long'];
                $data['user_language'] = $getData['user_language'] ?? 1;
                $varify_data = $this->user_model->insert_user_data($data);
                $row_data = $this->user_model->check_data_by_user_id($varify_data);
                $token = jwt::encode($varify_data, jwtKey);
                $response = array(
                    'user_id' => $row_data->user_id,
                    'invitation_code' => isset($row_data->invitation_code) && trim($row_data->invitation_code) != null ? $row_data->invitation_code : "",
                    'user_first_name' => isset($row_data->user_first_name) && trim($row_data->user_first_name) != null ? $row_data->user_first_name : "",
                    'user_last_name' => isset($row_data->user_last_name) && trim($row_data->user_last_name) != null ? $row_data->user_last_name : "",
                    'user_phone' => $row_data->user_phone,
                    'user_email' => $row_data->user_email,
                    'user_image' => isset($row_data->user_image) && trim($row_data->user_image) != null ? base_url() . 'uploads/profile_images/' . $row_data->user_image : "",
                    'usertoken' => $token,
                    'verification_code' => $data['verification_code'] ?? "",
                    'is_verified' => $row_data->is_verified,
                    'otp_respose' => $otpRespose ?? "",
                    'user_status' => $row_data->user_status,
                    'user_type' => $row_data->user_type,
                    'user_language' => $row_data->user_language,
                );
                $msg = 'Thank you for Registration Order Green - OTP';
                $content['userName'] = $data['user_first_name'];
                $content['content'] = $sms_message;
                $this->user_model->emailSending($data['user_email'], $msg, $content);

                echo json_encode(array("statusCode" => 200, "message" => "Registered Successfully", "data" => $response));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
        }
    }
    public function registerMobileNo()
    {
        $token = $this->chckToken();
        $getData = json_decode(trim(file_get_contents("php://input")), true);
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);
            //  var_dump($get_authenticate);
            $data = [];
            if (!empty($get_authenticate)) {
                $this->form_validation->set_data($getData);
                $this->form_validation->set_rules('user_phone', 'Mobile no', 'trim|required|is_unique[user_master.user_phone]');
                $this->form_validation->set_message('is_unique', 'Your phone number is already registered please login to your account.');
                if ($this->form_validation->run() == false) {
                    echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                } else {

                    $data['user_phone'] = htmlspecialchars($getData['user_phone']);
                    $data['verification_code'] = mt_rand(1000, 9999);
                    $sms_message = "\n" . $data['verification_code'] . "\n Use this code for OrderGreen verification";
                    $otpRespose = $this->sendSms($sms_message, $data['user_phone']);
                    $varify_data = $this->user_model->update_user_by_id($get_authenticate->user_id, $data);
                    $response = array(
                        'user_id' => $get_authenticate->user_id,
                        'invitation_code' => isset($get_authenticate->invitation_code) && trim($get_authenticate->invitation_code) != null ? $get_authenticate->invitation_code : "",
                        'user_first_name' => isset($get_authenticate->user_first_name) && trim($get_authenticate->user_first_name) != null ? $get_authenticate->user_first_name : "",
                        'user_last_name' => isset($get_authenticate->user_last_name) && trim($get_authenticate->user_last_name) != null ? $get_authenticate->user_last_name : "",
                        'user_phone' => $get_authenticate->user_phone,
                        'user_email' => $get_authenticate->user_email,
                        'user_image' => isset($get_authenticate->user_image) && trim($get_authenticate->user_image) != null ? base_url() . 'uploads/profile_images/' . $get_authenticate->user_image : "",
                        'verification_code' => $data['verification_code'] ?? "",
                        'is_verified' => $get_authenticate->is_verified,
                        'otp_respose' => $otpRespose ?? "",
                        'user_type' => $get_authenticate->user_type,

                    );

                    echo json_encode(array("statusCode" => 200, "message" => "Mobile Register Successfully", "data" => $response));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function userVerify()
    {
        $getData = json_decode(trim(file_get_contents("php://input")), true);
        if (isset($getData['verification_id']) && trim($getData['verification_id']) != null) {
            if (isset($getData['verification_code']) && trim($getData['verification_code']) != null) {
                $user_id = htmlspecialchars($getData['verification_id']);
                $verification_code = htmlspecialchars($getData['verification_code']);
                $varificationData = $this->user_model->user_verify($user_id, $verification_code);
                if (!empty($varificationData)) {
                    $data['is_verified'] = 1;
                    $message = 'Your account has been verified successfully.';
                    if ($varificationData->user_type == 2) {
                        // $garagedata['garage_status'] = 1;
                        // $this->user_model->update_garage_by_id($varificationData->user_id, $garagedata);
                        $message = 'Thank you for registraion, sent for the approval to admin! and sent Email.';
                    }
                    $this->user_model->update_user_by_id($varificationData->user_id, $data);

                    $response = array();
                    $response['user_id'] = $varificationData->user_id;
                    $response['user_first_name'] = $varificationData->user_first_name;
                    $response['user_last_name'] = $varificationData->user_last_name;
                    $response['user_email'] = $varificationData->user_email;
                    $response['user_phone'] = $varificationData->user_phone;
                    $token = jwt::encode($varificationData->user_id, jwtKey);
                    $response['user_token'] = $token;
                    $response['is_verified'] = $data['is_verified'];

                    $subject = "Order Green - Thank you for Registration!";
                    $content['userName'] = $varificationData->user_first_name;
                    $content['content'] = $message;
                    $this->user_model->emailSending($varificationData->user_email, $subject, $content);
                    $date = date("Y-m-d H:i:s");
                    $notify_type = 'normal';
                    $type = "Registration";
                    $notificationData = array(
                        ['user_id' => $varificationData->user_id,
                            'type' => $notify_type,
                            'title' => 'Registration',
                            'description' => $message,
                            'status' => 1,
                            'created_at' => $date,
                            'updated_at' => $date],
                    );
                    $this->user_model->insertNotification($notificationData);
                    $this->user_model->push_notify($varificationData->device_token, $message, $type, $notify_type, $varificationData->user_type);
                    echo json_encode(array("statusCode" => 200, "message" => $message, "data" => $response));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Invalid Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "varification_code Required"));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "varification_id Required"));
        }
    }
    public function resend_otp()
    {
        $getData = json_decode(trim(file_get_contents("php://input")), true);
        if (isset($getData['verification_id']) && trim($getData['verification_id']) != null) {
            $chckData = $this->user_model->check_data_by_user_id($getData['verification_id']);
            //var_dump($chckData);
            if (!empty($chckData)) {
                $data = array();
                $data['verification_code'] = mt_rand(1000, 9999);
                $sms_message = "\n" . $data['verification_code'] . "\nUse this code for OrderGreen verification";
                $this->sendSms($sms_message, $data['user_phone']);
                $this->user_model->update_user_by_id($chckData->user_id, $data);
                echo json_encode(array("statusCode" => 200, "message" => "New otp send to your registered mobile number", "data" => $data));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "varification_id Is Not Valid"));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
        }
    }
    public function userLogin()
    {
        $getData = json_decode(trim(file_get_contents("php://input")), true);
        if (!empty($getData)) {
            $this->form_validation->set_data($getData);
            if (isset($getData['provider_key'])) {
                $this->form_validation->set_rules('provider_key', 'provider_key', 'trim|required');
                $getData['user_password'] = 'dummy123456';
                $loginType = 1;
            } else {
                $this->form_validation->set_rules('user_phone', 'user_phone', 'trim|required');
                $this->form_validation->set_rules('user_password', 'user_password', 'trim|required');
                $loginType = 0;
            }
            $this->form_validation->set_rules('device_token', 'device_token', 'trim|required');
            if ($this->form_validation->run() == false) {
                echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
            } else {
                if (isset($getData['provider_key'])) {
                    $user_phone = $getData['provider_key'];
                } else {
                    $user_phone = $getData['user_phone'];
                }
                $userPassword = md5($getData['user_password']);
                $row_data = $this->user_model->userLogin($user_phone, $userPassword, $loginType);

                if (!empty($row_data)) {

                    if ($row_data->user_status == "" || $row_data->user_status == 0 && $row_data->user_type == 2) {
                        echo json_encode(array("statusCode" => 400, "message" => "Wating for admin Approval"));
                        exit;
                    }
                    $vehicle_status = $this->user_model->check_vehicle_status($row_data->user_id);
                    $token = jwt::encode($row_data->user_id, jwtKey);
                    $tokenData['device_token'] = $getData['device_token'];
                    $this->user_model->update_user_by_id($row_data->user_id, $tokenData);
                    $response = array(
                        'user_id' => $row_data->user_id,
                        'invitation_code' => $row_data->invitation_code,
                        'user_first_name' => $row_data->user_first_name,
                        'user_last_name' => $row_data->user_last_name,
                        'user_phone' => $row_data->user_phone,
                        'user_email' => $row_data->user_email,
                        'vehicle_status' => $vehicle_status,
                        'is_verified' => $row_data->is_verified,
                        'user_image' => isset($row_data->user_image) && trim($row_data->user_image) != null ? base_url() . 'uploads/profile_images/' . $row_data->user_image : "",
                        'usertoken' => $token,
                        'user_type' => $row_data->user_type,

                    );
                    if ($row_data->is_verified != 1) {
                        echo json_encode(array("statusCode" => 400, "message" => "Kindly verify your mobile number", "data" => $response));
                        exit;
                    }
                    echo json_encode(array("statusCode" => 200, "message" => "Login successfully", "data" => $response));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "User does not exist, Please register your account."));
                }
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
        }
    }

    public function logout()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);
            //  var_dump($get_authenticate);
            if (!empty($get_authenticate)) {
                $update_data['device_token'] = "";
                $this->user_model->update_user_by_id($get_authenticate->user_id, $update_data);
                echo json_encode(array("statusCode" => "200", "message" => "User logout successfully"));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function updateProfile()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $profile_Data = $this->input->post();
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                if (!empty($profile_Data)) {
                    $updateData['user_first_name'] = $get_authenticate->user_first_name;
                    $updateData['user_last_name'] = $get_authenticate->user_last_name;

                    if (isset($profile_Data['user_first_name']) && trim($profile_Data['user_first_name']) != null) {
                        $updateData['user_first_name'] = $profile_Data['user_first_name'];
                    }
                    if (isset($profile_Data['user_last_name']) && trim($profile_Data['user_last_name']) != null) {
                        $updateData['user_last_name'] = $profile_Data['user_last_name'];
                    }

                    $updateData['user_image'] = $get_authenticate->user_image;
                    if (isset($_FILES['user_image']['name']) && !empty($_FILES['user_image']['name'])) {
                        $fileName = $_FILES['user_image']['name'];
                        $tmpName = $_FILES['user_image']['tmp_name'];
                        $uploadPath = 'uploads/profile_images/';
                        $imageName = $uploadPath . $fileName;
                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                        $actualFileName = time() . "." . $fileExtension;
                        $image_dir = 'uploads/profile_images/';
                        $imgConfig['upload_path'] = $image_dir;
                        $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
                        $imgConfig['file_name'] = $actualFileName;
                        $this->load->library('upload', $actualFileName);
                        $this->upload->initialize($imgConfig);
                        if ($this->upload->do_upload('user_image')) {
                            $profileImage = $this->upload->data();
                            $updateData['user_image'] = $profileImage['file_name'];
                        } else {
                            $error = array('error' => $this->upload->display_errors());
                        }
                    }

                    $this->user_model->update_user_by_id($get_authenticate->user_id, $updateData);
                    $response = array();
                    $rowData = $this->user_model->check_data_by_user_id($get_authenticate->user_id);
                    if (!empty($rowData)) {
                        $response = array(
                            'user_id' => $rowData->user_id,
                            'user_first_name' => isset($rowData->user_first_name) && trim($rowData->user_first_name) != null ? $rowData->user_first_name : "",
                            'user_last_name' => isset($rowData->user_last_name) && trim($rowData->user_last_name) != null ? $rowData->user_last_name : "",
                            'user_phone' => isset($rowData->user_phone) && trim($rowData->user_phone) != null ? $rowData->user_phone : "",
                            'user_image' => isset($rowData->user_image) && trim($rowData->user_image) != null ? base_url() . 'uploads/profile_images/' . $rowData->user_image : "",
                            'device_token' => isset($rowData->device_token) && trim($rowData->device_token) != null ? $rowData->device_token : "",
                            'user_status' => $rowData->user_status,
                            'user_created_on' => $rowData->user_created_on,
                            'user_updated_on' => $rowData->user_updated_on,
                        );
                    }

                    echo json_encode(array("statusCode" => 200, "message" => "Profile updated successfully.", "data" => $response));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function changePassword()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $change_password = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($change_password)) {
                    $this->form_validation->set_data($change_password);
                    $this->form_validation->set_rules('old_password', 'old_password', 'trim|required');
                    $this->form_validation->set_rules('new_password', 'new_password', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $check_old_password = $this->user_model->old_password_authenticate($token, $change_password['old_password']);
                        if ($check_old_password == false) {
                            echo json_encode(array("statusCode" => 400, "message" => "old password does not matched."));
                        } else {
                            if ($get_authenticate->user_password != md5($change_password['new_password'])) {
                                $changePassword['user_password'] = md5($change_password['new_password']);
                                $this->user_model->update_user_by_id($get_authenticate->user_id, $changePassword);
                                echo json_encode(array("statusCode" => 200, "message" => "new Password update successfully."));
                            } else {
                                echo json_encode(array("statusCode" => 400, "message" => "New Password should not match with old password"));
                            }
                        }
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getProfile()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            //$profileData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                // if(!empty($profileData))
                // {
                //     $this->form_validation->set_data($profileData);
                //     $this->form_validation->set_rules('user_id','user_id','trim|required');
                //    if($this->form_validation->run()==FALSE)
                //     {
                //         echo json_encode(array("statusCode"=>400,"message"=>strip_tags(validation_errors())));
                //     }
                //     else
                //     {
                $response = array();
                $rowData = $this->user_model->check_data_by_user_id($get_authenticate->user_id);
                if (!empty($rowData)) {
                    $response = array(
                        'user_id' => $rowData->user_id,
                        'user_first_name' => isset($rowData->user_first_name) && trim($rowData->user_first_name) != null ? $rowData->user_first_name : "",
                        'user_last_name' => isset($rowData->user_last_name) && trim($rowData->user_last_name) != null ? $rowData->user_last_name : "",
                        'user_email' => isset($rowData->user_email) && trim($rowData->user_email) != null ? $rowData->user_email : "",
                        'user_phone' => isset($rowData->user_phone) && trim($rowData->user_phone) != null ? $rowData->user_phone : "",
                        'user_image' => isset($rowData->user_image) && trim($rowData->user_image) != null ? base_url() . 'uploads/profile_images/' . $rowData->user_image : "",
                        'device_token' => isset($rowData->device_token) && trim($rowData->device_token) != null ? $rowData->device_token : "",
                        'user_status' => $rowData->user_status,
                        'user_created_on' => $rowData->user_created_on,
                        'user_updated_on' => $rowData->user_updated_on,
                    );
                }

                echo json_encode(array("statusCode" => 200, "data" => $response, "message" => "Profile listed successfully"));
                //     }
                // }
                // else
                // {
                //     echo json_encode(array("statusCode"=>400,"message"=>"Empty Data"));
                // }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getFreeWashCount()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $free_wash = 0;
                $rowData = $this->user_model->check_data_by_user_id($get_authenticate->user_id);
                if (!empty($rowData)) {
                    $free_wash = $rowData->free_body_wash;
                }

                echo json_encode(array("statusCode" => 200, "free_wash" => $free_wash, "message" => "Free wash count successfully listed."));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getModel()
    {
        $ModelData = $this->user_model->fetchModel();
        echo json_encode(array("statusCode" => 200, "message" => " vehicle Model Data listed successfully", "data" => $ModelData));
    }

    public function getMake()
    {
        $MakeData = $this->user_model->fetchMake();
        echo json_encode(array("statusCode" => 200, "message" => " vehicle Make Data listed successfully", "data" => $MakeData));
    }

    public function getType()
    {
        $TypeData = $this->user_model->fetchType();
        echo json_encode(array("statusCode" => 200, "message" => " vehicle Type Data listed successfully", "data" => $TypeData));
    }

    public function fetchModelByMakeId()
    {
        $modelData = json_decode(trim(file_get_contents('php://input')), true);
        if (!empty($modelData)) {
            $this->form_validation->set_data($modelData);
            $this->form_validation->set_rules('make_id', 'make_id', 'trim|required');
            if ($this->form_validation->run() == false) {
                echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
            } else {
                $responseData = array();
                $rowData = $this->user_model->getModelById($modelData['make_id']);
                if (!empty($rowData)) {
                    foreach ($rowData as $modelKey => $modelValue) {
                        $responseData[$modelKey]['make_id'] = $modelValue['make_id'];
                        $responseData[$modelKey]['model_id'] = $modelValue['model_id'];
                        $responseData[$modelKey]['make'] = $modelValue['make'];
                        $responseData[$modelKey]['model'] = $modelValue['model'];

                    }

                }
                echo json_encode(array("statusCode" => 200, "message" => "vehicle Model listed successfully.", "data" => $responseData));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "empty Data"));
        }
    }

    public function addVehicle()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $addVehicle = $this->input->post();
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                if (!empty($addVehicle)) {
                    $this->form_validation->set_data($addVehicle);
                    $this->form_validation->set_rules('vehicle_make_id', 'vehicle_make_id', 'trim|required');
                    $this->form_validation->set_rules('vehicle_model_id', 'vehicle_model_id', 'trim|required');
                    $this->form_validation->set_rules('vehicle_type_id', 'vehicle_type_id', 'trim|required');
                    $this->form_validation->set_rules('vehicle_plate_no', 'vehicle_plate_no', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        if (isset($_FILES['vehicle_image']['name']) && trim($_FILES['vehicle_image']['name']) != null) {
                            /****************** vehicle Image**************** */
                            $fileName = $_FILES['vehicle_image']['name'];
                            $tmpName = $_FILES['vehicle_image']['tmp_name'];
                            $uploadPath = 'uploads/vehicle_images/';
                            $imageName = $uploadPath . $fileName;
                            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $actualFileName = time() . "." . $fileExtension;
                            $image_dir = 'uploads/vehicle_images';
                            $imgConfig['upload_path'] = $image_dir;
                            $imgConfig['allowed_types'] = 'gif|jpg|png|jpeg|PNG';
                            $imgConfig['file_name'] = $actualFileName;
                            $this->load->library('upload', $actualFileName);
                            $this->upload->initialize($imgConfig);
                            if ($this->upload->do_upload('vehicle_image')) {
                                $profileImage = $this->upload->data();
                                $addVehicleMaster['vehicle_image'] = $profileImage['file_name'];
                            } else {
                                $error = array('error' => $this->upload->display_errors());
                                var_dump($error);
                            }
                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "All images are required"));
                        }

                        $data = array(
                            'vehicle_user_id' => $get_authenticate->user_id,
                            'vehicle_make_id' => $addVehicle['vehicle_make_id'],
                            'vehicle_model_id' => $addVehicle['vehicle_model_id'],
                            'vehicle_type_id' => $addVehicle['vehicle_type_id'],
                            'vehicle_engine' => $addVehicle['vehicle_engine'],
                            'vehicle_plate_no' => $addVehicle['vehicle_plate_no'],
                            'vehicle_image' => $addVehicleMaster['vehicle_image'],
                            'vehicle_created_on' => $this->getDate(),
                        );

                        if ($vehicleId = $this->user_model->insertVehicle($data)) {
                            $dataVehicle['vehicle_id'] = $vehicleId;
                            $dataVehicle['vehicle_user_id'] = $get_authenticate->user_id;
                            $this->user_model->vehicleIsSet($dataVehicle);
                            $response = array(
                                'vehicle_id' => $vehicleId,
                                'vehicle_user_id' => $get_authenticate->user_id,
                                'vehicle_make_id' => $addVehicle['vehicle_make_id'],
                                'vehicle_model_id' => $addVehicle['vehicle_model_id'],
                                'vehicle_type_id' => $addVehicle['vehicle_type_id'],
                                'vehicle_engine' => $addVehicle['vehicle_engine'],
                                'vehicle_plate_no' => $addVehicle['vehicle_plate_no'],
                                'vehicle_image' => base_url('uploads/vehicle_images/') . $addVehicleMaster['vehicle_image'],
                                'vehicle_created_on' => $this->getDate(),
                            );
                            echo json_encode(array("statusCode" => 200, "message" => "vehicle added successfully.", "data" => $response));
                        }
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "empty Data"));
                }

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "invalid token"));
        }
    }
    public function editVehicle()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($status_Data)) {

                if (!empty($get_authenticate)) {
                    $data = array(
                        "vehicle_model_id" => $status_Data['vehicle_model_id'],
                        "vehicle_type_id" => $status_Data['vehicle_type_id'],
                        "vehicle_engine" => $status_Data['vehicle_engine'],
                        "vehicle_plate_no" => $status_Data['vehicle_plate_no'],
                        'vehicle_created_on' => $this->getDate(),
                    );
                    $this->user_model->editVehicle($status_Data['vehicle_id'], $data);
                    echo json_encode(array("statusCode" => 200, "message" => "Vehicle Updated successfully"));

                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getVehicleByUserID()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $profileData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($profileData)) {
                    $this->form_validation->set_data($profileData);
                    $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $responseData = array();
                        $rowData = $this->user_model->fetchVehicleByUserId($profileData['user_id']);
                        if (!empty($rowData)) {

                            foreach ($rowData as $vehicleKey => $vehicleValue) {
                                $responseData[$vehicleKey]['vehicle_id'] = $vehicleValue['vehicle_id'];
                                $responseData[$vehicleKey]['vehicle_user_id'] = $vehicleValue['vehicle_user_id'];
                                $responseData[$vehicleKey]['make_id'] = $vehicleValue['make_id'];
                                $responseData[$vehicleKey]['make'] = $vehicleValue['make'];
                                $responseData[$vehicleKey]['make_logo'] = base_url('uploads/cars_logo/') . $vehicleValue['make_logo'];
                                $responseData[$vehicleKey]['model_id'] = $vehicleValue['model_id'];
                                $responseData[$vehicleKey]['model'] = $vehicleValue['model'];
                                $responseData[$vehicleKey]['vehicle_type_id'] = $vehicleValue['vehicle_type_id'];
                                $responseData[$vehicleKey]['type'] = $vehicleValue['type'];
                                $responseData[$vehicleKey]['vehicle_image'] = base_url('uploads/vehicle_images/') . $vehicleValue['vehicle_image'];
                                $responseData[$vehicleKey]['vehicle_engine'] = $vehicleValue['vehicle_engine'];
                                $responseData[$vehicleKey]['vehicle_plate_no'] = $vehicleValue['vehicle_plate_no'];
                                $responseData[$vehicleKey]['vehicle_created_on'] = $vehicleValue['vehicle_created_on'];
                                $responseData[$vehicleKey]['is_default'] = $vehicleValue['is_default'];
                            }
                        }

                        echo json_encode(array("statusCode" => 200, "data" => $responseData, "message" => "Vehicle listed successfully"));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function updateVehicleIsSet()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('vehicle_id', 'vehicle_id', 'trim|required');
                    $this->form_validation->set_rules('vehicle_user_id', 'vehicle_user_id', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $this->user_model->vehicleIsSet($status_Data);
                        echo json_encode(array("statusCode" => 200, "message" => "Vehicle set as default Successfully"));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getPlan()
    {
        $PlanData = $this->user_model->fetchPlan();
        echo json_encode(array("statusCode" => 200, "message" => "Plan Data listed successfully", "data" => $PlanData));
    }

    public function cancelBooking()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('booking_id', 'booking_id', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
                        if (!empty($bookingData)) {
                            if ($bookingData->booking_status != 'completed') {
                                $date = date("Y-m-d H:i:s");
                                $updateStatus['booking_status'] = "cancel";
                                $this->user_model->status_update($status_Data['booking_id'], $token, $updateStatus);
                                $sms_message = "Dear Valued Customer, Your Order Number " . $bookingData->invoice_no . " Has been cancelled. If you have already paid, refund will be initiated shortly.";
                                //$this->sendSms($sms_message, $get_authenticate->user_phone);
                                $notify_type = 'booking-cancelled';
                                $type = 'Booking cancelled';
                                $notificationData = array(
                                    ['user_id' => $get_authenticate->user_id,
                                        'type' => $type,
                                        'title' => 'Booking Cancelled',
                                        'description' => $sms_message,
                                        'status' => 1,
                                        'created_at' => $date,
                                        'updated_at' => $date],
                                );
                                $this->user_model->insertNotification($notificationData);
                                $this->user_model->push_notify($get_authenticate->device_token, $sms_message, $type, $notify_type, $get_authenticate->user_type);
                                $subject = 'Order Greenly - Cancel Booking.';
                                $content['userName'] = $get_authenticate->user_first_name;
                                $content['content'] = $sms_message;
                                $this->user_model->emailSending($get_authenticate->user_email, $subject, $content);
                                echo json_encode(array("statusCode" => 200, "message" => "Booking cancel Successfully"));

                            } else {
                                echo json_encode(array("statusCode" => 400, "message" => "Order as been completed."));
                            }
                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "No booking found for this id"));
                        }

                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function get_upcoming_order()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $orderData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                $responseData = array();
                $rowData = $this->user_model->upcoming_Order($token, $get_authenticate);
                if ($get_authenticate->user_type == 2) {
                    if (in_array(3, json_decode($get_authenticate->service_type, true))) {
                        $requestOrders = $this->user_model->getRequestRecoveryOrders($get_authenticate->user_id);
                        foreach ($requestOrders as $reqOrder) {
                            array_push($rowData, $reqOrder);
                        }
                    }
                }

                if (!empty($rowData)) {
                    foreach ($rowData as $orderKey => $orderValue) {
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
                        if ($orderValue['booking_status'] == 'open' && $get_authenticate->user_type == 2) {
                            $responseData[$orderKey]['booking_status'] = 'pending';
                        } else {

                            $responseData[$orderKey]['booking_status'] = $orderValue['booking_status'];
                        }
                        $responseData[$orderKey]['booked_on'] = date('d/m/Y', $orderValue['booked_on']);
                        $responseData[$orderKey]['service_type'] = $orderValue['service_type'];
                        $responseData[$orderKey]['booking_date_time'] = isset($orderValue['booking_date_time']) && trim($orderValue['booking_date_time']) != null ? $orderValue['booking_date_time'] : "";
                        if ($orderValue['booking_code'] != null) {
                            $responseData[$orderKey]['booking_code'] = $orderValue['booking_code'];
                        }
                        if ($orderValue['service_id'] == 2) {
                            $responseData[$orderKey]['half_amount'] = $orderValue['total_amount'] / 2;
                            $getQuotationStatus = $this->user_model->getQuotationStatus($orderValue['booking_id'], $get_authenticate->user_type);
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
                        $responseData[$orderKey]['invoice_path'] = ($orderValue['invoice_path'] != null) ? base_url() . 'uploads/invoices/' . $orderValue['invoice_path'] : null;

                    }

                }
                echo json_encode(array("statusCode" => 200, "message" => "Upcoming order listed successfully.", "data" => $responseData));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "invalid token"));
        }
    }

    public function get_past_order()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $orderData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                $responseData = array();
                $rowData = $this->user_model->past_Order($token, $get_authenticate);
                if (!empty($rowData)) {
                    foreach ($rowData as $orderKey => $orderValue) {
                        $reviewData = $this->user_model->fetchReviewByBookingId($orderValue['booking_id']);
                        if (!empty($reviewData)) {
                            $responseData[$orderKey]['review_star'] = isset($reviewData->review_star) && trim($reviewData->review_star) != null ? $reviewData->review_star : "";
                            $responseData[$orderKey]['review'] = isset($reviewData->review) && trim($reviewData->review) != null ? $reviewData->review : "";
                        }
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
                        $responseData[$orderKey]['service_type'] = $orderValue['service_type'];
                        $responseData[$orderKey]['invoice_path'] = ($orderValue['invoice_path'] != null) ? base_url() . 'uploads/invoices/' . $orderValue['invoice_path'] : null;
                    }

                }
                echo json_encode(array("statusCode" => 200, "message" => "Past order listed successfully.", "data" => $responseData));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "invalid token"));
        }
    }

    public function getOrderById()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $profileData = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($profileData)) {
                    $this->form_validation->set_data($profileData);
                    $this->form_validation->set_rules('booking_id', 'booking_id', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $responseData = array();
                        $rowData = $this->user_model->fetchOrderById($profileData['booking_id']);
                        if (!empty($rowData)) {

                            foreach ($rowData as $BookingKey => $BookingValue) {
                                if (isset($BookingValue['booked_to']) && trim($BookingValue['booked_to']) != null) {
                                    $bookedToData = $this->user_model->check_data_by_user_id($BookingValue['booked_to']);
                                    if (!empty($bookedToData)) {
                                        $responseData[$BookingKey]['user_first_name'] = isset($bookedToData->user_first_name) && trim($bookedToData->user_first_name) != null ? $bookedToData->user_first_name : "";
                                        $responseData[$BookingKey]['user_last_name'] = isset($bookedToData->user_last_name) && trim($bookedToData->user_last_name) != null ? $bookedToData->user_last_name : "";
                                        $responseData[$BookingKey]['user_phone'] = isset($bookedToData->user_phone) && trim($bookedToData->user_phone) != null ? $bookedToData->user_phone : "";
                                        $responseData[$BookingKey]['user_lat'] = isset($bookedToData->user_lat) && trim($bookedToData->user_lat) != null ? $bookedToData->user_lat : "";
                                        $responseData[$BookingKey]['user_long'] = isset($bookedToData->user_long) && trim($bookedToData->user_long) != null ? $bookedToData->user_long : "";
                                        $responseData[$BookingKey]['user_image'] = isset($bookedToData->user_image) && trim($bookedToData->user_image) != null ? base_url() . 'uploads/profile_images/' . $bookedToData->user_image : "";
                                    }

                                }
                                $responseData[$BookingKey]['booking_id'] = $BookingValue['booking_id'];
                                $responseData[$BookingKey]['booked_by'] = $BookingValue['booked_by'];
                                $responseData[$BookingKey]['booked_to'] = isset($BookingValue['booked_to']) && trim($BookingValue['booked_to']) != null ? $BookingValue['booked_to'] : "";
                                $responseData[$BookingKey]['plan_id'] = $BookingValue['plan_id'];
                                $responseData[$BookingKey]['location'] = $BookingValue['location'];
                                $responseData[$BookingKey]['location_lat'] = $BookingValue['location_lat'];
                                $responseData[$BookingKey]['location_long'] = $BookingValue['location_long'];
                                $responseData[$BookingKey]['payment_method'] = $BookingValue['payment_method'];
                                $responseData[$BookingKey]['total_amount'] = $BookingValue['total_amount'];
                                $responseData[$BookingKey]['booked_on'] = $BookingValue['booked_on'];
                                $responseData[$BookingKey]['booking_status'] = $BookingValue['booking_status'];

                            }
                        }

                        echo json_encode(array("statusCode" => 200, "data" => $responseData, "message" => "Order listed successfully"));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function addReview()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('review_to', 'review_to', 'trim|required');
                    $this->form_validation->set_rules('booking_id', 'booking_id', 'trim|required');
                    $this->form_validation->set_rules('review_star', 'review_star', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
                        if (!empty($bookingData)) {
                            if ($bookingData->booking_status == 'CP') {
                                $reviewData['review_by'] = $get_authenticate->user_id;
                                $reviewData['review_to'] = $status_Data['review_to'];
                                $reviewData['booking_id'] = $status_Data['booking_id'];
                                $reviewData['review_star'] = $status_Data['review_star'];
                                $reviewData['review_on'] = $this->getDate();

                                if (isset($status_Data['review']) && trim($status_Data['review']) != null) {
                                    $reviewData['review'] = $status_Data['review'];
                                }
                                $this->user_model->insertReview($reviewData);

                                echo json_encode(array("statusCode" => 200, "message" => "Review added Successfully"));
                            } else {
                                echo json_encode(array("statusCode" => 400, "message" => "Review can be give on completed booking only."));
                            }
                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "No booking found for this id"));
                        }

                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function updatePassword()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $change_password = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($change_password)) {
                    $this->form_validation->set_data($change_password);
                    $this->form_validation->set_rules('new_password', 'new_password', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        if ($get_authenticate->user_password != md5($change_password['new_password'])) {
                            $changePassword['user_password'] = md5($change_password['new_password']);
                            $this->user_model->update_user_by_id($get_authenticate->user_id, $changePassword);
                            echo json_encode(array("statusCode" => 200, "message" => "new Password update successfully."));
                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "New Password should not match with old password"));
                        }
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function phoneExist()
    {
        $status_Data = json_decode(trim(file_get_contents('php://input')), true);
        if (!empty($status_Data)) {
            $this->form_validation->set_data($status_Data);
            $this->form_validation->set_rules('user_phone', 'user_phone', 'trim|required');
            if ($this->form_validation->run() == false) {
                echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
            } else {
                // echo $status_Data['user_phone'];
                $userData = $this->user_model->checkPhone($status_Data['user_phone']);
                if (!empty($userData)) {
                    $token = jwt::encode($userData->user_id, jwtKey);
                    echo json_encode(array("statusCode" => 200, "message" => "Phone Number Exist", "token" => $token));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Phone number not exist."));
                }

            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
        }
    }

    //29 Jan start
    public function getGarageByMakeId()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('make_id', 'make_id', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $GarageData = $this->user_model->getAllGarageByMakeId($status_Data['make_id']);
                        $responseData = array();
                        if (!empty($GarageData)) {
                            foreach ($GarageData as $GarageKey => $GarageValue) {
                                $responseData[$GarageKey]['make_id'] = $GarageValue['make_id'];
                                $responseData[$GarageKey]['make_name'] = $GarageValue['make'];
                                $responseData[$GarageKey]['garage_id'] = $GarageValue['garage_id'];
                                $responseData[$GarageKey]['garage_name'] = $GarageValue['garage_name'];
                                $responseData[$GarageKey]['garage_lat'] = $GarageValue['garage_lat'];
                                $responseData[$GarageKey]['garage_long'] = $GarageValue['garage_long'];
                                $responseData[$GarageKey]['garage_location'] = $GarageValue['garage_location'];

                            }

                        }
                        echo json_encode(array("statusCode" => 200, "message" => "Garage Listed Successfully", "data" => $responseData));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getNearestWashStation()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('user_lat', 'user_lat', 'trim|required');
                    $this->form_validation->set_rules('user_long', 'user_long', 'trim|required');
                    $this->form_validation->set_rules('user_location', 'user_location', 'trim');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $CenterData = $this->user_model->getNearestWashStation($status_Data['user_lat'], $status_Data['user_long'], $status_Data['service_type']);
                        $responseData = array();
                        if (!empty($CenterData)) {
                            foreach ($CenterData as $CenterKey => $CenterValue) {

                                $getServiceByID = $this->admin_model->getGarageService(implode(',', json_decode($CenterValue['service_type'])));
                                $responseData[$CenterKey]['center_id'] = $CenterValue['user_id'];
                                $responseData[$CenterKey]['center_name'] = $CenterValue['garage_name'];
                                $responseData[$CenterKey]['center_location'] = $CenterValue['garage_location'];
                                $responseData[$CenterKey]['center_description'] = $CenterValue['garage_description'];
                                $responseData[$CenterKey]['center_logo'] = (!empty($CenterValue['garage_logo'])) ? base_url() . "uploads/garage_logo/" . $CenterValue['garage_logo'] : "";
                                $responseData[$CenterKey]['center_lat'] = $CenterValue['user_lat'];
                                $responseData[$CenterKey]['center_long'] = $CenterValue['user_long'];
                                $responseData[$CenterKey]['center_status'] = $CenterValue['user_status'];
                                $responseData[$CenterKey]['center_type'] = $CenterValue['user_status'];
                                $responseData[$CenterKey]['service_type'] = $getServiceByID;
                                $responseData[$CenterKey]['distance'] = round($CenterValue['distance'], 1);
                            }

                        }
                        echo json_encode(array("statusCode" => 200, "message" => "Car Wash Center Listed Successfully", "data" => $responseData));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getSubServices_old()
    {
        $token = $this->chckToken();
        if (trim($token) != null ) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate) ) {
                $ServicesData = $this->user_model->getAllServicesByCenterId(); 
                $responseData = array();
                if (!empty($ServicesData)) {
                    foreach ($ServicesData as $ServicesKey => $ServicesValue) {
                        $responseData[$ServicesKey]['service_id'] = $ServicesValue['sub_service_id'];
                        $responseData[$ServicesKey]['service_name'] = $ServicesValue['service_name'];
                        $responseData[$ServicesKey]['service_price'] = $ServicesValue['service_price'];
                        $responseData[$ServicesKey]['service_status'] = $ServicesValue['service_status'];
                    }
                }
                echo json_encode(array("statusCode" => 200, "message" => "Services Listed Successfully", "data" => $responseData));

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }


    public function getSubServices()
    {
        $token = $this->chckToken();
        if (trim($token) != null ) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)  ) {
                $ServicesData = $this->user_model->getMappedSubServicesByUserId($status_Data['userId'],$status_Data['serviceId']);

                $responseData = array();
                if (!empty($ServicesData)) {
                    foreach ($ServicesData as $ServicesKey => $ServicesValue) {
                        $responseData[$ServicesKey]['service_id'] = $ServicesValue['service_id'];
                        $responseData[$ServicesKey]['sub_service_id'] = $ServicesValue['sub_service_id'];
                        $responseData[$ServicesKey]['service_name'] = $ServicesValue['service_name'];
                        $responseData[$ServicesKey]['service_price'] = $ServicesValue['service_price'];
                        $responseData[$ServicesKey]['default_price'] = $ServicesValue['default_price'];
                        $responseData[$ServicesKey]['mapping_id'] = $ServicesValue['mapping_id'];
                        $responseData[$ServicesKey]['service_status']=1;
                    }
                    echo json_encode(array("statusCode" => 200, "message" => "Services Listed Successfully", "data" => $responseData));
                }
                else{
                    echo json_encode(array("statusCode" => 400, "message" => "No Data Found!", "data" => []));
                }
                

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function BookWashServices()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('center_id', 'Garage', 'trim|required');
                    $this->form_validation->set_rules('total_amount', 'Total Amount', 'trim|required');
                    $this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required');
                    $this->form_validation->set_rules('sub_service_id[]', 'Sub service', 'trim|required');
                    $this->form_validation->set_rules('service_mapping_id[]', 'Mapped Services', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        // $checkVehicle = $this->user_model->checkVehicle($status_Data['vehicle_id'], $get_authenticate->user_id);
                        // if (!$checkVehicle) {
                        //     echo json_encode(array("statusCode" => 400, "message" => "This vehicle not available"));
                        //     exit;
                        // }
                        $checkBooking = $this->user_model->checkBooking($get_authenticate->user_id, $status_Data['vehicle_id'], 1);
                        if ($checkBooking) {
                            echo json_encode(array("statusCode" => 400, "message" => "This vehicle already booked"));
                            exit;
                        }

                        if (trim($status_Data['payment_method']) == 'Card') {
                            if (isset($status_Data['payment_id']) && trim($status_Data['payment_id']) != null) {
                                $Data['payment_id'] = $status_Data['payment_id'];
                                $Data['is_paid'] = 1;
                            } else {
                                echo json_encode(array("statusCode" => 400, "message" => "Payment Id is required"));
                                exit;
                            }
                        } else {
                            $Data['is_paid'] = 0;
                        }
                        $date = date("Y-m-d H:i:s");
                        $orderNumber = $this->generateNumber();
                        $CenterDetailData = array(
                            'garage_id' => $status_Data['center_id'],
                            'invoice_no' => $this->user_model->invoiceNumber('W'),
                            'booked_by' => $token,
                            'sub_service_id' => json_encode($status_Data['sub_service_id']),
                            'service_mapping_id' => json_encode($status_Data['service_mapping_id']),
                            'booking_status' => "open",
                            'service_type' => 1,
                            'vehicle_id' => $status_Data['vehicle_id'],
                            'location_lat' => $status_Data['location_lat'],
                            'location_long' => $status_Data['location_long'],
                            'total_amount' => $status_Data['total_amount'],
                            'paid_amount' => $status_Data['total_amount'],
                            'vat_amount' => $status_Data['vat_amount'],
                            'payment_method' => $status_Data['payment_method'],
                            'instruction' => $status_Data['instruction'],
                            'grand_total' => round($status_Data['total_amount'] + $status_Data['vat_amount'], 1),
                            'is_paid' => $Data['is_paid'],
                            'booked_on' => $this->getDate(),
                            'booking_code' => $orderNumber ?? null,
                        );
                        if (isset($status_Data['promo_id'])) {
                            $CenterDetailData['promo_id'] = $status_Data['promo_id'];
                            $updateUserPromoData['promo_id'] = $status_Data['promo_id'];
                            $updateUserPromoData['user_id'] = $token;
                            $updateUserPromoData['created_at'] = $date;
                            $this->user_model->insertUserPromo($updateUserPromoData);
                        }
                        $bookingId = $this->user_model->insertBookingData($CenterDetailData);
                        $provider = $this->user_model->check_data_by_user_id($status_Data['center_id']);
                        $sms_message = "Dear Valued Customer, Your Order Number " . $CenterDetailData['invoice_no'] . " Has been confirmed, Thank You, Greenly Team.";
                        $sms_message_html = "<p>Dear Valued Customer, Your Order Number <b>" . $CenterDetailData['invoice_no'] . "</b> Has been confirmed. Thank You, Greenly Team. <a href='https://www.google.com/maps/place/" . $provider->user_lat . "," . $provider->user_long . "'>Click to navigate</a></p>";

                        if ($Data['is_paid'] == 1) {
                            $sms_message = "Dear Valued Customer, Your Order Number " . $CenterDetailData['invoice_no'] . " Has been confirmed with Paid amount " . $CenterDetailData['grand_total'] . " Dhs, Thank You, Greenly Team.";
                            $sms_message_html = "<p>Dear Valued Customer, Your Order Number <b>" . $CenterDetailData['invoice_no'] . "</b> Has been confirmed with Paid amount <b>" . $CenterDetailData['grand_total'] . " Dhs</b>, Thank You, Greenly Team. <a href='https://www.google.com/maps/place/" . $provider->user_lat . "," . $provider->user_long . "'>Click to navigate</a></p>";
                            $filename = $bookingId . time() . '.pdf';
                            $updatedData['invoice_path'] = $filename;
                            $paymentData = array(
                                'booking_id' => $bookingId,
                                'payment_id' => $status_Data['payment_id'],
                                'invoice_id' => $status_Data['invoice_id'],
                                'transaction_id' => $status_Data['transaction_id'],
                                'transaction_date' => $status_Data['transaction_date'],
                                'transaction_status' => $status_Data['transaction_status'],
                                'paid_amount' => $CenterDetailData['grand_total'],
                                'paid_by' => $token,
                                'payment_invoice' => $filename,
                                'payment_status' => 1,
                                'created_at' => $date,
                                'updated_at' => $date,
                            );
                            $this->user_model->insertPayment($paymentData);
                            $invoice = $this->createInvoice($filename, $bookingId);
                            $this->user_model->updateBookingService($bookingId, $updatedData);
                            $msg = 'Order Greenly Yalla Wash Booking Invoice - ' . $CenterDetailData['invoice_no'];
                            $content['userName'] = $get_authenticate->user_first_name;
                            $content['content'] = $sms_message;
                            $content['invoice'] = $filename;
                            $this->user_model->emailSendingWithAttach($get_authenticate->user_email, $msg, $content);

                        }
                        //push notification
                        $this->bookingNotify($get_authenticate, $provider, 'Yalla Wash', $sms_message_html, 'yallo-wash', $sms_message);
                        //$this->sendSms($sms_message, $get_authenticate->user_phone);
                        $response = $this->user_model->getOrderDetailById($bookingId);
                        $response->booked_on = date('d/m/Y', $response->booked_on);
                        $orderDetail['orderDetail'] = $response;

                        echo json_encode(array("statusCode" => 200, "message" => "Services Booked Successfully", "data" => $orderDetail));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    private function bookingNotify($get_authenticate, $provider, $type, $smsMessageHtml, $notify_type, $smsMessage)
    {
        $msg = 'New order received by ' . $type;
        $date = date("Y-m-d H:i:s");
        $notificationData = array(
            ['user_id' => $get_authenticate->user_id,
                'type' => $type,
                'title' => 'Booking',
                'description' => $smsMessageHtml,
                'status' => 1,
                'created_at' => $date,
                'updated_at' => $date],
            [
                'user_id' => $provider->user_id,
                'type' => $type,
                'title' => 'Booking',
                'description' => $msg,
                'status' => 1,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        );
        $this->user_model->insertNotification($notificationData);
        $this->user_model->push_notify($get_authenticate->device_token, $smsMessage, $type, $notify_type, $get_authenticate->user_type);
        $this->user_model->push_notify($provider->device_token, $msg, $type, $notify_type, $provider->user_type);
    }
    public function BookGarageServices()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('garage_id', 'garage_id', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $checkVehicle = $this->user_model->checkVehicle($status_Data['vehicle_id'], $get_authenticate->user_id);
                        if (!$checkVehicle) {
                            echo json_encode(array("statusCode" => 400, "message" => "This vehicle not available"));
                            exit;
                        }
                        $checkProviderModel = $this->user_model->checkProviderModel($status_Data['garage_id'], $checkVehicle->vehicle_make_id);
                        if (!$checkProviderModel) {
                            echo json_encode(array("statusCode" => 400, "message" => "This Make not available in this Garage."));
                            exit;
                        }
                        $checkBooking = $this->user_model->checkBooking($get_authenticate->user_id, $status_Data['vehicle_id'], 2);
                        if ($checkBooking) {
                            echo json_encode(array("statusCode" => 400, "message" => "This vehicle already booked"));
                            exit;
                        }
                        $orderNumber = $this->generateNumber();
                        $status_Data['is_paid'] = 0;
                        $GarageData = $this->user_model->getGarageByGarageId($status_Data['garage_id']);
                        $responseData = array();
                        if (!empty($GarageData)) {
                            $orderNumber = $this->generateNumber();
                            $BookingData = array(
                                'garage_id' => $status_Data['garage_id'],
                                'location_lat' => $status_Data['location_lat'],
                                'location_long' => $status_Data['location_long'],
                                'invoice_no' => $this->user_model->invoiceNumber('F'),
                                'booked_by' => $token,
                                'booking_status' => "open",
                                'service_type' => 2,
                                'vehicle_id' => $status_Data['vehicle_id'],
                                'booking_code' => $orderNumber ?? null,
                                'is_paid' => $status_Data['is_paid'],
                                'instruction' => $status_Data['instruction'],
                                'booked_on' => $this->getDate(),
                                'appointment_date' => $status_Data['appointment_date'], 
                            );
                            if (isset($status_Data['promo_id'])) {
                                $date = date("Y-m-d H:i:s");
                                $BookingData['promo_id'] = $status_Data['promo_id'];
                                $updateUserPromoData['promo_id'] = $status_Data['promo_id'];
                                $updateUserPromoData['user_id'] = $token;
                                $updateUserPromoData['created_at'] = $date;
                                $this->user_model->insertUserPromo($updateUserPromoData);
                            }
                            $bookingId = $this->user_model->insertBookingData($BookingData);
                            $provider = $this->user_model->check_data_by_user_id($status_Data['garage_id']);
                            $sms_message = "Dear Valued Customer, Your Order Number " . $BookingData['invoice_no'] . " Has been confirmed, Thank You, Greenly Team.";
                            $sms_message_html = "<p>Dear Valued Customer, Your Order Number <b>" . $BookingData['invoice_no'] . " </b>has been confirmed, Thank You, Greenly Team. <a href='https://www.google.com/maps/place/" . $provider->user_lat . "," . $provider->user_long . "'>Click to navigate</a></p>";
                            $quotation_sms = "We are prepering a quotation for your vehicle. (Order No:" . $BookingData['invoice_no'] . ", Car Reg No:" . $checkVehicle->vehicle_plate_no . ") Thank You.";
                            $provider_sms_content = "Hi " . $GarageData->garage_name . ", New order greenly Yalla Fix received.";
                            //$this->sendSms($sms_message, $get_authenticate->user_phone);
                            //$this->sendSms($quotation_sms, $get_authenticate->user_phone);
                            //$this->sendSms($provider_sms_content, $GarageData->user_phone);
                            $this->bookingNotify($get_authenticate, $provider, 'Yalla Fix', $sms_message_html, 'yallo-fix', $sms_message);
                            $response = $this->user_model->getOrderDetailById($bookingId);
                            $response->booked_on = date('d/m/Y', $response->booked_on);
                            $orderDetail['orderDetail'] = $response;

                            $subject = 'Order Greenly Yalla Fix ';
                            $content['userName'] = $get_authenticate->user_first_name;
                            $content['content'] = $sms_message;
                            $this->user_model->emailSending($get_authenticate->user_email, $subject, $content);

                            $provider_subject = 'New Yalla Fix order received';
                            $provider_content['userName'] = $get_authenticate->user_first_name;
                            $provider_content['content'] = $provider_sms_content;
                            $this->user_model->emailSending($GarageData->user_email, $provider_subject, $provider_content);

                            echo json_encode(array("statusCode" => 200, "message" => "Garage service Booked Successfully", "data" => $orderDetail));
                        }

                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function BookGoEnQaZ()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('location', 'Location', 'trim|required');
                    $this->form_validation->set_rules('location_lat', 'Latitude', 'trim|required');
                    $this->form_validation->set_rules('location_long', 'Latitude', 'trim|required');
                    $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim');
                    $this->form_validation->set_rules('another_location', 'Another Location', 'trim');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $checkBooking = $this->user_model->checkBooking($get_authenticate->user_id, $status_Data['vehicle_id'], 3);
                        if ($checkBooking) {
                            echo json_encode(array("statusCode" => 400, "message" => "This vehicle already booked"));
                            exit;
                        }
                        $orderNumber = $this->generateNumber();
                        $GarageData = $this->user_model->getNearestWashStation($status_Data['location_lat'], $status_Data['location_long'], $status_Data['service_type']);
                        $responseData = array();
                        if (!empty($GarageData)) {
                            $date = date("Y-m-d H:i:s");
                            $BookingData = array(
                                'invoice_no' => $this->user_model->invoiceNumber('R'),
                                'location' => $status_Data['location'],
                                'location_lat' => $status_Data['location_lat'],
                                'location_long' => $status_Data['location_long'],
                                'to_address' => $status_Data['to_location'],
                                'to_location_lat' => $status_Data['to_location_lat'],
                                'to_location_long' => $status_Data['to_location_long'],
                                'phone_number' => isset($status_Data['phone_number']) && trim($status_Data['phone_number']) != null ? $status_Data['phone_number'] : '',
                                'booked_by' => $token,
                                'booking_status' => "request",
                                'service_type' => $status_Data['service_type'],
                                'total_amount' => $status_Data['total_amount'],
                                'vat_amount' => $status_Data['vat_amount'],
                                'paid_amount' => $status_Data['total_amount'],
                                'grand_total' => round($status_Data['total_amount'] + $status_Data['vat_amount'], 1),
                                'payment_method' => $status_Data['payment_method'],
                                'booked_on' => $this->getDate(),
                                'vehicle_id' => $status_Data['vehicle_id'],
                                'distance' => $status_Data['distance'],
                                'instruction' => $status_Data['instruction'],
                                'booking_code' => $orderNumber ?? null,
                                'is_paid' => 1,
                            );
                            if (isset($status_Data['promo_id'])) {
                                $date = date("Y-m-d H:i:s");
                                $BookingData['promo_id'] = $status_Data['promo_id'];
                                $updateUserPromoData['promo_id'] = $status_Data['promo_id'];
                                $updateUserPromoData['user_id'] = $token;
                                $updateUserPromoData['created_at'] = $date;
                                $this->user_model->insertUserPromo($updateUserPromoData);
                            }
                            if ($bookingId = $this->user_model->insertBookingData($BookingData)) {
                                $orderText = "";
                                if ($orderNumber != "") {
                                    $orderText = " Your booking code is " . $BookingData['invoice_no'];
                                }
                                $sms_message = "Dear Valued Customer, Your Order Number " . $BookingData['invoice_no'] . " Has been confirmed with Paid amount " . $BookingData['grand_total'] . " Dhs, Thank You, Greenly Team.";
                                $sms_message_html = "Dear Valued Customer, Your Order Number " . $BookingData['invoice_no'] . " Has been confirmed with Paid amount " . $BookingData['grand_total'] . " Dhs, Thank You, Greenly Team.";
                                //$this->sendSms($sms_message, $get_authenticate->user_phone);

                                $notify_type = 'yallo-recovery';
                                $type = 'Yalla Recovery';
                                $notificationData = array(
                                    ['user_id' => $get_authenticate->user_id,
                                        'type' => $type,
                                        'title' => 'Booking',
                                        'description' => $sms_message,
                                        'status' => 1,
                                        'created_at' => $date,
                                        'updated_at' => $date],
                                );
                                $this->user_model->insertNotification($notificationData);
                                $this->user_model->push_notify($get_authenticate->device_token, $sms_message, $type, $notify_type, $get_authenticate->user_type);

                                foreach ($GarageData as $drivers) {
                                    $notify_type_request = "yallo-recovery-request";
                                    $requestData = array(
                                        "booking_id" => $bookingId,
                                        "request_to" => $drivers['user_id'],
                                        "request_type" => $status_Data['service_type'],
                                        "status" => "request",
                                    );
                                    $this->user_model->insertBookingRequest($requestData);
                                    $msg = 'A New Yalla Recovery is available.';
                                    $notificationData = array(
                                        ['user_id' => $drivers['user_id'],
                                            'type' => $type,
                                            'title' => 'Booking',
                                            'description' => $msg,
                                            'status' => 1,
                                            'created_at' => $date,
                                            'updated_at' => $date],
                                    );
                                    $this->user_model->insertNotification($notificationData);
                                    $this->user_model->push_notify($drivers['device_token'], $msg, $type, $notify_type_request, $drivers['user_type']);
                                }
                                if ($BookingData['is_paid'] == 1 && $BookingData['payment_method'] == 'Card') {
                                    $paymentData = array(
                                        'booking_id' => $bookingId,
                                        'payment_id' => $status_Data['payment_id'],
                                        'invoice_id' => $status_Data['invoice_id'],
                                        'transaction_id' => $status_Data['transaction_id'],
                                        'transaction_date' => $status_Data['transaction_date'],
                                        'transaction_status' => $status_Data['transaction_status'],
                                        'paid_amount' => $status_Data['grand_total'],
                                        'paid_by' => $token,
                                        'payment_status' => 1,
                                        'created_at' => $date,
                                        'updated_at' => $date,
                                    );
                                    $this->user_model->insertPayment($paymentData);

                                } else {
                                    $getSubscriptionByuserID = $this->user_model->getSubscriptionByuserID($token);
                                    $updateSubscriptionData = array(
                                        'used_service_count' => $getSubscriptionByuserID->used_service_count + 1,
                                    );
                                    $this->user_model->updateSubscription($updateSubscriptionData, $token);
                                }
                                $sms_message = "Hi this is Ordergreenly.com, Thank you for Booking Yalla Recovery Service. this is your order token - " . $BookingData['grand_total'];
                                $subject = 'Order Greenly Yalla Recovery Booking Invoice - ' . $BookingData['grand_total'];
                                $filename = $bookingId . time() . '.pdf';
                                $content['userName'] = $get_authenticate->user_first_name;
                                $content['content'] = $sms_message;
                                $content['invoice'] = $filename;
                                $updatedData['invoice_path'] = $filename;
                                $invoice = $this->createInvoice($filename, $bookingId);
                                $this->user_model->updateBookingService($bookingId, $updatedData);
                                $this->user_model->emailSendingWithAttach($get_authenticate->user_email, $subject, $content);

                                $response = $this->user_model->getOrderDetailById($bookingId);
                                $response->booked_on = date('d/m/Y', $response->booked_on);
                                $orderDetail['orderDetail'] = $response;

                                echo json_encode(array("statusCode" => 200, "message" => "Garage service Booked Successfully", "data" => $orderDetail));
                            } else {
                                echo json_encode(array("statusCode" => 400, "message" => "Unable to place booking"));
                            }
                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "Drivers not available"));
                        }
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    // End
    public function getGarageServices()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('user_long', 'user_lat', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $ServicesData = $this->user_model->getAllGarage($status_Data['user_long'], $status_Data['user_lat']);
                        $resultArray = [];
                        foreach ($ServicesData as $ServicesKey => $ServicesValue) {
                            $resultArray[$ServicesKey]['garage_id'] = $ServicesValue->garage_id;
                            $resultArray[$ServicesKey]['garage_name'] = $ServicesValue->garage_name;
                            $resultArray[$ServicesKey]['garage_description'] = $ServicesValue->garage_description;
                            $resultArray[$ServicesKey]['garage_logo'] = $ServicesValue->garage_logo;
                            $resultArray[$ServicesKey]['garage_location'] = $ServicesValue->garage_location;
                            $resultArray[$ServicesKey]['garage_lat'] = $ServicesValue->garage_lat;
                            $resultArray[$ServicesKey]['garage_long'] = $ServicesValue->garage_long;
                            $resultArray[$ServicesKey]['garage_status'] = $ServicesValue->garage_status;
                            $resultArray[$ServicesKey]['make_id'] = $ServicesValue->make_id;
                            $resultArray[$ServicesKey]['distance'] = $ServicesValue->distance;
                            $resultArray[$ServicesKey]['garage_sevice'] = $this->user_model->getGarageServicesById($ServicesValue->garage_id);
                        }
                        echo json_encode(array("statusCode" => 200, "message" => "Garage list.", "data" => $resultArray));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function GetGoEnQaZDistance()
    {
        $token = $this->chckToken();
       
        if (trim($token) != null ) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate) ) {
                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('from_lat', 'Latitude', 'trim|required');
                    $this->form_validation->set_rules('to_lat', 'Latitude', 'trim|required');
                    $this->form_validation->set_rules('from_long', 'Latitude', 'trim|required');
                    $this->form_validation->set_rules('to_long', 'Latitude', 'trim|required');

                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $responseData = array();
                        $dist = $this->GetDrivingDistance($status_Data['from_lat'], $status_Data['to_lat'], $status_Data['from_long'], $status_Data['to_long']);
                        
                        $Distance = isset($dist['distance']) && trim($dist['distance']) != null ? str_replace(',', '.', $dist['distance']) : 1;
                        $time = isset($dist['time']) && trim($dist['time']) != null ? str_replace('godz.', 'hr', $dist['time']) : 1;
                        // $Distance_data = preg_replace("/[^0-9]/", '', $Distance);
                        $Distance_data = (float) filter_var($Distance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        $Data['distance'] = $Distance;
                        if ($Distance_data <= 30) {
                            $total_amount = 100;
                        } elseif ($Distance_data > 30) {
                            $total_amount = 100 + (($Distance_data - 30) * 2);
                        }
                        $GarageData = $this->user_model->getNearestWashStation($status_Data['from_lat'], $status_Data['from_long'], 3);
                        $Data['check_drivers'] = (empty($GarageData) ? "Drivers not available" : "Drivers available");
                        $Data['check_driver_status'] = (empty($GarageData) ? 0 : 1);
                        $Data['sub_total_amount'] = $total_amount;
                        $Data['total_amount'] = $this->vatCalulator($total_amount);
                        $Data['vat_amount'] = round($this->vatCalulator($total_amount) - $total_amount, 1);

                        echo json_encode(array("statusCode" => 200, "message" => "Total amount and distance.", "data" => $Data));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=AIzaSyAGvT16evD61Cf59xBBakhdDQSynwyWgSc";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        //var_dump($response_a);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'] ?? '';
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'] ?? '';

        return array('distance' => $dist, 'time' => $time);
    }
    public function privacyPolicy()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                $response = array();
                $rowData = $this->user_model->privacy_policy();

                echo json_encode(array("statusCode" => 200, "data" => $rowData));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function termsConditions()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                $response = array();
                $rowData = $this->user_model->terms_conditions();

                echo json_encode(array("statusCode" => 200, "data" => $rowData));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getProductCategory()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {

                $rowData['category'] = $this->admin_model->getCategoryData();

                echo json_encode(array("statusCode" => 200, "data" => $rowData));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getProductSubCategory()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);

            if (!empty($get_authenticate)) {

                $rowData['sub_category'] = $this->admin_model->getsubCategoryById($status_Data['category_id']);

                echo json_encode(array("statusCode" => 200, "data" => $rowData));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getProduct()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('sub_category_id', 'sub_category_id', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $response = array();
                        $rowData = $this->user_model->getProducctById($status_Data['sub_category_id']);
                        if (!empty($rowData)) {
                            foreach ($rowData as $key => $product) {
                                $rowData[$key]['name'] = $product['name'];
                                $rowData[$key]['description'] = $product['description'];
                                $rowData[$key]['price'] = $product['price'];
                                $rowData[$key]['qty'] = $product['qty'];
                                $rowData[$key]['product_image'] = base_url() . 'uploads/product_image/' . $product['product_image'];
                            }

                        }
                        $response['product'] = $rowData;
                        echo json_encode(array("statusCode" => 200, "data" => $response));
                    }

                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function getServiceType()
    {
        $response = array();
        $rowData = $this->user_model->fetchServiceType();
        foreach ($rowData as $key => $serviceType) {
            $Data[$key]['service_id'] = $serviceType['service_id'];
            $Data[$key]['service_type'] = $serviceType['service_type'];
            $Data[$key]['service_image'] = base_url() . "assets/img/logo_image/" . $serviceType['service_image'];
            $Data[$key]['status'] = $serviceType['status'];
        }
        echo json_encode(array("statusCode" => 200, "data" => $Data));
    }
    public function deleteVehicle()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $status_Data = json_decode(trim(file_get_contents('php://input')), true);
                $response = array();
                $response = $this->user_model->deleteVehicle($status_Data['vehicle_id'], $get_authenticate->user_id);
                echo json_encode(array("statusCode" => 200, "message" => "Vehicle deleted successfully"));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    // public function demo()
    // {
    //     $invoice = $this->createInvoice('demo.pdf', 53);
    // }
    public function orderStatusUpdate()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);
            $date = date("Y-m-d H:i:s");
            if (!empty($get_authenticate)) {
                $status_Data = json_decode(trim(file_get_contents('php://input')), true);
                $response = array();
                $this->user_model->orderStatusUpdate($status_Data['booking_id'], $status_Data['update_status'], $get_authenticate);
                $orderDetail = $this->user_model->getOrderDetailById($status_Data['booking_id']);
                $row_data = $this->user_model->check_data_by_user_id($orderDetail->booked_by);
                $msg = 'Your ' . $orderDetail->service_type . ' status updated.';
                if ($orderDetail->service_type_id == 3 && $status_Data['update_status'] == 1) {
                    $getrequestStatus = $this->user_model->getRequestStatus($status_Data['booking_id']);
                    if ($getrequestStatus) {
                        $msg = 'Your ' . $orderDetail->service_type . ' accepeted.';
                        $this->user_model->updateRequeststatus($status_Data['booking_id']);
                        $locationdata['current_long'] = $status_Data['current_long'];
                        $locationdata['current_lat'] = $status_Data['current_lat'];
                        $locationdata['user_id'] = $token;
                        $locationdata['status'] = 1;
                        $locationdata['created_at'] = $date;
                        $locationdata['updated_at'] = $date;
                        $this->user_model->insertGarageLocation($locationdata);
                    } else {
                        echo json_encode(array("statusCode" => 200, "message" => "This booking request has been expired"));
                        exit;
                    }

                }
                if ($status_Data['update_status'] == 3) {
                    $msg = "Your " . $orderDetail->service_type . " has been completed. ";
                    if ($orderDetail->payment_method == 'Cash' && $orderDetail->service_type_id == 1) {
                        $filename = $status_Data['booking_id'] . time() . '.pdf';
                        $updatedData['invoice_path'] = $filename;
                        $invoice = $this->createInvoice($filename, $status_Data['booking_id']);
                        $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);
                        $sms_message = "Dear Valued Customer, Your Order Number " . $orderDetail->booking_id . " Has been confirmed, Thank You, Greenly Team.";
                        $msg = 'Order Greenly Yalla Wash Booking Invoice - ' . $orderDetail->booking_code;
                        $content['userName'] = $get_authenticate->user_first_name;
                        $content['content'] = $sms_message;
                        $content['invoice'] = $filename;
                        $this->user_model->emailSendingWithAttach($get_authenticate->user_email, $msg, $content);
                    }
                }
                $notify_type = 'order-status-update';
                $type = "Order status";
                $notificationData = array(
                    ['user_id' => $row_data->user_id,
                        'type' => $notify_type,
                        'title' => 'Order Status',
                        'description' => $msg,
                        'status' => 1,
                        'created_at' => $date,
                        'updated_at' => $date],
                );
                $this->user_model->insertNotification($notificationData);
                $this->user_model->push_notify($row_data->device_token, $msg, $type, $notify_type, $row_data->user_type);
                //$this->sendSms($msg, $row_data->user_phone);
                $subject = 'Order Greenly ' . $orderDetail->service_type . " Booking status updated.";
                $content['userName'] = $row_data->user_first_name;
                $content['content'] = $msg;
                $this->user_model->emailSending($row_data->user_email, $subject, $content);

                echo json_encode(array("statusCode" => 200, "message" => "Booking status updated successfully"));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function GetOrderDetails()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $status_Data = json_decode(trim(file_get_contents('php://input')), true);
                $response = array();
                $response = $this->user_model->getOrderDetailById($status_Data['booking_id']);
                $response->booked_on = date('d/m/Y', $response->booked_on);
                echo json_encode(array("statusCode" => 200, "message" => "Order Detail", "data" => $response));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function checkAdminApproval()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_model->user_status_check($token);
            if ($get_authenticate->user_status == null || $get_authenticate->user_status == 0) {
                $status = 0;
            } else {
                $status = 1;
            }
            echo json_encode(array("statusCode" => 200, "data" => ['user_status' => $status]));

        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function removeCart()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $status_Data = json_decode(trim(file_get_contents('php://input')), true);

                echo json_encode(array("statusCode" => 200, "message" => "Product Remove to cart"));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function addToCart()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $get_authenticate = $this->user_authenticate($token);
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);

            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $cartToken = substr(str_shuffle($permitted_chars), 0, 24);
            if (!empty($get_authenticate)) {
                $cartDataExist = $this->user_model->getCartData($get_authenticate->user_id, $status_Data['product_id']);
                $date = date("Y-m-d H:i:s");

                if (!empty($cartDataExist)) {
                    $qty = (integer) $cartDataExist->qty + (integer) $status_Data['qty'];
                    $cartData['qty'] = $qty;
                    $cartData['updated_at'] = $date;
                    $this->user_model->updateCart($get_authenticate->user_id, $status_Data['product_id'], $cartData);
                    echo json_encode(array("statusCode" => 200, "message" => "Update to cart successfully.", "data" => ['cart_token' => $cartDataExist->cart_token]));
                    exit;
                }
                $getCartToken = $this->user_model->getCartToken($get_authenticate->user_id);
                $cartData['user_id'] = $get_authenticate->user_id;
                $cartData['cart_token'] = $getCartToken->cart_token ?? $cartToken;
                $cartData['product_id'] = $status_Data['product_id'];
                $cartData['qty'] = $status_Data['qty'];
                $cartData['status'] = 1;
                $cartData['created_at'] = $date;
                $cartData['updated_at'] = $date;
                $this->user_model->insertCartItems($cartData);
                echo json_encode(array("statusCode" => 200, "message" => "Add to cart successfully.", "data" => ['cart_token' => $cartData['cart_token']]));

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function createFixInvoice()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $this->form_validation->set_data($status_Data);
                    $this->form_validation->set_rules('booking_id', 'booking_id', 'trim|required');
                    if ($this->form_validation->run() == false) {
                        echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                    } else {
                        $date = date("Y-m-d H:i:s");
                        $invoiceData = $this->user_model->getInvoiceById($status_Data['booking_id']);
                        $responseData = array();
                        if (!empty($invoiceData)) {
                            if ($status_Data['type'] == 1) {
                                $this->createInvoiceItems($status_Data);
                                $QuotationData = $this->user_model->getInvoiceById($status_Data['booking_id']);
                                $total_amount = array_sum(array_column($QuotationData, 'total_amount'));
                                $updatedData['total_amount'] = $this->vatCalulator($total_amount);
                                $updatedData['vat_amount'] = round($updatedData['total_amount'] - $total_amount, 1);
                                $updatedData['booking_status'] = 'pending';
                                $updatedData['payment_method'] = null;
                                $updatedData['is_paid'] = 0;
                                $updatedData['is_invoice'] = 1;
                                $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);
                                $notify_type = 'invoice-add-update';
                                $message = "Your Yalla Fix Invoice Generated";
                                $this->quotationNotification($status_Data, $notify_type, $message);
                                echo json_encode(array("statusCode" => 200, "message" => "Invoice created successfully"));
                            } else {
                                $message = "Yalla Fix Quotation generated";
                                if ($invoiceData[0]['status'] == 'reject') {
                                    $status_Data['status'] = 'initiate';
                                }
                                $this->user_model->removeOldQuation($status_Data);
                                $this->createQuotation($status_Data);
                                $QuotationData = $this->user_model->getInvoiceById($status_Data['booking_id']);
                                $updatedData['total_amount'] = $this->vatCalulator(array_sum(array_column($QuotationData, 'total_amount')));
                                echo json_encode(array("statusCode" => 200, "message" => "Quotation updated successfully"));
                                $notify_type = 'quotation-add-update';
                                $this->quotationNotification($status_Data, $notify_type, $message);

                            }
                        } else {
                            $this->createQuotation($status_Data);
                            $QuotationData = $this->user_model->getInvoiceById($status_Data['booking_id']);
                            $updatedData['total_amount'] = $this->vatCalulator(array_sum(array_column($QuotationData, 'total_amount')));
                            $notify_type = 'quotation-add-update';
                            $message = "Your Yalla Fix Quotation generated.";
                            $this->quotationNotification($status_Data, $notify_type, $message);
                            echo json_encode(array("statusCode" => 200, "message" => "Quotation added successfully"));

                        }
                        $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);

                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    private function quotationNotification($status_Data, $notify_type, $msg)
    {
        $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
        $row_data = $this->user_model->check_data_by_user_id($bookingData->booked_by);
        $date = date("Y-m-d H:i:s");
        $type = "Quotation status";
        $notificationData = array(
            ['user_id' => $row_data->user_id,
                'type' => $notify_type,
                'title' => 'Order Status',
                'description' => $msg,
                'status' => 1,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        );
        $this->user_model->insertNotification($notificationData);
        if ($status_Data['type'] == 2) {
            $quotation_sms = "We kindly request you to submit your quotation for (Order Token:" . $bookingData->booking_code . ") Thank you.";
        } else {
            $quotation_sms = "We kindly request you to submit your invoice for (Order Token:" . $bookingData->booking_code . ") Thank you.";
        }
        //$this->sendSms($quotation_sms, $row_data->user_phone);
        $this->user_model->push_notify($row_data->device_token, $msg, $type, $notify_type, $row_data->user_type);
    }
    private function createQuotation($status_Data)
    {
        $date = date("Y-m-d H:i:s");
        foreach ($status_Data['service_details'] as $service_details) {
            $insertInvoiceData['booking_id'] = $status_Data['booking_id'];
            $insertInvoiceData['service_name'] = $service_details['service_name'];
            $insertInvoiceData['amount'] = $service_details['amount'];
            $insertInvoiceData['quantity'] = $service_details['quantity'] ?? 0;
            $insertInvoiceData['total_amount'] = $service_details['quantity'] * $service_details['amount'] ?? 0;
            $insertInvoiceData['type'] = $service_details['type'];
            $insertInvoiceData['created_at'] = $date;
            $insertInvoiceData['updated_at'] = $date;
            $insertInvoiceData['status'] = 'initiate';
            $this->user_model->insertInvoiceItems($insertInvoiceData);
        }
    }
    private function createInvoiceItems($status_Data)
    {
        $date = date("Y-m-d H:i:s");
        foreach ($status_Data['service_details'] as $service_details) {
            $insertInvoiceData['booking_id'] = $status_Data['booking_id'];
            $insertInvoiceData['service_name'] = $service_details['service_name'];
            $insertInvoiceData['amount'] = $service_details['amount'];
            $insertInvoiceData['quantity'] = $service_details['quantity'] ?? 0;
            $insertInvoiceData['total_amount'] = $service_details['quantity'] * $service_details['amount'] ?? 0;
            $insertInvoiceData['type'] = $service_details['type'];
            $insertInvoiceData['created_at'] = $date;
            $insertInvoiceData['updated_at'] = $date;
            $insertInvoiceData['status'] = 'completed';
            if ($service_details['type'] == 1) {
                $this->user_model->insertInvoiceItems($insertInvoiceData);
            }
        }
    }

    public function updateQuotation()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $invoiceData = $this->user_model->getInvoiceById($status_Data['booking_id']);
                    if (!empty($invoiceData)) {
                        $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
                        $row_data = $this->user_model->check_data_by_user_id($bookingData->booked_by);
                        $date = date("Y-m-d H:i:s");
                        $data['status'] = $status_Data['status'];
                        if ($status_Data['status'] == 'accept') {
                            $updatedData['payment_method'] = $status_Data['payment_type'];
                            $updatedData['is_paid'] = 0; //50 % payment
                            if ($status_Data['payment_type'] == 'Card') {
                                $QuotationData = $this->user_model->getInvoiceById($status_Data['booking_id']);
                                $total_amount = array_sum(array_column($QuotationData, 'total_amount'));
                                $updatedData['total_amount'] = $this->vatCalulator($total_amount);
                                $updatedData['vat_amount'] = round($updatedData['total_amount'] - $total_amount, 1);
                                $updatedData['invoice_path'] = $status_Data['booking_id'] . time() . '.pdf';
                                $updatedData['paid_amount'] = $status_Data['payment_amount'];
                                $updatedData['grand_total'] = round($status_Data['total_amount'] + $updatedData['vat_amount'], 1);
                                $updatedData['is_paid'] = 2; //50 % payment
                                $updatedData['booking_status'] = 'approved';
                                $invoice = $this->createInvoice($updatedData['invoice_path'], $status_Data['booking_id']);
                                $paymentData = array(
                                    'booking_id' => $status_Data['booking_id'],
                                    'payment_id' => $status_Data['payment_id'],
                                    'invoice_id' => $status_Data['invoice_id'],
                                    'transaction_id' => $status_Data['transaction_id'],
                                    'transaction_date' => $status_Data['transaction_date'],
                                    'transaction_status' => $status_Data['transaction_status'],
                                    'paid_amount' => $status_Data['payment_amount'],
                                    'paid_by' => $token,
                                    'payment_status' => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                );
                                $this->user_model->insertPayment($paymentData);
                                $sms_message = "Your decision has been successfully submitted, Thanks you.";
                                $provider_message = "Order Greenly Yalla Fix order Quotation accepted.";
                                $subject = 'Order Greenly Yalla Fix Booking Invoice - ' . $bookingData->booking_code;
                                $content['userName'] = $row_data->user_first_name;
                                $content['content'] = $sms_message;
                                $content['invoice'] = $updatedData['invoice_path'];
                                //$this->sendSms($sms_message, $row_data->user_phone);
                                //$this->sendSms($provider_message, $get_authenticate->user_phone);
                                $this->user_model->emailSendingWithAttach($row_data->user_email, $subject, $content);
                            }
                            $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);
                        }
                        $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
                        if ($status_Data['status'] == 'accept' || $status_Data['status'] == 'reject') {
                            $row_data = $this->user_model->check_data_by_user_id($bookingData->garage_id);
                        } else {
                            $row_data = $this->user_model->check_data_by_user_id($bookingData->booked_by);
                        }
                        $msg = "Your Quotation status- " . $status_Data['status'];
                        $notify_type = 'quotation-status-update';
                        $type = "Quotation status";
                        $notificationData = array(
                            ['user_id' => $row_data->user_id,
                                'type' => $notify_type,
                                'title' => 'Quotation Status',
                                'description' => $msg,
                                'status' => 1,
                                'created_at' => $date,
                                'updated_at' => $date],
                        );
                        $this->user_model->insertNotification($notificationData);
                        $this->user_model->push_notify($row_data->device_token, $msg, $type, $notify_type, $row_data->user_type);
                        $this->user_model->updateInvoice($status_Data['booking_id'], $data);
                        echo json_encode(array("statusCode" => 200, "message" => "Quotation status updated successfully"));

                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "Booking data invalid"));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function updateQuotationProvider()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
                    if ($status_Data['type'] == 2) {
                        $updatedData['invoice_path'] = $status_Data['booking_id'] . time() . '.pdf';
                        $updatedData['paid_amount'] = $status_Data['payment_amount'];
                        $updatedData['is_paid'] = 2; //50 % payment
                        $updatedData['booking_status'] = 'approved';
                        $this->createInvoice($updatedData['invoice_path'], $status_Data['booking_id']);
                        $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);
                        $row_data = $this->user_model->check_data_by_user_id($bookingData->booked_by);
                        $date = date("Y-m-d H:i:s");
                        $msg = "Dear Valued Customer, Your Quotation cash payment completed, Thank You, Greenly Team.";
                        $notify_type = 'quotation-status-update';
                        $type = "Quotation status";
                        $notificationData = array(
                            [
                                'user_id' => $row_data->user_id,
                                'type' => $notify_type,
                                'title' => 'Quotation Status',
                                'description' => $msg,
                                'status' => 1,
                                'created_at' => $date,
                                'updated_at' => $date,
                            ],
                        );
                        $this->user_model->insertNotification($notificationData);
                        $this->user_model->push_notify($row_data->device_token, $msg, $type, $notify_type, $row_data->user_type);

                        echo json_encode(array("statusCode" => 200, "message" => "Quotation Payment updated successfully"));
                    } else {
                        $updatedData['invoice_path'] = $status_Data['booking_id'] . time() . '.pdf';
                        $updatedData['paid_amount'] = $bookingData->paid_amount + $status_Data['payment_amount'];
                        $updatedData['is_paid'] = 1; //50 % payment
                        if ($updatedData['paid_amount'] == $bookingData->total_amount) {
                            $updatedData['booking_status'] = 'completed';
                        }
                        $this->createInvoice($updatedData['invoice_path'], $status_Data['booking_id']);
                        $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);
                        echo json_encode(array("statusCode" => 200, "message" => "Invoice Payment updated successfully"));

                    }

                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function makeInvoicePayment()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $filename = $status_Data['booking_id'] . time() . '.pdf';
                    if ($status_Data['payment_type'] == 'Card') {
                        $filename = $status_Data['booking_id'] . time() . '.pdf';
                        $date = date("Y-m-d H:i:s");
                        $data['booking_id'] = $status_Data['booking_id'];
                        $data['payment_id'] = $status_Data['payment_id'];
                        $data['invoice_id'] = $status_Data['invoice_id'];
                        $data['transaction_id'] = $status_Data['transaction_id'];
                        $data['transaction_date'] = $status_Data['transaction_date'];
                        $data['transaction_status'] = $status_Data['transaction_status'];
                        $data['paid_amount'] = $status_Data['payment_amount'];
                        $data['paid_by'] = $token;
                        $data['payment_invoice'] = $filename;
                        $data['created_at'] = $date;
                        $data['updated_at'] = $date;
                        $updatedData['is_paid'] = 1;
                        $updatedData['booking_status'] = 'completed';
                        $updatedData['invoice_path'] = $filename;
                        $this->createInvoice($filename, $status_Data['booking_id']);
                        $this->user_model->insertPayment($data);
                        $msg = "Dear Valued Customer, Your Invoice cash payment completed, Thank You, Greenly Team.";
                        $subject = 'Order Greenly Your Invoice cash payment completed';
                        $content['userName'] = $get_authenticate->user_first_name;
                        $content['content'] = $msg;
                        $content['invoice'] = $filename;
                        $this->user_model->emailSendingWithAttach($get_authenticate->user_email, $subject, $content);
                    } else {
                        $updatedData['invoice_path'] = $filename;
                        $this->createInvoice($filename, $status_Data['booking_id']);
                        $updatedData['payment_method'] = $status_Data['payment_type'];
                        $msg = "Your invoice status updated. Kindly make cash payment to complete your order";
                    }
                    $this->user_model->updateBookingService($status_Data['booking_id'], $updatedData);
                    //$this->sendSms($msg, $get_authenticate->user_phone);
                    $bookingData = $this->user_model->booked_id($status_Data['booking_id']);
                    $row_data = $this->user_model->check_data_by_user_id($bookingData->booked_by);
                    $date = date("Y-m-d H:i:s");
                    $notify_type = 'invoice-status-update';
                    $type = "Invoice status";
                    $notificationData = array(
                        ['user_id' => $row_data->user_id,
                            'type' => $notify_type,
                            'title' => 'Invoice Status',
                            'description' => $msg,
                            'status' => 1,
                            'created_at' => $date,
                            'updated_at' => $date,
                        ],
                    );
                    $this->user_model->insertNotification($notificationData);
                    $this->user_model->push_notify($row_data->device_token, $msg, $type, $notify_type, $row_data->user_type);

                    echo json_encode(array("statusCode" => 200, "message" => "Invoice Payment updated successfully"));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getNotification()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $getNotificationByID = $this->user_model->getNotificationByID($get_authenticate->user_id);

                if (!empty($getNotificationByID)) {

                    echo json_encode(array("statusCode" => 200, "data" => $getNotificationByID));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Notification not available"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getNotificationCount()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $getNotificationCount['count'] = $this->user_model->getNotificationCount($get_authenticate->user_id);

                if (!empty($getNotificationCount)) {

                    echo json_encode(array("statusCode" => 200, "data" => $getNotificationCount));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Notification not available"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function updateNotificationStatus()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $this->user_model->updateNotification($status_Data['notification_id'], $token);
                echo json_encode(array("statusCode" => 200, "message" => "Notification status updated"));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function updateAllNotificationStatus()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $this->user_model->updateAllNotification($token);
                echo json_encode(array("statusCode" => 200, "message" => "All Notification status updated"));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getInvoice()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $getOrderDetailViewById = $this->user_model->getOrderDetailById($status_Data['booking_id']);
 
                    if (!empty($getOrderDetailViewById)) {
                        if ($getOrderDetailViewById->invoice_path != null) {
                            $data['invoice_path'] = base_url() . 'uploads/invoices/' . $getOrderDetailViewById->invoice_path;
                            echo json_encode(array("statusCode" => 200, "data" => $data));
                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "Invoice not available."));
                        }

                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "Booking record not available"));
                    }

                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function viewQuotation()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $getQuotationDetails = $this->user_model->getQuotationDetails($status_Data['booking_id']);
                    $getOrderDetailViewById = $this->user_model->getOrderDetailById($status_Data['booking_id']);

                    if (!empty($getQuotationDetails)) {
                        $total_amount = $this->vatCalulator(array_sum(array_column($getQuotationDetails, "total_amount")));
                        $response['quotation_details'] = $getQuotationDetails;
                        $response['total_amount'] = $total_amount;
                        $response['half_amount'] = $total_amount / 2;
                        $response['paid_amount'] = $getOrderDetailViewById->paid_amount;
                        echo json_encode(array("statusCode" => 200, "data" => $response));
                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "Quotation     not available"));
                    }

                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function demo()
    {
         $invoice = $this->createInvoice('demo.pdf', 65);
    }
    private function createInvoice($filename, $booking_id, $type = "booking")
    {
        if ($type == 'booking') {
            $html = $this->user_model->getOrderDetailViewById($booking_id);
        } else {
            $html = $this->user_model->getSubscriptionInvoice($booking_id);
        }
        
        // print_r($html); die();
        $this->load->library('pdf');
        $this->dompdf->load_html($html);
        $this->dompdf->set_option('enable_html5_parser', true);
        $this->dompdf->set_option('isRemoteEnabled', true);
        $this->dompdf->set_option('isHtml5ParserEnabled', true);
        $this->dompdf->render();
        $pdf_string = $this->dompdf->output();
        file_put_contents('./uploads/invoices/' . $filename, $pdf_string);
    }
    public function vatCalulator($amount)
    {
        //The VAT rate / percentage.
        $vat = 5;
        //Calculate how much VAT needs to be paid.
        $vatToPay = ($amount / 100) * $vat;

        //The total price, including VAT.
        $totalPrice = $amount + $vatToPay;

        //Print out the final price, with VAT added.
        //Format it to two decimal places with number_format.
        return round($totalPrice, 1);
    }
    public function getUserCurrentLocation()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $getUserLocation = $this->user_model->getUserLocation($status_Data['garage_id']);
                    if (!empty($getUserLocation)) {

                        echo json_encode(array("statusCode" => 200, "data" => $getUserLocation));
                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "User not available"));
                    }

                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function updateUserCurrentLocation()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $updateStatus['current_long'] = $status_Data['current_long'];
                    $updateStatus['current_lat'] = $status_Data['current_lat'];
                    $updateStatus['updated_at'] = date("Y-m-d H:i:s");
                    $this->user_model->updateUserLocation($updateStatus, $token);
                    echo json_encode(array("statusCode" => 200, "message" => "Location updated"));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function sendPasswordOTP()
    {
        $status_Data = json_decode(trim(file_get_contents('php://input')), true);
        $get_authenticate = $this->user_model->checkUserByNumber($status_Data['user_phone']);
        if (!empty($get_authenticate)) {
            $verification_code = mt_rand(1000, 9999);
            $sms_message = "Order Greenly, Password reset OTP Code is: " . $verification_code . ", Please Do not share it with anyone, Thank You, Greenly Team.";
            $this->sendSms($sms_message, $status_Data['user_phone']);
            $userData['password_reset_token'] = $verification_code;
            $this->user_model->update_user_by_id($get_authenticate->user_id, $userData);
            echo json_encode(array("statusCode" => 200, "message" => "Reset password OTP send to your register Mobile Number.", "data" => $userData));
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
        }
    }
    public function resetPassword()
    {
        $change_password = json_decode(trim(file_get_contents('php://input')), true);
        $get_authenticate = $this->user_model->checkUserByNumber($change_password['user_phone']);
        if (!empty($get_authenticate)) {
            if (!empty($change_password)) {
                $this->form_validation->set_data($change_password);
                $this->form_validation->set_rules('new_password', 'new_password', 'trim|required');
                if ($this->form_validation->run() == false) {
                    echo json_encode(array("statusCode" => 400, "message" => strip_tags(validation_errors())));
                } else {
                    if ($get_authenticate->password_reset_token == $change_password['password_reset_token']) {
                        $changePassword['user_password'] = md5($change_password['new_password']);
                        $changePassword['password_reset_token'] = null;
                        $this->user_model->update_user_by_id($get_authenticate->user_id, $changePassword);
                        echo json_encode(array("statusCode" => 200, "message" => "new Password update successfully."));
                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "Invaild OTP number."));

                    }
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
        }

    }
    public function getSubscription()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);

            if (!empty($get_authenticate)) {
                $getSubscriptionByuserID = $this->user_model->getSubscriptionByuserID($token);
                if (!empty($getSubscriptionByuserID)) {
                    $getSubscriptionByuserID->is_subscribed = 1;
                    echo json_encode(array("statusCode" => 200, "data" => $getSubscriptionByuserID));
                } else {
                    $getSubscription = $this->user_model->getSubscription();
                    $getSubscription->is_subscribed = 0;
                    echo json_encode(array("statusCode" => 200, "data" => $getSubscription));
                }

            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function makeSubscription()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $date = date("Y-m-d H:i:s");
                    $filename = $status_Data['invoice_id'] . time() . '.pdf';
                    $subscription_end = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($date)));
                    $paymentData = array(
                        'payment_id' => $status_Data['payment_id'],
                        'invoice_id' => $status_Data['invoice_id'],
                        'transaction_id' => $status_Data['transaction_id'],
                        'transaction_date' => $status_Data['transaction_date'],
                        'transaction_status' => $status_Data['transaction_status'],
                        'paid_amount' => $status_Data['subscription_amount'],
                        'paid_by' => $token,
                        'payment_invoice' => $filename,
                        'payment_status' => 1,
                        'created_at' => $date,
                        'updated_at' => $date,
                    );
                    $payment_id = $this->user_model->insertPayment($paymentData);
                    $insertData['subscription_id'] = $status_Data['subscription_id'];
                    $insertData['user_id'] = $token;
                    $insertData['payment_id'] = $payment_id;
                    $insertData['total_service_count'] = 3;
                    $insertData['used_service_count'] = 0;
                    $insertData['subscription_end'] = $subscription_end;
                    $insertData['created_at'] = $date;
                    $insertData['updated_at'] = $date;
                    $subscription_id = $this->user_model->insertSubscription($insertData);
                    $invoice = $this->createInvoice($filename, $subscription_id, 'subscription');
                    $sms_message = 'Thank you for subscription. Totally 3 free Yalla Recovery added your account.';
                    $content['userName'] = $get_authenticate->user_first_name;
                    $content['content'] = $sms_message;
                    $content['invoice'] = $filename;
                    $this->user_model->emailSendingWithAttach($get_authenticate->user_email, $sms_message, $content);
                    //$this->sendSms($sms_message, $get_authenticate->user_phone);

                    echo json_encode(array("statusCode" => 200, "message" => "Subscription created successfully."));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }

    public function applyPromo()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $date = date("Y-m-d H:i:s");
                    $getPromoStatus = $this->user_model->getPromoCode($status_Data, 'status');
                    if (!empty($getPromoStatus)) {
                        $getPromoSevice = $this->user_model->getPromoCode($status_Data, 'service');
                        if (!empty($getPromoSevice)) {
                            $getPromoCode = $this->user_model->getPromoCode($status_Data, 'date');
                            if (!empty($getPromoCode)) {
                                $checkPromoByuserId = $this->user_model->checkPromoByUserId($getPromoCode->id, $token);
                                if (empty($checkPromoByuserId)) {
                                    $response['discount_amount'] = round(($getPromoCode->percentage / 100) * $status_Data['total_amount'], 1);
                                    $response['percentage'] = $getPromoCode->percentage;
                                    $response['promo_id'] = $getPromoCode->id;
                                    $response['after_discount_amount'] = $status_Data['total_amount'] - $response['discount_amount'];

                                    echo json_encode(array("statusCode" => 200, "message" => "Promo Code applied successfully.", "data" => $response));
                                } else {
                                    echo json_encode(array("statusCode" => 400, "message" => "This Promo Code is already used."));

                                }
                            } else {
                                echo json_encode(array("statusCode" => 400, "message" => "This Promo Code is expired."));

                            }

                        } else {
                            echo json_encode(array("statusCode" => 400, "message" => "This Promo Code not applicable in this Service."));
                        }

                    } else {
                        echo json_encode(array("statusCode" => 400, "message" => "This Promo Code not available."));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function getTermsPolicy()
    {
        $status_Data = json_decode(trim(file_get_contents('php://input')), true);
        if (!empty($status_Data)) {
            $getTermsPolicyByType = $this->user_model->getTermsPolicyByType($status_Data);
            if (!empty($getTermsPolicyByType)) {
                echo json_encode(array("statusCode" => 200, "data" => $getTermsPolicyByType));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Empty Data."));
        }

    }
    public function getLanguage(Type $var = null)
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
        $status_Data = json_decode(trim(file_get_contents('php://input')), true);
        if (!empty($status_Data)) {
                echo json_encode(array("statusCode" => 200, "message" => "Language listed successfully.", "data" => ["user_language" => $languageDetails->user_language]));
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
    public function updateLanguage()
    {
        $token = $this->chckToken();
        if (trim($token) != null) {
            $status_Data = json_decode(trim(file_get_contents('php://input')), true);
            $get_authenticate = $this->user_authenticate($token);
            if (!empty($get_authenticate)) {
                if (!empty($status_Data)) {
                    $Data = array(
                        'user_language' => $status_Data['user_language']
                    );
                    $this->user_model->update_user_by_id($get_authenticate->user_id, $Data);
                    $updatedData = $this->user_authenticate($token);

                    echo json_encode(array("statusCode" => 200, "message" => "Language Updated.", "data" => $updatedData));
                } else {
                    echo json_encode(array("statusCode" => 400, "message" => "Empty Data"));
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Not Authenticate."));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "message" => "Invalid Token."));
        }
    }
}
