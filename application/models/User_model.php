<?php

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_authenticate($token)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id', $token);
        $this->db->where('user_status', 1);
        $query = $this->db->get();
        return $query->row();
    }
    public function user_status_check($token)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id', $token);
        $query = $this->db->get();
        return $query->row();
    }
    public function insert_user_data($data)
    {
        $this->db->insert('user_master', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function insert_garage_data($data)
    {
        $this->db->insert('garage_data', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function insertCartItems($data)
    {
        $this->db->insert('cart_master', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function getCartData($user_id, $product_id)
    {
        $this->db->select('*');
        $this->db->from('cart_master');
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function getCartToken($user_id)
    {
        $this->db->select('cart_token');
        $this->db->from('cart_master');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function updateCart($user_id, $product_id, $data)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);
        $query = $this->db->update('cart_master', $data);
    }
    public function updateBookingService($booking_id, $data)
    {
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->update('booking_master', $data);
    }
    public function user_verify($user_id, $varification_code)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id', $user_id);
        $this->db->where('verification_code', $varification_code);
        $query = $this->db->get();
        return $query->row();
    }
    public function update_user_by_id($user_id, $data)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->update('user_master', $data);
    }
    public function update_garage_by_id($user_id, $data)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->update('garage_data', $data);
    }
    public function check_data_by_user_id($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id', $user_id);
        // $this->db->where('user_status', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function userLogin($user_phone, $user_password, $loginType)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        // $this->db->where('is_verified', 1);
        if ($loginType == 1) {
            $this->db->where('provider_key', $user_phone);
        } else {
            $this->db->where('user_phone', $user_phone);
        }
        $this->db->where('user_password', $user_password);
        $query = $this->db->get();
        return $query->row();
    }

    public function old_password_authenticate($token, $oldPassword)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id', $token);
        $this->db->where('user_status', 1);
        $query = $this->db->get();
        $userData = $query->row();

        if ($userData) {
            $pass = trim($userData->user_password);
            if (md5($oldPassword) == $pass) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function push_notify($token, $msg, $type, $notify_type, $user_type)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $key = 'AAAAibXqB3Y:APA91bHUySaamrkuY6Y9X-mcsR5iJvMzbbqhnp2TBTSAwP-NuVnVnbSzTCGcQEmU6DbDTEEyGDRslYoZCCR-ggrkCQeEUrTam2hsoyY40LZ3qDBrfJInOIsLQLrm-ZrW8vTNhGOlpAIA';
        $notification = array(
            'body' => $msg,
            'title' => ucFirst($type),
        );
        $msg = array
            (
            'type' => $notify_type,
            'user_type' => $user_type,
        );

        $fields = array
            (
            'to' => $token,
            'data' => $msg,
            'notification' => $notification,
        );

        $headers = array
            (
            'Authorization: key=' . $key,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, true));
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
    public function fetchModel()
    {
        $this->db->select('*');
        $this->db->from('model_master');
        $this->db->where('model_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function fetchMake()
    {
        $this->db->select('*');
        $this->db->from('make_master');
        $this->db->where('make_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function fetchType()
    {
        $this->db->select('*');
        $this->db->from('type_master');
        $this->db->where('type_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function fetchServiceType()
    {
        $this->db->select('*');
        $this->db->from('service_type');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function vehicleIsSet($data)
    {
        $this->db->where('vehicle_user_id', $data['vehicle_user_id']);
        $this->db->where('vehicle_id', $data['vehicle_id']);
        $query = $this->db->update('vehicle_master', ['is_default' => 1]);

        $this->db->where('vehicle_user_id', $data['vehicle_user_id']);
        $this->db->where('vehicle_id !=', $data['vehicle_id']);
        $query = $this->db->update('vehicle_master', ['is_default' => 0]);

    }
    public function deleteVehicle($vehicle_id, $vehicle_user_id)
    {
        $this->db->where('vehicle_id', $vehicle_id);
        $this->db->where('vehicle_user_id', $vehicle_user_id);
        return $this->db->delete('vehicle_master');
    }
    public function getModelById($make_id)
    {
        $responseData = array();
        $this->db->select('*');
        $this->db->from('model_master');
        $this->db->join('make_master', 'model_master.make_id=make_master.make_id');
        $this->db->where('model_master.make_id', $make_id);
        $this->db->where('model_master.model_status', 1);
        $this->db->where('make_master.make_status', 1);
        $query = $this->db->get();
        $result = $query->result_array();
        if (!empty($result)) {
            foreach ($result as $modelKey => $modelValue) {
                $responseData[$modelKey]['model_id'] = $modelValue['model_id'];
                $responseData[$modelKey]['make_id'] = $modelValue['make_id'];
                $responseData[$modelKey]['model'] = $modelValue['model'];
                $responseData[$modelKey]['make'] = $modelValue['make'];
                $responseData[$modelKey]['model_status'] = $modelValue['model_status'];
                $responseData[$modelKey]['make_status'] = $modelValue['make_status'];

            }
        }
        return $responseData;
    }

    public function insertVehicle($vehicle_data)
    {
        $this->db->insert('vehicle_master', $vehicle_data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function insertNotification($notificationData)
    {
        $this->db->insert_batch('notifications', $notificationData);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function getNotificationByID($user_id)
    {
        $date = date_create(date('Y-m-d'));
        date_sub($date, date_interval_create_from_date_string("3 days"));
        $start_date = date('Y-m-d H:i:s');
        $expiry_date = date_format($date, "Y-m-d H:i:s");

        $this->db->select('*');
        $this->db->from('notifications');
        // $this->db->where('updated_at >=', $expiry_date);
        // $this->db->where('updated_at <=', $start_date);
        $this->db->or_where('status', 1);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->result_array();

    }
    public function getNotificationCount($user_id)
    {
        $date = date_create(date('Y-m-d'));
        date_sub($date, date_interval_create_from_date_string("3 days"));
        $start_date = date('Y-m-d H:i:s');
        $expiry_date = date_format($date, "Y-m-d H:i:s");

        $this->db->select('*');
        $this->db->from('notifications');
        // $this->db->where('updated_at >=', $expiry_date);
        // $this->db->where('updated_at <=', $start_date);
        $this->db->or_where('status', 1);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return count($query->result_array());

    }

    public function editVehicle($vehicle_id, $data)
    {
        $this->db->where('vehicle_id', $vehicle_id);
        $query = $this->db->update('vehicle_master', $data);
    }
    public function updateNotification($notification_id, $token)
    {
        $status_data['status'] = 2;
        $this->db->where('id', $notification_id);
        $this->db->where('user_id', $token);
        $query = $this->db->update('notifications', $status_data);
    }
    public function updateAllNotification($token)
    {
        $status_data['status'] = 2;
        $this->db->where('user_id', $token);
        $query = $this->db->update('notifications', $status_data);
    }
    public function fetchVehicleByUserId($vehicle_user_id)
    {
        $this->db->select('*');
        $this->db->from('vehicle_master');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id = vehicle_master.vehicle_make_id');
        $this->db->join('type_master', 'type_master.type_id = vehicle_master.vehicle_type_id');
        $this->db->where('vehicle_user_id', $vehicle_user_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function check_vehicle_status($vehicle_user_id)
    {
        $this->db->select('*');
        $this->db->from('vehicle_master');
        $this->db->where('vehicle_user_id', $vehicle_user_id);
        $query = $this->db->get();
        $query->result_array();
        if (empty($query->result())) {
            return 1;
        }
        return 0;

    }

    public function fetchPlan()
    {
        $this->db->select('*');
        $this->db->from('plan_master');
        $this->db->where('plan_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function insertBookingData($bookingData)
    { 
        $this->db->insert('booking_master', $bookingData);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function insertPayment($paymentData)
    {
        $this->db->insert('manage_payment', $paymentData);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function status_update($booking_id, $token, $status_data)
    {
        $this->db->where('booking_id', $booking_id);
        $this->db->where('booked_by', $token);
        $query = $this->db->update('booking_master', $status_data);
    }

    public function booked_id($booking_id)
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function checkBooking($user_id, $vehicle_id, $service_type)
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_by', $user_id);
        $this->db->where('vehicle_id', $vehicle_id);
        $this->db->where('service_type', $service_type);
        $this->db->where_not_in('booking_master.booking_status', ['completed', 'cancel']);
        $query = $this->db->get();
        return $query->row();
    }
    public function getQuotationStatus($booking_id, $user_type)
    {
        $this->db->select('*');
        $this->db->from('manage_invoice');
        $this->db->where('booking_id', $booking_id);
        $this->db->where('type', 2);
        $query = $this->db->get();
        return $query->row();
    }
    public function removeOldQuation($status_Data)
    {
        $this->db->where('booking_id', $status_Data['booking_id']);
        $this->db->where('status !=', 'accept');
        return $this->db->delete('manage_invoice');
    }
    public function getInvoiceStatus($booking_id)
    {
        $this->db->select('*');
        $this->db->from('manage_invoice');
        $this->db->where('booking_id', $booking_id);
        $this->db->where_in('status', ['completed', 'accept']);
        $this->db->where('type', 1);
        $query = $this->db->get();
        return $query->row();
    }
    public function upcoming_Order($token, $get_authenticate)
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        if ($get_authenticate->user_type == 1) {
            $this->db->where('booking_master.booked_by', $token);
        } else {
            $this->db->where('booking_master.garage_id', $token);
        }
        $this->db->where_not_in('booking_master.booking_status', ['completed', 'cancel']);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function checkVehicle($vehicle_id, $vehicle_user_id)
    {
        $this->db->select('*');
        $this->db->from('vehicle_master');
        $this->db->where('vehicle_id', $vehicle_id);
        $this->db->where('vehicle_user_id', $vehicle_user_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function checkProviderModel($user_id, $vehicle_make_id)
    {
        $where = "user_id = '" . $user_id . "' AND JSON_SEARCH(make_id, 'all', '" . $vehicle_make_id . "%') IS NOT NULL";
        $query = $this->db->query("SELECT * FROM  user_master WHERE $where ");
        return $result = $query->row();
    }
    public function getRequestRecoveryOrders($user_id)
    {
        $this->db->select('*');
        $this->db->from('booking_request');
        $this->db->join('booking_master', 'booking_master.booking_id = booking_request.booking_id');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        $this->db->where('booking_request.request_to', $user_id);
        $this->db->where('booking_request.status', 'request');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function updateRequeststatus($booking_id)
    {
        $this->db->where('booking_id', $booking_id);
        return $this->db->delete('booking_request');
    }
    public function getRequestStatus($booking_id)
    {
        $this->db->select('*');
        $this->db->from('booking_request');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function pendingOrder()
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booking_status', 'open');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function past_Order($token, $get_authenticate)
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        if ($get_authenticate->user_type == 1) {
            $this->db->where('booked_by', $token);
        } else {
            $this->db->where('garage_id', $token);
        }$this->db->where_in('booking_status', ['completed','cancel']);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function fetchOrderById($booking_id)
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function fetchAvailableDriver()
    {
        $query = $this->db->query('select * from user_master where user_type=0 and user_status=1 and is_verified=1 and driver_verified=1 and user_id not in (select booked_to from booking_master master where booking_status="AC")');
        return $query->row();
        // return $this->db->count_all_results();
    }

    public function insertBookingRequest($requestData)
    {
        $this->db->insert('booking_request', $requestData);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function getAvailableDriver($booking_id)
    {
        $query = $this->db->query('select * from user_master where user_type=0 and user_status=1 and  is_verified=1 and driver_verified=1 and user_id not in (select booked_to from booking_master master where booking_status="AC") and user_id not in (select request_to from booking_request where booking_id=' . $booking_id . ')');
        return $query->row();
    }

    public function checkInvitedCode($invitation_code)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('invitation_code ', $invitation_code);
        $this->db->where('user_status', 1);
        $query = $this->db->get();
        return $query->row();
        // return $this->db->count_all_results();
    }

    public function countInvitedCode($invited_by_code)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('invited_by_code', $invited_by_code);
        $this->db->where('is_avail', 0);
        return $this->db->count_all_results();
    }

    public function updateUsersAvail($invited_by_code)
    {
        $this->db->set('is_avail', 1);
        $this->db->where('invited_by_code', $invited_by_code);
        $query = $this->db->update('user_master');
    }

    public function insertReview($data)
    {
        $this->db->insert('review_master', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function fetchReviewByBookingId($booking_id)
    {
        $this->db->select('*');
        $this->db->from('review_master');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function checkPhone($user_phone)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_phone', $user_phone);
        $this->db->where('user_type', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function getAllGarageByMakeId($make_id)
    {
        $this->db->select('*');
        $this->db->from('garage_data');
        $this->db->join('make_master', 'make_master.make_id=garage_data.make_id');
        $this->db->where('garage_data.make_id', $make_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getProducctById($sub_category_id)
    {
        $this->db->select('*');
        $this->db->from('product_master');
        $this->db->where('sub_category_id', $sub_category_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getGarageServicesById($garage_id)
    {
        $this->db->select('*');
        $this->db->from('service_garage');
        $this->db->where('service_garage.garage_id', $garage_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getNearestWashStation($lat, $long, $service_type)
    {
        if ($service_type == 'all') {
            $where = "user_type = '2' AND user_status = '1'";
        } else {
            $where = "user_status = '1' AND user_type = '2' AND JSON_SEARCH(service_type, 'all', '" . $service_type . "%') IS NOT NULL";
        }

        $query = $this->db->query("SELECT *, (3959 * acos ( cos ( radians($lat) ) * cos( radians( `user_lat` ) ) * cos( radians( `user_long` ) - radians($long) ) + sin ( radians($lat) ) * sin( radians( `user_lat` ) ) ) ) AS distance FROM  user_master WHERE $where HAVING distance < 70 ORDER BY distance DESC");
        return $result = $query->result_array();
    }

    public function getAllServicesByCenterId()
    {
        $this->db->select('*');
        $this->db->from('sub_services');
        $this->db->where('service_id', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMappedServicesByUserId($userId)
    {
        $this->db->select('*, service_type.service_type as service_name');
        $this->db->from('service_mapping');
        $this->db->join('service_type', 'service_type.service_id=service_mapping.service_id');
        $this->db->where('service_mapping.user_id',$userId);
        $query = $this->db->get();
        return $query->result_array();

    }
    public function getMappedSubServicesByUserId($userId,$service_id)
    {
        $this->db->select('mapping.service_mapping_id as mapping_id, sub_services.sub_service_id, sub_services.service_name as service_name,sub_services.service_id as service_id,mapping.service_price, sub_services.service_price as default_price');
        $this->db->from('sub_service_mapping as mapping');
        $this->db->join('sub_services', 'sub_services.sub_service_id=mapping.sub_service_id');
        $this->db->where('mapping.user_id',$userId);
        $this->db->where('sub_services.service_id',$service_id);
        $query = $this->db->get();
        // print_r($this->db->last_query()); die();
        return $query->result_array();

    }
    

    public function getGarageByGarageId($garage_id)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_master.user_id', $garage_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function getInvoiceById($booking_id)
    {
        $this->db->select('*');
        $this->db->from('manage_invoice');
        $this->db->where('manage_invoice.booking_id', $booking_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function updateInvoice($booking_id, $data)
    {
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->update('manage_invoice', $data);
    }
    public function updatePayment($booking_id, $data)
    {
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->update('manage_invoice', $data);
    }
    public function insertServiceBooking($bookingData)
    {
        $this->db->insert('service_booking_detail', $bookingData);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function insertInvoiceItems($invoiceData)
    {
        $this->db->insert('manage_invoice', $invoiceData);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function getNearestDriver($lat, $long)
    {
        $query = $this->db->query('select (((acos(sin((' . $lat . '*pi()/180))*sin((user_lat*pi()/180))+cos((' . $lat . '*pi()/180))*cos((user_lat*pi()/180))*cos(((' . $long . '- user_long)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance,user_master.* from user_master  where user_master.user_type=0 and user_master.driver_verified=1  having distance<=10');
        return $result = $query->result_array();
    }
    public function getAllGarage($long, $lat)
    {

        $query = $this->db->query("SELECT *, ( 6371 * acos ( cos ( radians($lat) ) * cos( radians( `garage_lat` ) ) * cos( radians( `garage_long` ) - radians($long) ) + sin ( radians($lat) ) * sin( radians( `garage_lat` ) ) ) ) AS distance FROM garage_data HAVING distance < 15 ORDER BY distance ");
        return $query->result();
    }
    public function orderStatusUpdate($booking_id, $update_status, $get_authenticate)
    {
        if ($update_status == '1') {
            $data['booking_status'] = 'approved';
            $data['garage_id'] = $get_authenticate->user_id;
        } elseif ($update_status == '2') {
            $data['booking_status'] = 'reject';
        } elseif ($update_status == '3') {
            $data['is_paid'] = 1;
            $data['booking_status'] = 'completed';
        }
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->update('booking_master', $data);
    }
    public function invoiceNumber($string)
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->order_by('booking_id', 'desc');
        $query = $this->db->get();
        $order = $query->result_array();
        $orderCount = count($order);
        if ($orderCount == 0) {
            $orderCount = 1;
        } else {
            $orderCount = $orderCount + 1;
        }
        return $this->invoice_num($orderCount, 7, $string);
    }
    public function invoice_num($orderCount, $invoiceLenght = 7, $prefix = null)
    {

        if (is_string($prefix)) {
            return sprintf("%s%s", $prefix, str_pad($orderCount, $invoiceLenght, "0", STR_PAD_LEFT));
        }

        return str_pad($orderCount, $invoiceLenght, "0", STR_PAD_LEFT);
    }

    public function getQuotationDetails($booking_id)
    {
        $this->db->select('*');
        $this->db->from('manage_invoice');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getOrderDetailById($booking_id)
    {
        $this->db->select('user_master.user_first_name,user_master.user_last_name,user_master.user_email,user_master.user_phone,vehicle_master.vehicle_engine,vehicle_master.vehicle_plate_no,model_master.model,make_master.make,service_type.service_type,booking_master.booking_id,booking_master.invoice_no,booking_master.garage_id,booking_master.payment_method,booking_master.total_amount,booking_master.booked_on,booking_master.service_type as service_type_id, booking_master.invoice_path,booking_master.booked_by,booking_master.paid_amount,booking_master.booking_status,booking_master.is_paid,booking_master.booking_code,service_provider.garage_name as company_name,service_provider.user_first_name as service_provider_name,service_provider.user_phone as service_provider_phone,service_provider.user_lat as service_provider_lat,service_provider.user_long as service_provider_long,booking_master.location_lat as user_lat,booking_master.location_long as user_long');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        $this->db->join('user_master', 'user_master.user_id = booking_master.booked_by');
        $this->db->join('user_master as service_provider', 'service_provider.user_id = booking_master.garage_id', 'left outer');
        $this->db->where('booking_master.booking_id', $booking_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function getOrderDetailViewById($booking_id)
    {
        $this->db->select('user_master.user_first_name,user_master.user_last_name,user_master.user_email,user_master.user_phone,vehicle_master.vehicle_engine,vehicle_master.vehicle_plate_no,model_master.model,make_master.make,service_type.service_type,booking_master.booking_id,booking_master.paid_amount,booking_master.invoice_no,booking_master.payment_method,booking_master.vat_amount,booking_master.total_amount,booking_master.booked_on,booking_master.service_type as service_type_id, booking_master.sub_service_id,booking_master.service_mapping_id,booking_master.distance');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        $this->db->join('user_master', 'user_master.user_id = booking_master.booked_by');
        $this->db->where('booking_master.booking_id', $booking_id);
        $query = $this->db->get();
        // print_r($this->db->last_query());
        $invoice = $query->row();
        if ($invoice->service_type_id == 1) {
            $this->db->select('sub_services.service_name as service_name, sub_service_mapping.service_price as service_price');
            $this->db->from('sub_service_mapping');
            $this->db->join('sub_services','sub_services.sub_service_id = sub_service_mapping.sub_service_id');
            $this->db->where_in('sub_service_mapping.service_mapping_id', json_decode($invoice->service_mapping_id));
            $query = $this->db->get();
            $serviceDetails = $query->result_array();
        } elseif ($invoice->service_type_id == 2) {
            $this->db->select('*');
            $this->db->from('manage_invoice');
            $this->db->where('booking_id', $invoice->booking_id);
            $query = $this->db->get();
            $serviceDetails = $query->result_array();
        }
        $html = "";
        $html .= '<style type="text/css">
        body {
        margin: 0 !important;
        padding: 0 !important;
        -webkit-text-size-adjust: 100% !important;
        -ms-text-size-adjust: 100% !important;
        -webkit-font-smoothing: antialiased !important;
        font-family: "Baloo Bhai 2", cursive;
        }
        img {
        border: 0 !important;
        outline: none !important;
        }
        p {
        Margin: 0px !important;
        Padding: 0px !important;
        }
        table {
        border-collapse: collapse;
        mso-table-lspace: 0px;
        mso-table-rspace: 0px;
        }
        td, a, span {
        border-collapse: collapse;
        mso-line-height-rule: exactly;
        }
        a.green_btn {
            background: #34b450;
            text-decoration: none;
            color: #fff;
            padding: 5px 9px;
            display: inline-block;
            text-align: center;
            width: 95%;
            margin: 0 auto;
            font-size: 10px;
            text-transform: uppercase;
        }
        a.blue_btn {
            background: #68c4ea;
            padding: 8px 30px;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            border-radius: 5px;
            font-size: 20px;
        }
        table.table_custom th {
            font-size: 14px !important;
            padding: 5px;
            text-align: center !important;
            color: #29abe2;
        }
        table.table_custom td {
            font-size: 14px;
            text-align: center;
            padding: 5px;
        }
        table.color_table td:first-child {
            background: #35b44f;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
        }
        table.color_table td:nth-child(2) {
            background: #29abe2;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
        }
        table.billing_table td {
            padding: 0px 10px;
            font-size: 12px;
            color: #2d2d2d;
            font-weight: 500;
            text-align: center;
            font-family: "Baloo Bhai 2", cursive;
        }

        table.billing_table {
            border-color: #000;
        }
        table.billing_table.order_table td {
            text-align: left;
            font-size: 10px;
        }
        table.billing_table.order_table td:first-child b {
            width: 55%;
            display: inline-block;
            float: left;
        }
        table.billing_table.order_table td:nth-child(2) b {
            width: 35%;
            display: inline-block;
            float: left;
        }
        table.billing_table.order_table td span {
            display: table;
        }
        table.billing_table tr.nrightbord td {
            border: none;
        }
        tr.bno td {
            border-bottom-color: transparent;
        }
        .ExternalClass * {
        line-height: 100%;
        }
        .em_defaultlink a {
        color: inherit !important;
        text-decoration: none !important;
        }
        span.MsoHyperlink {
        mso-style-priority: 99;
        color: inherit;
        }
        span.MsoHyperlinkFollowed {
        mso-style-priority: 99;
        color: inherit;
        }
        @media only screen and (min-width:481px) and (max-width:699px) {
        .em_main_table {
        width: 100% !important;
        }
        .em_wrapper {
        width: 100% !important;
        }
        .em_hide {
        display: none !important;
        }
        .em_img {
        width: 100% !important;
        height: auto !important;
        }
        .em_h20 {
        height: 20px !important;
        }
        .em_padd {
        padding: 20px 10px !important;
        }
        }
        @media screen and (max-width: 480px) {
        .em_main_table {
        width: 100% !important;
        }
        .em_wrapper {
        width: 100% !important;
        }
        .em_hide {
        display: none !important;
        }
        .em_img {
        width: 100% !important;
        height: auto !important;
        }
        .em_h20 {
        height: 20px !important;
        }
        .em_padd {
        padding: 20px 10px !important;
        }
        .em_text1 {
        font-size: 16px !important;
        line-height: 24px !important;
        }
        u + .em_body .em_full_wrap {
        width: 100% !important;
        width: 100vw !important;
        }
        }
        </style>
        <body class="em_body" style="margin:0px; padding:0px;" bgcolor="#efefef">
        <table class="em_full_wrap" valign="top" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#efefef" align="center">
        <tbody><tr>
        <td valign="top" align="center"><table class="em_main_table" style="width:750px;background-color: #fff;background-size: 120px;background-repeat: no-repeat;background-position: top right;"  cellspacing="0" cellpadding="0" border="0" align="center">
            <tbody>

            <tr>
            <td valign="top" align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                    <tr class="em_padd" valign="top" >
                    <td width="100%" style="padding: 0px;text-align: center;"><img src="/assets/img/logo.png" alt="Logo" width="60" style="float: right;"></td>
                    </tr>
                </tbody></table></td>
            </tr>
            <tr>
                <td style="font-size: 30px; text-align: center; font-weight: 600;padding: 8px 0;">Electronic Invoice Number : ' . $invoice->invoice_no . ' </td>
            </tr>
            <tr>
                <td style="">
                    <div style="width: 60%;margin: 0 auto;font-weight: 600;font-size: 14px;border: 1px solid;padding: 4px 10px;">SERVICE PROVIDER NUMBER :  ' . $invoice->user_first_name . ' </div>
                </td>
            </tr>
            <tr>
                <td class="em_h20" style="font-size:0px; line-height:0px; height:70px;" >&nbsp;</td>
            </tr>

            <tr>
            <td style="" class="em_padd" valign="top"  align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                <tr style="width: 100%;">
                    <td style="font-family:Baloo Bhai 2, cursive;font-size: 16px;color: #000000;text-align: left;" valign="top" width="100%">

                    <table style="font-size: 15px;" width="100%">
                        <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Customer Name :</div>
                            <div style="">' . $invoice->user_first_name . ' ' . $invoice->user_last_name . '</div></td>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">DATE :</div>
                            <div style="">' . $invoice->invoice_no . ' </div>
                        </td>
                        </tr>
                    <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Contact Number :</div>
                            <div style="">' . $invoice->user_phone . ' </div></td>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Car Make :</div>
                            <div style="">' . $invoice->make . ' </div>
                        </td>
                        </tr>
                    <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Email Address :</div>
                            <div style="">' . $invoice->user_email . ' </div></td>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Model :</div>
                            <div style="">' . $invoice->model . ' </div>
                        </td>
                        </tr>
                        <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Order Number :</div>
                            <div style="">' . $invoice->booking_id . ' </div></td>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Year :</div>
                            <div style=""></div>
                        </td>
                        </tr>
                        <tr>
                            <td width="50%" style="padding: 5px 0;">
                                <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Service Type :</div>
                                <div style="">' . $invoice->service_type . '</div></td>
                                <td width="50%" style="padding: 5px 0;">
                                <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Plate Number :</div>
                                <div style="">' . trim($invoice->vehicle_engine) . '/' . trim($invoice->vehicle_plate_no) . '</div>
                            </td>
                        </tr>
                    </table>
                    </td>

                </tr>
                <tr>
                    <td style="font-size:0px; line-height:0px; height:40px;">&nbsp;</td>
                </tr>
                <td style="">
                <tr>
                        <div style="text-align:center; font-weight: 400;font-size: 24px;padding: 4px 10px;">Service Details</div>
                        </td>
                    </tr>
                <tr>
                    <td style="font-family:Open Sans, Arial, sans-serif;font-size:18px;line-height:22px;color: #000000;padding-bottom:12px;" valign="top" align="left">

                    <table class="billing_table" width="100%" border="1px">
                        <tr>
                        <td width="12%" style="padding: 8px 0;"><b>ITEM NO</b></td>
                        <td width="57%" style="padding: 8px 0;"><b>Discription</b></td>
                        <td width="6%" style="padding: 8px 0;"><b>Qty</b></td>
                        <td width="10%" style="padding: 8px 0;"><b>Price</b></td>
                        <td width="15%" style="padding: 8px 0;"><b>Amount</b></td>
                        </tr>
                        ';
        if ($invoice->service_type_id == 1) {
            foreach ($serviceDetails as $key => $serviceDetail) {
                $html .= '
                                <tr style="height: 270px;vertical-align: baseline;">
                                <td>
                                <div>' . ($key + 1) . '</div>
                                </td>
                                <td>
                                <div style="text-align: left;">' . $serviceDetail['service_name'] . '</div>
                                </td>
                                <td>
                                <div>1</div>
                                </td>
                                <td>
                                <div>' . $serviceDetail['service_price'] . '</div>
                                </td>
                                <td>
                                <div>' . $serviceDetail['service_price'] . '</div>
                                </td>
                            </tr>';
            }
        } elseif ($invoice->service_type_id == 2) {
            foreach ($serviceDetails as $key => $serviceDetail) {
                $html .= '
                                <tr style="height: 270px;vertical-align: baseline;">
                                <td>
                                <div>' . ($key + 1) . '</div>
                                </td>
                                <td>
                                <div style="text-align: left;">' . $serviceDetail['service_name'] . '</div>
                                </td>
                                <td>
                                <div>' . $serviceDetail['quantity'] . '</div>
                                </td>
                                <td>
                                <div>' . $serviceDetail['amount'] . '</div>
                                </td>
                                <td>
                                <div>' . $serviceDetail['quantity'] * $serviceDetail['amount'] . '</div>
                                </td>
                            </tr>';
            }
        } elseif ($invoice->service_type_id == 3) {
            $html .= '
                                <tr style="height: 270px;vertical-align: baseline;">
                                <td>
                                <div>1</div>
                                </td>
                                <td colspan="2">
                                <div style="text-align: left;"><b>Totally ' . $invoice->distance . ' distance.</b></div>
                                </td>
                                <td>
                                <div>' . $invoice->total_amount . '</div>
                                </td>
                                <td>
                                <div>' . $invoice->total_amount . '</div>
                                </td>
                            </tr>';
        }
        $html .= '<tr class="nrightbord">
                        <td colspan="3" class="sdf" style="font-weight: 500; text-align: right;font-size: 13px; border-right: 0px; padding: 10px 6px;">
                                <div>SubTotal</div>
                                <div>VAT 5%</div>
                                <div>Total</div>
                        </td>
                        <td colspan="2">
                                <div style="text-align: left;">: ' . $invoice->total_amount . '</div>
                                <div style="text-align: left;">: ' . $invoice->vat_amount . '</div>
                                <div style="text-align: left;">: ' . round(($invoice->total_amount + $invoice->vat_amount),1) . '</div>

                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td class="em_h20" style="font-size:0px; line-height:0px; height:10px;" >&nbsp;</td>
                </tr>
                <tr>
                    <td style="font-family:Baloo Bhai 2, cursive;font-size:11px;line-height:18px;color: #000000;padding-bottom:2px;font-weight: 500; " valign="top" align="center">شكراً لإستخدامكم خدمات أوردر جريـنلـي و نتمني لكم يوماً سعيداً</td>
                </tr>
                <tr>
                    <td style="font-family:Baloo Bhai 2, cursive;font-size:11px;line-height:18px;color: #000000;padding-bottom:12px;font-weight: 500; " valign="top" align="center">Thank you for using Order Greenly Services and we wish you a happy day
                    </td>
                </tr>
                <tr>
                    <td class="em_h20" style="font-size:0px; line-height:0px; height:12px;" >&nbsp;</td>
                </tr>

                </tbody></table></td>
            </tr>

        </tbody></table></td>
        </tr></tbody></table>';
        return $html;

    }
    public function terms_conditions()
    {
        $result = "<h2>Terms of Use</h2>
            <p>These terms and conditions (collectively, the “Agreement”) constitute a legal agreement between you and Green international Technologies & investment Group LLC , a Cairo-based limited liability corporation (the “Company”). In order to use the Service (defined below) and the associated Application (defined below) you must agree to the terms and conditions that are set out below. By using or receiving any services supplied to you by the Company (collectively, the “Service”), or downloading, installing or using any associated application supplied by the Company (collectively, the “Application”), you hereby expressly acknowledge and agree to be bound by the Agreement, and any future amendments and additions to the Agreement as published from time to time at http://www.Order-Green.com /terms or through the Service.</p>
            <p>The Company reserves the right to modify the Agreement or its policies relating to the Service or Application at any time, effective from the time of posting of an updated version of this Agreement at http://www.Order-Green.com/terms or through the Service. You are responsible for regularly reviewing this Agreement. Continued use of the Service or Application after any such changes shall constitute your consent to such changes.</p>
            <p>Order-Green is a car services provider that allows users to request a car service via the mobile app.</p>
            <h3>Key Content-related Terms</h3>
                <ul>
                <li>    “Content” means text, graphics, images, music, software (excluding the Application), audio, video, information or other materials.
            </li>
                <li>    “Company Content” means Content that Company makes available through the Service or Application, including any Content licensed from a third party, but excluding User Content.
            </li>
                <li>    “User” means a person who accesses or uses the Service or Application.
            </li>
                <li>    “User Content” means Content that a User posts, uploads, publishes, submits or transmits to be made available through the Service or Application.
            </li>
                <li>    “Collective Content” means, collectively, Company Content and User Content.​
            </li>
                </ul>
            <h3>Representations and Warranties</h3>
            <p>By using the Application or Service, you expressly represent and warrant that you are legally entitled to enter this Agreement. If you reside in a jurisdiction that restricts the use of the Service because of age, or restricts the ability to enter into agreements such as this one due to age, you must abide by such age limits and you must not use the Application or Service. By using the Application or the Service, you represent and warrant that you have the right, authority and capacity to enter into this Agreement and to abide by the terms and conditions of this Agreement. Your participation in using the Service and/or Application is for your personal use and the use of others that have explicitly authorized you. You may not authorize others to use your user status, and you may not assign or otherwise transfer your user account to any other person or entity. When using the Application or Service you agree to comply with all applicable laws from your home nation, the country, state and city in which you are present while using the Application or Service.​</p>
            <ul>
                <li>     You may only access the Service using authorized means. The Company reserves the right to terminate this Agreement if you use the Service or Application with an incompatible or unauthorized device.
            </li>
                <li>     By using the Application or the Service, you agree that:
            </li>
                <li>     You will only use the Service or Application for lawful purposes; you will not use the Services for sending or storing any unlawful material or for fraudulent purposes.
            </li>
                <li>    You will not use the Service or Application to cause nuisance, annoyance or inconvenience.
            </li>
                <li>     You will not impair the proper operation of the network.
            </li>
                <li>     You will not try to harm the Service or Application in any way whatsoever.
            </li>
                <li>     You will not copy, or distribute the Application or other content without written permission from the Company.
            </li>
                <li>     You will only use the Application and Service for your own use and will not resell it to a third party.
            </li>
                <li>     You will keep secure and confidential your account password or any identification we provide you which allows access to the Service.
            </li>
                <li>     You will provide us with whatever proof of identity we may reasonably request.
            </li>
                <li>     You will only use an access point, 3G or 4G data account (AP) which you are authorized to use.
            </li>
                <li>     License Grant, Restrictions and Copyright Policy
            </li>
            </ul>
            <h3>Licenses Granted by Company to Company Content and User Content</h3>
            <p>Subject to your compliance with the terms and conditions of this Agreement, Company grants you a limited, non-exclusive, non-transferable license:</p>
                (i) To view, download and print any Company Content solely for your personal and non-commercial purposes; and <br>
                (ii) To view any User Content to which you are permitted access solely for your personal and non-commercial purposes. <br>
                (iii) You have no right to sublicense the license rights granted in this Agreement.<br>
            <p>You will not use, copy, adapt, modify, prepare derivative works based upon, distribute, license, sell, transfer, publicly display, publicly perform, transmit, stream, broadcast or otherwise exploit the Service, Application or Collective Content, except as expressly permitted in this Agreement. No licenses or rights of any kind are granted to you by implication or otherwise by Company or its licensors, except for the licenses and rights expressly granted in this section.</p>
            <h3>License Granted by User</h3>
            <p>We may, in our sole discretion, permit Users to post, upload, publish, submit or transmit User Content. By making available any User Content on or through the Service or Application, you hereby grant to Company a worldwide, irrevocable, perpetual, non-exclusive, transferable, royalty- free license, with the right to sublicense, to use, view, copy, adapt, modify, distribute, license, sell, transfer, publicly display, publicly perform, transmit, stream, broadcast and otherwise exploit such User Content only on, through or by means of the Service or Application. Company does not claim any ownership rights in any User Content and nothing in this Agreement will be deemed to restrict any rights that you may have to use and exploit any User Content.</p>
            <p>You acknowledge and agree that you are solely responsible for all User Content that you make available through the Service or Application. Accordingly, you represent and warrant that:</p>
                (i) You either are the sole and exclusive owner of all User Content that you make available through the Service or Application or you have all rights, licenses, consents and releases that are necessary to grant to Company and to the rights in such User Content, as contemplated under this Agreement, <br>
                (ii) Neither the User Content nor your posting, uploading, publication, submission or transmittal of the User Content or Company’s use of the User Content (or any portion thereof) on, through or by means of the Service or Application will infringe, misappropriate or violate a third party’s patent, copyright, trademark, trade secret, moral rights or other intellectual property rights, or rights of publicity or privacy, or result in the violation of any applicable law or regulation.
            <h3>Application License</h3>
            <p>Subject to your compliance with this Agreement, Company grants you a limited non-exclusive, non- transferable license to download and install a copy of the Application on a single mobile device or computer that you own or control and to run such copy of the Application solely for your own personal use. Furthermore, with respect to any Application accessed through or downloaded from the Apple App Store (“App Store Sourced Application”), you will use the App Store Sourced Application only: <p>
                (i) on an Apple-branded product that runs iOS (Apple’s proprietary operating system software); and <br>
                (ii) as permitted by the “Usage Rules” set forth in the Apple App Store Terms of Service. Company reserves all rights in and to the Application not expressly granted to you under this Agreement.
            <p>Accessing and Downloading the Application from iTunes</p>
            <p>The following applies to any App Store Sourced Application:</p>
            <p>You acknowledge and agree that </p>
            (i) this Agreement is concluded between you and Company only, and not Apple, and <br>
            (ii) Company, not Apple, is solely responsible for the App Store Sourced Application and content thereof. Your use of the App Store Sourced Application must comply with the App Store Terms of Service.
            <p>You acknowledge that Apple has no obligation whatsoever to furnish any maintenance or support services with respect to the App Store Sourced Application.</p>
            <p>In the event of any failure of the App Store Sourced Application to conform to any applicable warranty, you may notify Apple, and Apple will refund the purchase price for the App Store Sourced Application to you and to the maximum extent permitted by applicable law, Apple will have no other warranty obligation whatsoever with respect to the App Store Sourced Application. As between Company and Apple, any other claims, losses, liabilities, damages, costs or expenses attributable to any failure to conform to any warranty will be the sole responsibility of Company.</p>
            <p>You and Company acknowledge that, as between Company and Apple, Apple is not responsible for addressing any claims you have or any claims of any third party relating to the App Store Sourced Application or your possession and use of the App Store Sourced Application, including, but not limited to:</p>
                (i) product liability claims;<br>
                (ii) any claim that the App Store Sourced Application fails to confirm to any applicable legal or regulatory requirement; and <br>
                (iii) Claims arising under consumer protection or similar legislation.
                <p>You and Company acknowledge that, in the event of any third party claim that the App Store Sourced Application or your possession and use of that App Store Sourced Application infringes that third party’s intellectual property rights, as between Company and Apple, Company, not Apple, will be solely responsible for the investigation, defense, settlement and discharge of any such intellectual property infringement claim to the extent required by this Agreement.</p>
                <p>You and Company acknowledge and agree that Apple, and Apple’s subsidiaries, are third party beneficiaries of this Agreement as related to your license of the App Store Sourced Application, and that, upon your acceptance of the terms and conditions of this Agreement, Apple will have the right (and will be deemed to have accepted the right) to enforce this Agreement as related to your license of the App Store Sourced Application against you as a third party beneficiary thereof.</p>
                <p>Without limiting any other terms of this Agreement, you must comply with all applicable third party terms of agreement when using the App Store Sourced Application.</p>
            <p>You shall not </p>
                (i) license, sublicense, sell, resell, transfer, assign, distribute or otherwise commercially exploit or make available to any third party the Service or the Application in any way; <br>
                (ii) modify or make derivative works based upon the Service or the Application; <br>
                (iii) create Internet “links” to the Service or “frame” or “mirror” any Application on any other server or wireless or Internet-based device; <br>
                (iv) reverse engineer or access the Application in order to (a) build a competitive product or service, (b) build a product using similar ideas, features, functions or graphics of the Service or Application, or (c) copy any ideas, features, functions or graphics of the Service or Application, or (v) launch an automated program or script, including, but not limited to, web spiders, web crawlers, web robots, web ants, web indexers, bots, viruses or worms, or any program which may make multiple server requests per second, or unduly burdens or hinders the operation and/or performance of the Service or Application.<br>
                <p>You shall not: </p>
                (i) send spam or otherwise duplicative or unsolicited messages in violation of applicable laws; <br>
                (ii) send or store infringing, obscene, threatening, libelous, or otherwise unlawful or tortious material, including material harmful to children or violative of third party privacy rights; <br>
                (iii) send or store material containing software viruses, worms, Trojan horses or other harmful computer code, files, scripts, agents or programs; Interfere with or disrupt the integrity or performance of the Application or Service or the data contained therein; or (v) attempt to gain unauthorized access to the Application or Service or its related systems or networks.<br>
                <p><strong>Green International Technology& Investment Group</strong> will have the right to investigate and prosecute violations of any of the above to the fullest extent of the law. Company may involve and cooperate with law enforcement authorities in prosecuting users who violate this Agreement. You acknowledge that Company has no obligation to monitor your access to or use of the Service, Application or Collective Content or to review or edit any Collective Content, but has the right to do so for the purpose of operating the Service and Application, to ensure your compliance with this Agreement, or to comply with applicable law or the order or requirement of a court, administrative agency or other governmental body. Company reserves the right, at any time and without prior notice, to remove or disable access to any Collective Content that Company, at its sole discretion, considers to be in violation of this Agreement or otherwise harmful to the Service or Application.</p>
            <h3>Copyright Policy​</h3>
            <p><strong>Green International Technology& Investment Group</strong> respects copyright law and expects its users to do the same. It is Company’s policy to terminate in appropriate circumstances Users or other account holders who repeatedly infringe or are believed to be repeatedly infringing the rights of copyright holders.</p>
            <h3>Payment Methods</h3>
            <p>We accept payments online using Visa and MasterCard credit/debit card also Cash in Egyptian Pound </p>
            1. Customer can choose between different payment methods provided on the platforms, which are currently the following: Cash on Delivery, local Or internationa debit cards and credit cards.<br>
            2. Order-Green reserves the right to provide other payment methods or to no longer offer certain payment methods.<br>
            3. Customer can choose the payment method when ordering the service, provided that the customer can choose an online payment method, the payment will be processed by an external online payment provider cooperating with Order-Green.<br>
            4. Cards data will be stored for future orders by the external online payment providers, on the condition that the customer has given consent to the storage and future usage. <br>
            5. Customer is obliged to ensure sufficient cover of the respective account or, when using credit card, to use the credit card only within the card transaction limit. Customer has to refrain from causing unauthorized debit charge backs.<br>
            <h3>Refund Policy:</h3>
            <p>In order to refund any amount please contact Order-Green through our live chat or call us on our hotline number and we will assist you. In appropriate cases, if you have been ordered our services we will issue full refunds. In the following case: if you did not get your service done by our representative, we will do our best to ensure your satisfaction,</p>
            <p>Any refund will be done only through the original mode of payment .​</p>
            <p>The Refunded amount will be minus the transfer’s charges if the mistake not made by the company </p>
            <p>Go EnQaz service in EGYPT will deduct 75 EGP incase of the cancellation made by the customer after ordering the service by 3 minutes.</p>
            <p>Go EnQaz Service in UAE & Gulf area will Deduct 10 Dhs  incase of the cancellation made by the customer after ordering the service by 3 minutes.</p>
            <p>Please read the following terms of use and disclaimers carefully before using Debit / Credit Cards:</p>
            <p>You may cancel the service Request if the time waiting exceeded our  promise time; your paid amount will be refunded back to your account.</p>
            <p>Debit & credit Card activation Fees on Order Green application is 1 EGP OR 1 Dhs or Equivalent in the Gulf area.</p>
            <p>The customer order cancellation is limited to a maximum time of 5 minutes from the time of Requesting the Go wash service only .</p>
            <p>The customer refund procedure might take 3-7 working days to process on the Debit /Credit Cards bank payment gateway. The customer has to follow on with the bank in case of any delay in crediting back the customer’s account with the amount previously paid by the customer. We will send an email to the customer that contains a printout of the refund advice printed from Debit / Credit Cards bank payment gateway as reference in case the customer wants to check with the bank.</p>
            <p>Customers using the Debit / Credit Cards facility are requested to be available on their respective contact numbers.</p>
            <p>Credit or Debit cards used in placing orders through the online payment gateway on ORDER-GREEN website or applications must belong to the user. Otherwise, the user must attain the legal permission from the card owner to perform the transaction.</p>
            <p>The customer is entirely liable for placing a request for the service using the Debit / Credit Cards facility after carefully reading all the terms & conditions.</p>
            <p>The Company, at its sole discretion, makes promotional offers with different features and different rates to any of our customers. These promotional offers, unless made to you, shall have no bearing whatsoever on your offer or any contract with the Company. The Company may change the fees for the Service or Application, as we deem necessary for our business. We encourage you to check back at our website / application periodically if you are interested in the Company’s charges for the Service or Application,</p>
            <p>Open Meter Fare 75 EGP in GO EnQaZ service is fixed no refund for this amount if the customer cancelled the service after 3 minutes  from the time the customer ordered the service </p>
            ​<p>Free monthly EnQaZ service cost 150 EGP  will be automatically deducted and  renewed every 6 month but if customer would like to cancel subscription .</p>
            <p>Free monthly EnQaZ service cost 50 DHS in the gulf area  will be automatically deducted and  renewed every 6 month but if customer would like to cancel subscription .</p>
            <h3> Access To FREE ENQAZ Service </h3>
            <p>We are introducing Free EnQaZ service , Every customer will join the membership of Free EnQaZ service will be entitled to the maximum number of free Two pickup only per month incase of emergencies on road in every Governorate in Egypt or in the gulf area that has available free EnQaZ service .</p>
            <p>The Activation of the Free EnQaZ Membership will take 72 Hours after the payment has been initiated.</p>
            <h3>Intellectual Property Ownership</h3>
            <p>The Company alone (and its licensors, where applicable) shall own all right, title and interest, including all related intellectual property rights, in and to the Application and the Service and any suggestions, ideas, enhancement requests, feedback, recommendations or other information provided by you or any other party relating to the Application or the Service. This Agreement is not a sale and does not convey to you any rights of ownership in or related to the Application or the Service, or any intellectual property rights owned by the Company. The Company name, the Company logo, and the product names associated with the Application and Service are trademarks of the Company or third parties, and no right or license is granted to use them.</p>
            <h3>Third Party Interactions</h3>
            <p>During use of the Application and Service, you may enter into correspondence with, purchase goods and/or services from, or participate in promotions of third party service providers, advertisers or sponsors showing their goods and/or services through the Application or Service. Any such activity, and any terms, conditions, warranties or representations associated with such activity, is solely between you and the applicable third-party. The Order-Green and its licensors shall have no liability, obligation or responsibility for any such correspondence, purchase, transaction or promotion between you and any such third-party. The Order-Green does not endorse any sites on the internet that are linked through the Service or Application, and in no event shall the Order-Green or its licensors be responsible for any content, products, services or other materials on or available from such sites or third party providers. The Company provides the Application and Service to you pursuant to the terms and conditions of this Agreement. You recognize, however, that certain third-party providers of goods and/or services may require your agreement to additional or different terms and conditions prior to your use of or access to such goods or services, and the Company disclaims any and all responsibility or liability arising from such agreements between you and the third party providers.</p>
            <p>The Company may rely on third party advertising and marketing supplied through the Application or Service and other mechanisms to subsidize the Application or Service. By agreeing to these terms and conditions you agree to receive such advertising and marketing. If you do not want to receive such advertising you should notify us in writing. The Company reserves the right to charge you a higher fee for the Service or Application should you choose not to receive these advertising services. This higher fee, if applicable, will be posted on the Company’s website located at http://www.order-green.com . The Company may compile and release information regarding you and your use of the Application or Service on an anonymous basis as part of a customer profile or similar report or analysis. You agree that it is your responsibility to take reasonable precautions in all actions and interactions with any third party you interact with through the Service.</p>
            <h3>Conduct of Users</h3>
            <p>By entering into this Agreement or using the Application or the Service you agree that (1) you will comply with the laws of the Arab republic of Egypt (whichever is applicable). You will be solely responsible for any failure to comply with this provision.</p>
            <h3>Access to Service Site/Vehicles</h3>
            <p>By entering into this Agreement or using the Application or the Service you agree that you will guarantee the access to service site and/or vehicle – be it an individual parking of a villa, apartment parking, community parking, public parking, commercial parking lot, etc. – to Order-Green staff in order to provide the requested service(s).</p>
            <h3>Indemnification</h3>
            <p>By entering into this Agreement and using the Application or Service, you agree that you shall defend, indemnify and hold the Company, its licensors and each such party’s parent organizations, subsidiaries, affiliates, officers, directors, Users, employees, attorneys and agents harmless from and against any and all claims, costs, damages, losses, liabilities and expenses (including attorneys’ fees and costs) arising out of or in connection with: (a) your violation or breach of any term of this Agreement or any applicable law or regulation, whether or not referred to in this Agreement; (b) your violation of any rights of any third party, including any providers of transportation services, or (c) your use or misuse of the Application or Service.</p>
            <h3>Agreement Validity</h3>
            <p>This agreement of service is considered to in force from the date and time, you sign up with the company and remains valid until your customer account is active in the Company’s database. You customer account can be removed due to breach of any contractual clauses in this agreement or as a result of an explicit written notice by either of the two parties.</p>
            <h3>Disclaimer of Warranties</h3>
            <p>THE COMPANY MAKES NO REPRESENTATION, WARRANTY, OR GUARANTY AS TO THE RELIABILITY, TIMELINESS, QUALITY, SUITABILITY, AVAILABILITY, ACCURACY OR COMPLETENESS OF THE SERVICE OR APPLICATION. THE COMPANY DOES NOT REPRESENT OR WARRANT THAT (A) THE USE OF THE SERVICE OR APPLICATION WILL BE SECURE, TIMELY, UNINTERRUPTED OR ERROR-FREE OR OPERATE IN COMBINATION WITH ANY OTHER HARDWARE, APPLICATION, SYSTEM OR DATA, (B) THE SERVICE OR APPLICATION WILL MEET YOUR REQUIREMENTS OR EXPECTATIONS, (C) ANY STORED DATA WILL BE ACCURATE OR RELIABLE, (D) THE QUALITY OF ANY PRODUCTS, SERVICES, INFORMATION, OR OTHER MATERIAL PURCHASED OR OBTAINED BY YOU THROUGH THE SERVICE WILL MEET YOUR REQUIREMENTS OR EXPECTATIONS, (E) ERRORS OR DEFECTS IN THE SERVICE OR APPLICATION WILL BE CORRECTED, OR (F) THE SERVICE OR THE SERVER(S) THAT MAKE THE SERVICE AVAILABLE ARE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS. THE SERVICE AND APPLICATION IS PROVIDED TO YOU STRICTLY ON AN “AS IS” BASIS. ALL CONDITIONS, REPRESENTATIONS AND WARRANTIES, WHETHER EXPRESS, IMPLIED, STATUTORY OR OTHERWISE, INCLUDING, WITHOUT LIMITATION, ANY IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT OF THIRD PARTY RIGHTS, ARE HEREBY DISCLAIMED TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW BY THE COMPANY. THE COMPANY MAKES NO REPRESENTATION, WARRANTY, OR GUARANTY AS TO THE RELIABILITY, SAFETY, TIMELINESS, QUALITY, SUITABILITY OR AVAILABILITY OF ANY SERVICES, PRODUCTS OR GOODS OBTAINED BY THIRD PARTIES THROUGH THE USE OF THE SERVICE OR APPLICATION. YOU ACKNOWLEDGE AND AGREE THAT THE ENTIRE RISK ARISING OUT OF YOUR USE OF THE APPLICATION AND SERVICE, AND ANY THIRD PARTY SERVICES OR PRODUCTS REMAINS SOLELY WITH YOU, TO THE MAXIMUM EXTENT PERMITTED BY LAW.</p>
            <h3>Internet Delays</h3>
            <p>THE COMPANY’S SERVICE AND APPLICATION MAY BE SUBJECT TO LIMITATIONS, DELAYS, AND OTHER PROBLEMS INHERENT IN THE USE OF THE INTERNET AND ELECTRONIC COMMUNICATIONS. THE COMPANY IS NOT RESPONSIBLE FOR ANY DELAYS, DELIVERY FAILURES, OR OTHER DAMAGE RESULTING FROM SUCH PROBLEMS.</p>
            <h3>Limitation of Liability</h3>
            <p>IN NO EVENT SHALL THE COMPANY’S AGGREGATE LIABILITY EXCEED THE AMOUNTS ACTUALLY PAID BY AND/OR DUE FROM YOU IN THE SIX (6) MONTH PERIOD IMMEDIATELY PRECEDING THE EVENT GIVING RISE TO SUCH CLAIM. IN NO EVENT SHALL THE COMPANY AND/OR ITS LICENSORS BE LIABLE TO ANYONE FOR ANY INDIRECT, PUNITIVE, SPECIAL, EXEMPLARY, INCIDENTAL, CONSEQUENTIAL OR OTHER DAMAGES OF ANY TYPE OR KIND (INCLUDING PERSONAL INJURY, LOSS OF DATA, REVENUE, PROFITS, USE OR OTHER ECONOMIC ADVANTAGE). THE COMPANY AND/OR ITS LICENSORS SHALL NOT BE LIABLE FOR ANY LOSS, DAMAGE OR INJURY WHICH MAY BE INCURRED BY YOU, INCLUDING BY NOT LIMITED TO LOSS, DAMAGE OR INJURY ARISING OUT OF, OR IN ANY WAY CONNECTED WITH THE SERVICE OR APPLICATION, INCLUDING BUT NOT LIMITED TO THE USE OR INABILITY TO USE THE SERVICE OR APPLICATION, ANY RELIANCE PLACED BY YOU ON THE COMPLETENESS, ACCURACY OR EXISTENCE OF ANY ADVERTISING, OR AS A RESULT OF ANY RELATIONSHIP OR TRANSACTION BETWEEN YOU AND ANY THIRD PARTY SERVICE PROVIDER, ADVERTISER OR SPONSOR WHOSE ADVERTISING APPEARS ON THE WEBSITE OR IS REFERRED BY THE SERVICE OR APPLICATION, EVEN IF THE COMPANY AND/OR ITS LICENSORS HAVE BEEN PREVIOUSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</p>
            <h3>Notice</h3>
            <p>The Company may give notice by means of a general notice on the Service, electronic mail to your email address on record in the Company’s account information, or by written communication sent by first class mail or pre-paid post to your address on record in the Company’s account information. Such notice shall be deemed to have been given upon the expiration of 48 hours after mailing or posting (if sent by first class mail or pre-paid post) or 12 hours after sending (if sent by email). You may give notice to the Company (such notice shall be deemed given when received by the Company) at any time by any of the following: letter sent by confirmed facsimile to the Company at the following EMAIL ( INFO@order-green.com ); letter delivered by nationally recognized overnight delivery service or first class postage prepaid mail to the Company at the following addresses (whichever is appropriate): </p>
            <p><strong>Green international Technologies & investment Group LLC </strong></p>
            5th Obour buildings ,14th floor, <br>
            Office No 6, Salah Salem, Cairo<br>
            Tel: 0222621111<br>
            Email: info@order-green.com  www.Order-Green.com<br>
            addressed to the attention of: Managing Director.
            <h3>Assignment</h3>
            <p>This Agreement may not be assigned by you without the prior written approval of the Company but may be assigned without your consent by the Company to </p>
            (i) A parent or subsidiary,<br>
            (ii) An acquirer of https://www.Order-Green.com , or <br>
            (iii) A successor by merger. Any purported assignment in violation of this section shall be void.<br>
            <h3>Governing Law</h3>
            <p>This Agreement is governed by the laws of the country of publicity .
            <h3>Dispute Resolution</h3>
            <p>Any dispute, claim or controversy arising out of or in connection with this Agreement, including a dispute, claim or controversy arising in relation to its interpretation or relating to any non-contractual obligations arising out of or in connection with this agreement (a “Dispute”) shall be settled amicably between the parties following the receipt by either party of written notice of the Dispute from the other party. In the event that a Dispute cannot be settled amicably within a period of 60 days from the date on which the relevant party notifies the other in writing that a Dispute has arisen, the parties agree that such Dispute shall be referred to and finally settled by arbitration under the Egyptian court Arbitration Rules (the “Rules”), which Rules are deemed to be incorporated by reference into this Agreement. The seat, or legal place,</p>
            <p>The language to be used in the arbitration shall be English/Arabic.​</p>
            <p>The award made by the arbitrator shall be final and binding on the parties and may be enforced in any court of competent jurisdiction. To the extent permissible by law, the parties hereby waive any right to appeal against the decision of the arbitrator.
            <p>This “Dispute Resolution” section will survive any termination of this Agreement.</p>
            <h3>General</h3>
            <p>No joint venture, partnership, employment, or agency relationship exists between you, the Company or any third party provider as a result of this Agreement or use of the Service or Application. If any provision of the Agreement is held to be invalid or unenforceable, such provision shall be struck and the remaining provisions shall be enforced to the fullest extent under law. The failure of the Company to enforce any right or provision in this Agreement shall not constitute a waiver of such right or provision unless acknowledged and agreed to by the Company in writing. This Agreement comprises the entire agreement between you and the Company and supersedes all prior or contemporaneous negotiations, discussions or agreements, whether written or oral, between the parties regarding the subject matter of this Agreement.</p>
                • Any dispute or claim arising out of or in connection with this website shall be governed and construed in accordance with the laws of Arab Republic of Egypt.
                • Arab Republic of Egypt is our country of domicile.
                • Minors under the age of 18 shall are prohibited to register as a User of this website and are not allowed to transact or use the website.
                • If you make a payment for our products or services on our website, the details you are asked to submit will be provided directly to our payment provider via a secured connection.
                • The cardholder must retain a copy of transaction records and Merchant policies and rules.
                • You are responsible for all damages to your property and Green International Technology& Investment Group staff  are not liable for any loss or damage incurred
            ";
        return $result;
    }
    public function privacy_policy()
    {
        $result = "<h2>Privacy policy</h2>
        <p>Green international Technologies & investment Group LLC the owner of Order-Green application is committed to protecting our visitors’ and members’ privacy. The following Privacy Policy outlines the information Green international Technologies & investment Group LLC (the “Company”, “we”, or “us”) may collect and how we may use that information to better serve visitors and members while using our website www.order-green and mobile application.</p>
        <p>Please read this Privacy Policy carefully to understand our policies and practices regarding your information and how we will treat it. By accessing or using our website and mobile application (collectively, the service), you agree to the terms of this Privacy Policy. This Privacy Policy may change from time to time. Please review the following carefully so that you understand our privacy practices.</p>
        <p>If you have questions about this Privacy Policy, please contact us at (management@order-green.com).</p>
        <h3>Information We Collect:</h3>
        <p>Upon registration with the Service (either as a consumer, partner or driver), a user profile is developed to further customize the user’s experience. The current required data fields are:</p>
        <ul>
            <li>Email Password</li>
            <li>Name Mobile Number</li>
            <li>Date of birth </li>
            <li>Gender</li>
        </ul>

        <h3>Description of the service</h3>
        <p>Customer can order/request our services (car services ) through our application ( Order-Green , In addition, tracking information is collected as you navigate through our application (Order-Green) or use the Service, including, but not limited to geographic areas. If you are ordering a car services via the Service, the driver’s mobile phone will record your GPS coordinates. Most GPS enabled mobile devices can define one’s location to within 80 meter. We collect this information only to locate our customer's location on the map.</p>

        <p>We also collect device type and unique identifier when you use our mobile application, we use this information for the sole purpose of providing you with the most up to date application and features. You may also choose to upload photos while using the application, if you wish to do so this may be viewable by the car service providers so that they are able to verify your vehicle. You may remove or update photos at any time by logging into your account. If you use our services through your mobile device, we will track your geo-location information so that you are able to view the car  service providers in your area that are close to your location, set your location, and our servicers are able to find the location in which you wish to have the vehicle served. We will not share this information with third parties for any purpose and will only use this information for the sole purpose of fulfilling your request. You may at any time no longer allow our application to use your location by turning this off at the device level.</p>

        <p>To help us serve your needs better, we use “cookies” to store and sometimes track user information. A cookie is a small amount of data that is sent to your browser from a web server and stored on your computer’s hard drive. Cookies can be disabled or controlled by setting a preference within your web browser.</p>

        <p>Users of the Website should be aware that non-personal information and data may be automatically collected by virtue of the standard operation of the Company’s computer servers or through the use of “cookies”. Cookies are files a website can use to recognize repeat users, and allow a website to track web usage behavior. Cookies take up minimal room on your computer and cannot damage your computer’s files. Cookies work by assigning a number to the user that has no meaning outside of the assigning website. Users should be aware that the Company cannot control the use of cookies (or the resulting information) by third-parties. If you do not want information to be collected through the use of cookies, your browser allows you to deny or accept the use of cookies. There may, however, be some features of the Service which require the use of cookies in order to customize the delivery of information to you.</p>

        <p>The use of third party cookies is not covered by our privacy policy. We do not have access or control over these cookies.</p>

        <p>All credit/debit cards details and personally identifiable information will NOT be stored, sold, shared, rented or leased to any third parties.</p>

        <p>The Website Policies and Terms & Conditions may be changed or updated occasionally to meet the requirements and standards. Therefore the Customers’ are encouraged to frequently visit these sections in order to be updated about the changes on the website. Modifications will be effective on the day they are posted.</p>

        <p>Some of the advertisements you may see on the Site are selected and delivered by third parties, such as ad networks, advertising agencies, advertisers, and audience segment providers. These third parties may collect information about you and your online activities, either on the Site or on other websites, through cookies, web beacons, and other technologies in an effort to understand your interests and deliver to you advertisements that are tailored to your interests. Please remember that we do not have access to, or control over, the information these third parties may collect. The information practices of these third parties are not covered by this privacy policy.</p>


        <h3>How We Use Your Information</h3>
        <p>Our primary goal in collecting information is to provide you with an enhanced experience when using the Service. We use this information to closely monitor which features of the Service are used most, to allow you to view your car services history, store your credit card information with our PCI certified payment partner, view any promotions we may currently be running, rate trips, and to determine which features we need to focus on improving, including usage patterns and geographic locations to determine where we should offer or focus services, features and/or resources, we use the mobile information collected so that we are able to serve you the correct app version depending on your device type, for troubleshooting and in some cases marketing purposes. We use the credit card information you provide us to bill you for services.</p>
        <p>The Company uses your Internet Protocol (IP) address to help diagnose problems with our computer server, and to administer the Website. Your IP address is used to help identify you, and to gather broad demographic data. Your IP address contains no personal information about you.</p>

        <h3>Service-Related Announcements</h3>
        <p>We will send you strictly service-related announcements on rare occasions when it is necessary to do so. For instance, if our service is temporarily suspended for maintenance, we might send you an email.</p>
        <p>Generally, you may not opt-out of these communications, which are not promotional in nature. If you do not wish to receive them, you have the option to deactivate your account.</p>

        <h3>Customer Service</h3>
        <p>Based upon the personally identifiable information you provide us, we will send you a welcoming email to verify your username and password. We will also communicate with you in response to your inquiries, to provide the services you request, and to manage your account. We will communicate with you by email or telephone, in accordance with your wishes.</p>

        ​<h3>Targeted or Behavioral Advertising</h3>
        <p>Targeted advertising (also known as Behavioral Advertising) uses information collected on an individual’s web browsing behavior such as the pages they have visited or the searches they have made. This information is then used to select which Order-Green.com advertisement should be displayed to a particular individual on websites other than Order-Green.com, For example, if you have shown a preference for nursing while visiting Order-Green.com, you may be served an advertisement from Order-Green.com for nursing related programs when you visit a site other than Order-Green.com. The information collected is only linked to an anonymous cookie ID (alphanumeric number); it does not include any information that could be linked back to a particular person, such as their name, address or credit card number. The information used for targeted advertising either comes from Order-Green.com or through third party website publishers.</p>

        ​<h3>Our Disclosure of Your Information</h3>
        <p>We do not sell, rent or trade your personal information or geo-location information. We will only use this information as disclosed within this privacy policy.</p>
        <p>The Company may share aggregated information that includes non-identifying information and log data with third parties for industry analysis, demographic profiling and to deliver targeted advertising about other products and services.</p>
        <p>We may employ third party companies and individuals to facilitate our Service, to provide the Service on our behalf, to process payment, provide customer support, provide geo-location information to our service providers, to host our job application form, to perform Website-related services (e.g., without limitation, maintenance services, database management, web analytics and improvement of the Website’s features) or to assist us in analyzing how our Website and Service are used. These third parties have access to your personal information only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose. We may also provide personal information to our business partners or other trusted entities for the purpose of providing you with information on goods or services we believe will be of interest to you. You can, at any time, opt out of receiving such communications by contacting those third parties directly.</p>

        <p>The Company cooperates with government and law enforcement officials and private parties to enforce and comply with the law. We will disclose any information about you to government or law enforcement officials or private parties as we, in our sole discretion, believe necessary or appropriate to respond to claims and legal process (including but not limited to subpoenas), to protect the property and rights of the Company or a third party, to protect the safety of the public or any person, or to prevent or stop activity we may consider to be, or to pose a risk of being, an illegal, unethical or legally actionable activity.</p>

        <p>If we are involved in a merger, acquisition, or sale of all or a portion of its https://www. Order-Green.com, you will be notified via email and/or a prominent notice on our Web site of any change in ownership or uses of your personal information, as well as any choices you may have regarding your personal information.</p>

        <p>We use a third party hosting provider who hosts our support section of the site. Information collected within this section of the site is governed by our privacy policy. Our third party service provider does not have access to this information.</p>

        <h3>Access to Personal Information</h3>
        <p>If your personal information changes, or if you no longer desire our service, you may correct, delete inaccuracies, or amend it by making the change on our member information page or by emailing us at (management@Order-Green.com). We will respond to your access request within 30 days.</p>

        <p>We will retain your information (including geo-location) for as long as your account is active or as needed to provide you services. If you wish to cancel your account or request that we no longer use your information to provide you services contact us at management@Order-Green.com. We will retain and use your information as necessary to comply with our legal obligations, resolve disputes, and enforce our agreements.</p>

        <h3>Security</h3>
        <p>The personally identifiable and geo-location information we collect is securely stored within our database, and we use standard, industry-wide, commercially reasonable security practices such as encryption, firewalls and SSL (Secure Socket Layers) for protecting your information. However, as effective as encryption technology is, no security system is impenetrable. We cannot guarantee the security of our database, nor can we guarantee that information you supply won’t be intercepted while being transmitted to us over the Internet, and any information you transmit to the Company you do at your own risk. We recommend that you not disclose your password to anyone.</p>

        <h3>Invite Friends</h3>
        <p>If you choose to use our referral service to tell a friend about our site, we will ask you for your friend’s name and email address. We will automatically send your friend a one-time email inviting him or her to visit the site. We store this information for the sole purpose of sending this one-time email and tracking the success of our referral program.</p>
        <p>Your friend may contact us at info@Order-Green.com to request that we remove this information from our database.</p>

        <h3>Social Media (Features) and Widgets</h3>
        <p>Our website includes Social Media Features, such as the Facebook Like button and Widgets, such as the Share this button or interactive mini-programs that run on our site. These features may collect your IP address, which page you are visiting on our site, and may set a cookie to enable the feature to function properly. Social Media Features and Widgets are either hosted by a third party or hosted directly on our Site. Your interactions with these Features are governed by the privacy policy of the company providing it.</p>

        <h3>Changes in this Privacy Policy</h3>
        <p>We may update this privacy statement to reflect changes to our information practices. If we make any material changes we will notify you by email (sent to the e-mail address specified in your account) or by means of a notice on this Site prior to the change becoming effective. We encourage you to periodically review this page for the latest information on our privacy practices.</p>



        <h3>Contact us by postal mail</h3>
        Green international Technologies & investment Group LLC
        <br>5th Obour buildings ,14th floor, Office No 6,
        <br>Salah Salem, Cairo
        <br>Tel: 0222621111
        <br>Email: info@order-green.com  www.Order-Green.com";

        return $result;
    }
    public function getSubscriptionInvoice($subscription_id)
    {
        $this->db->select('*');
        $this->db->from('manage_subscription');
        $this->db->join('manage_payment', 'manage_subscription.payment_id = manage_payment.id');
        $this->db->join('subscription', 'manage_subscription.subscription_id=subscription.id');
        $this->db->join('user_master', 'manage_subscription.user_id=user_master.user_id');
        $this->db->where('manage_subscription.id', $subscription_id);
        $query = $this->db->get();
        $invoice = $query->row();
        $html = "";
        $html .= '<style type="text/css">
        body {
        margin: 0 !important;
        padding: 0 !important;
        -webkit-text-size-adjust: 100% !important;
        -ms-text-size-adjust: 100% !important;
        -webkit-font-smoothing: antialiased !important;
        font-family: "Baloo Bhai 2", cursive;
        }
        img {
        border: 0 !important;
        outline: none !important;
        }
        p {
        Margin: 0px !important;
        Padding: 0px !important;
        }
        table {
        border-collapse: collapse;
        mso-table-lspace: 0px;
        mso-table-rspace: 0px;
        }
        td, a, span {
        border-collapse: collapse;
        mso-line-height-rule: exactly;
        }
        a.green_btn {
            background: #34b450;
            text-decoration: none;
            color: #fff;
            padding: 5px 9px;
            display: inline-block;
            text-align: center;
            width: 95%;
            margin: 0 auto;
            font-size: 10px;
            text-transform: uppercase;
        }
        a.blue_btn {
            background: #68c4ea;
            padding: 8px 30px;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            border-radius: 5px;
            font-size: 20px;
        }
        table.table_custom th {
            font-size: 14px !important;
            padding: 5px;
            text-align: center !important;
            color: #29abe2;
        }
        table.table_custom td {
            font-size: 14px;
            text-align: center;
            padding: 5px;
        }
        table.color_table td:first-child {
            background: #35b44f;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
        }
        table.color_table td:nth-child(2) {
            background: #29abe2;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
        }
        table.billing_table td {
            padding: 0px 10px;
            font-size: 12px;
            color: #2d2d2d;
            font-weight: 500;
            text-align: center;
            font-family: "Baloo Bhai 2", cursive;
        }

        table.billing_table {
            border-color: #000;
        }
        table.billing_table.order_table td {
            text-align: left;
            font-size: 10px;
        }
        table.billing_table.order_table td:first-child b {
            width: 55%;
            display: inline-block;
            float: left;
        }
        table.billing_table.order_table td:nth-child(2) b {
            width: 35%;
            display: inline-block;
            float: left;
        }
        table.billing_table.order_table td span {
            display: table;
        }
        table.billing_table tr.nrightbord td {
            border: none;
        }
        tr.bno td {
            border-bottom-color: transparent;
        }
        .ExternalClass * {
        line-height: 100%;
        }
        .em_defaultlink a {
        color: inherit !important;
        text-decoration: none !important;
        }
        span.MsoHyperlink {
        mso-style-priority: 99;
        color: inherit;
        }
        span.MsoHyperlinkFollowed {
        mso-style-priority: 99;
        color: inherit;
        }
        @media only screen and (min-width:481px) and (max-width:699px) {
        .em_main_table {
        width: 100% !important;
        }
        .em_wrapper {
        width: 100% !important;
        }
        .em_hide {
        display: none !important;
        }
        .em_img {
        width: 100% !important;
        height: auto !important;
        }
        .em_h20 {
        height: 20px !important;
        }
        .em_padd {
        padding: 20px 10px !important;
        }
        }
        @media screen and (max-width: 480px) {
        .em_main_table {
        width: 100% !important;
        }
        .em_wrapper {
        width: 100% !important;
        }
        .em_hide {
        display: none !important;
        }
        .em_img {
        width: 100% !important;
        height: auto !important;
        }
        .em_h20 {
        height: 20px !important;
        }
        .em_padd {
        padding: 20px 10px !important;
        }
        .em_text1 {
        font-size: 16px !important;
        line-height: 24px !important;
        }
        u + .em_body .em_full_wrap {
        width: 100% !important;
        width: 100vw !important;
        }
        }
        </style>
        <body class="em_body" style="margin:0px; padding:0px;" bgcolor="#efefef">
        <table class="em_full_wrap" valign="top" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#efefef" align="center">
        <tbody><tr>
        <td valign="top" align="center"><table class="em_main_table" style="width:750px;background-color: #fff;background-size: 120px;background-repeat: no-repeat;background-position: top right;"  cellspacing="0" cellpadding="0" border="0" align="center">
            <tbody>

            <tr>
            <td valign="top" align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                    <tr class="em_padd" valign="top" >
                    <td width="100%" style="padding: 0px;text-align: center;"><img src="' . base_url() . "assets/img/logo.png" . '" alt="Logo" width="60" style="float: right;"></td>
                    </tr>
                </tbody></table></td>
            </tr>
            <tr>
                <td style="font-size: 30px; text-align: center; font-weight: 600;padding: 8px 0;">Order Greenly ' . $invoice->subscription_type . ' Subscription </td>
            </tr>
            <tr>
                <td style="">
                    <div style="width: 60%;margin: 0 auto;font-weight: 600;font-size: 14px;border: 1px solid;padding: 4px 10px;">Payment Invoice ID - ' . $invoice->invoice_id . '  </div>
                </td>
            </tr>
            <tr>
                <td class="em_h20" style="font-size:0px; line-height:0px; height:70px;" >&nbsp;</td>
            </tr>

            <tr>
            <td style="" class="em_padd" valign="top"  align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                <tr style="width: 100%;">
                    <td style="font-family:Baloo Bhai 2, cursive;font-size: 16px;color: #000000;text-align: left;" valign="top" width="100%">

                    <table style="font-size: 15px;" width="100%">
                        <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Customer Name :</div>
                            <div style="">' . $invoice->user_first_name . ' ' . $invoice->user_last_name . '</div></td>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">DATE :</div>
                            <div style="">' . $invoice->created_at . ' </div>
                        </td>
                        </tr>
                    <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Contact Number :</div>
                            <div style="">' . $invoice->user_phone . ' </div></td>

                        </tr>
                    <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Email Address :</div>
                            <div style="">' . $invoice->user_email . ' </div></td>

                        </tr>
                        <tr>
                        <td width="50%" style="padding: 5px 0;">
                            <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Invoice Number :</div>
                            <div style="">' . $invoice->invoice_id . ' </div></td>

                        </tr>
                        <tr>
                            <td width="50%" style="padding: 5px 0;">
                                <div style="width: 145px; float: left; text-align: center; border: 1px solid;   margin-right: 10px; font-size: 13px;">Service Type :</div>
                                <div style="">' . $invoice->subscription_type . '</div></td>

                        </tr>
                    </table>
                    </td>

                </tr>
                <tr>
                    <td style="font-size:0px; line-height:0px; height:40px;">&nbsp;</td>
                </tr>
                <td style="">
                <tr>
                        <div style="text-align:center; font-weight: 400;font-size: 24px;padding: 4px 10px;">Invoice Details</div>
                        </td>
                    </tr>
                <tr>
                    <td style="font-family:Open Sans, Arial, sans-serif;font-size:18px;line-height:22px;color: #000000;padding-bottom:12px;" valign="top" align="left">

                    <table class="billing_table" width="100%" border="1px">
                        <tr>
                        <td width="12%" style="padding: 8px 0;"><b>ITEM NO</b></td>
                        <td width="57%" style="padding: 8px 0;"><b>Discription</b></td>
                        <td width="6%" style="padding: 8px 0;"><b>Qty</b></td>
                        <td width="10%" style="padding: 8px 0;"><b>Price</b></td>
                        <td width="15%" style="padding: 8px 0;"><b>Amount</b></td>
                        </tr>
                        ';

        $html .= '
                                <tr style="height: 270px;vertical-align: baseline;">
                                <td>
                                <div>1</div>
                                </td>
                                <td colspan="2">
                                <div style="text-align: left;"><b>Totally ' . $invoice->total_service_count . ' Free Yallo Recovery services.</b></div>
                                </td>
                                <td>
                                <div>' . $invoice->subscription_amount . '</div>
                                </td>
                                <td>
                                <div>' . $invoice->subscription_amount . '</div>
                                </td>
                            </tr>';

        $html .= '<tr class="nrightbord">
                        <td colspan="3" class="sdf" style="font-weight: 500; text-align: right;font-size: 13px; border-right: 0px; padding: 10px 6px;">
                                <div>SubTotal</div>
                                <div>Total</div>
                        </td>
                        <td colspan="2">
                                <div style="text-align: left;">: ' . $invoice->subscription_amount . '</div>
                                <div style="text-align: left;">: ' . $invoice->subscription_amount . '</div>

                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td class="em_h20" style="font-size:0px; line-height:0px; height:10px;" >&nbsp;</td>
                </tr>
                <tr>
                    <td style="font-family:Baloo Bhai 2, cursive;font-size:11px;line-height:18px;color: #000000;padding-bottom:2px;font-weight: 500; " valign="top" align="center">شكراً لإستخدامكم خدمات أوردر جريـنلـي و نتمني لكم يوماً سعيداً</td>
                </tr>
                <tr>
                    <td style="font-family:Baloo Bhai 2, cursive;font-size:11px;line-height:18px;color: #000000;padding-bottom:12px;font-weight: 500; " valign="top" align="center">Thank you for using Order Greenly Services and we wish you a happy day
                    </td>
                </tr>
                <tr>
                    <td class="em_h20" style="font-size:0px; line-height:0px; height:12px;" >&nbsp;</td>
                </tr>

                </tbody></table></td>
            </tr>

        </tbody></table></td>
        </tr></tbody></table>';
        return $html;

    }

    public function emailSending($to, $subject, $content)
    {
        $body = $this->load->view('email', $content, true);
        $this->load->config('email');
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $this->email->from($this->config->item('smtp_user'), 'Order Greenly');
        $this->email->to($to);
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();
    }
    public function emailSendingWithAttach($to, $subject, $content)
    {
        $body = $this->load->view('email', $content, true);
        $this->load->config('email');
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->from($this->config->item('smtp_user'), 'Order Greenly');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($body);
        $invoice = FCPATH . '/uploads/invoices/' . $content['invoice'];
        $this->email->attach($invoice);
        $this->email->send();
    }
    public function getUserLocation($garage_id)
    {
        $this->db->select('current_lat,current_long');
        $this->db->from('user_tracking');
        $this->db->where('user_id', $garage_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->row();
    }
    public function updateUserLocation($data, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->update('user_tracking', $data);
    }
    public function insertGarageLocation($data)
    {
        $this->db->insert('user_tracking', $data);
        $insert_id = $this->db->insert_id();
    }
    public function checkUserByNumber($user_phone)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_phone', $user_phone);
        $query = $this->db->get();
        return $query->row();
    }
    public function getSubscriptionByuserID($user_id)
    {
        $this->db->select('*');
        $this->db->from('manage_subscription');
        $this->db->join('subscription', 'subscription.id = manage_subscription.subscription_id');
        $this->db->where('subscription.status', 1);
        $this->db->where('manage_subscription.user_id',$user_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function getSubscription()
    {
        $this->db->select('*');
        $this->db->from('subscription');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->row();
    }
    public function insertSubscription($data)
    {
        $this->db->insert('manage_subscription', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function updateSubscription($data, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->update('manage_subscription', $data);
    }
    public function getPromoCode($data, $type)
    {
        if($type == 'status'){
            $where = "status = 1 AND promo_code = '" . $data['promo_code'] . "'";
        } elseif($type == "service"){
            $where = " status = 1 AND promo_code = '" . $data['promo_code'] . "' AND JSON_SEARCH(service_type, 'all', '" . $data['service_type'] . "%') IS NOT NULL";
        } elseif($type == 'date'){
            $where = " status = 1 AND promo_code = '" . $data['promo_code'] . "' AND expiry_date > NOW() AND JSON_SEARCH(service_type, 'all', '" . $data['service_type'] . "%') IS NOT NULL";
        } 
        $query = $this->db->query("SELECT * FROM  promotion_code WHERE $where ");
        return $result = $query->row();
    }
    public function insertUserPromo($data)
    {
        $this->db->insert('manage_promo', $data);
        $insert_id = $this->db->insert_id();
    }
    public function checkPromoByuserId($promo_id, $user_id)
    {
        $this->db->select('*');
        $this->db->from('manage_promo');
        $this->db->where('promo_id',$promo_id);
        $this->db->where('user_id',$user_id);
        $query = $this->db->get();
        return $query->row();
    }
    public function getTermsPolicyByType($data)
    {
        $this->db->select('*');
        $this->db->from('manage_content');
        $this->db->where('type', $data['type']);
        $this->db->where('language', $data['user_language'] ?? 1);
        $query = $this->db->get();
        return $query->row();
    }
    public function getLanguage($data)
    {
        $this->db->select('*');
        $this->db->from('manage_content');
        $this->db->where('type', $data['type']);
        $query = $this->db->get();
        return $query->row();
    }
}
