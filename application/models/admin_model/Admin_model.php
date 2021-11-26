<?php
class admin_model extends CI_Model
{
    public function login($admin_email, $admin_password)
    {
        $admin_password = md5($admin_password);
        $this->db->select('*');
        $this->db->from('admin_master');
        $this->db->where('admin_email', $admin_email);
        $this->db->where('admin_password', $admin_password);

        if ($query = $this->db->get()) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function userData()
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_status', 1);
        $this->db->where('user_type', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function driverData()
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_status', 1);
        $this->db->where('user_type', 0);
        $this->db->where('driver_verified!=', 2);
        $this->db->where('user_first_name IS NOT NULL', null, false);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function user_update($user_data, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->update('user_master', $user_data);
    }

    public function updateStatus($user_status, $user_id)
    {
        $this->db->set('driver_verified', $user_status);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_master');
    }
    public function updateUserStatus($user_status, $user_id)
    {
        $this->db->set('user_status', $user_status);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_master');

    }
    public function update_status($user_status, $user_id)
    {
        $this->db->set('user_status', $user_status);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_master');
    }

    public function getUserProfile($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_master.user_id', $user_id);
        $this->db->where('is_verified', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function getDriverProfile($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->join('driver_certificate', 'user_master.user_id=driver_certificate.driver_id', 'LEFT');
        $this->db->where('user_master.user_id', $user_id);
        $this->db->where('is_verified', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function getDriverCertificate($driver_id)
    {
        $this->db->select('*');
        $this->db->from('driver_certificate');
        $this->db->where('driver_id', $driver_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function garageData()
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_master.user_type', 2);
        $this->db->where('user_master.is_verified', 1);
        $this->db->where('(user_master.user_status<>3)');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getCategoryData()
    {
        $this->db->select('*');
        $this->db->from('category_master');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getCategoryById($categoryId)
    {
        $this->db->select('*');
        $this->db->from('category_master');
        $this->db->where('status', 1);
        $this->db->where('id', $categoryId);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getsubCategoryData()
    {
        $this->db->select('*');
        $this->db->from('sub_category_master');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getSingleSubCategoryById($id)
    {
        $this->db->select('*');
        $this->db->from('sub_category_master');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCarData()
    {
        $this->db->select('*');
        $this->db->from('make_master');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getCarDataById($make_id)
    {
        $this->db->select('*');
        $this->db->from('make_master');
        $this->db->where("make_id", $make_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function deleteCar($garage_id)
    {
        $this->db->where('make_id', $garage_id);
        $query = $this->db->delete('make_master');
    }
    public function getsubCategoryById($category)
    {
        $this->db->select('*');
        $this->db->from('sub_category_master');
        $this->db->where("category_id", $category);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getGarageData($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_master.user_id', $user_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getGarageService($type_id) 
    {
        $this->db->select('*');
        $this->db->from('service_type');
        $this->db->where('service_id IN (' . $type_id . ')');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getAllGarageServices() 
    {
        $this->db->select('*,(select count(*) from sub_services where sub_services.service_id=service_type.service_id) as subservice_count');
        $this->db->from('service_type');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getTypeofVehicle($make_id)
    {
        $this->db->select('*');
        $this->db->from('make_master');
        $this->db->where('make_id IN (' . $make_id . ')');
        $this->db->where('make_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function AllGarageData($make_id)
    {
        $this->db->select('*');
        $this->db->from('garage_data');
        $this->db->where('garage_data.make_id', $make_id);
        $this->db->join('make_master', 'garage_data.make_id=make_master.make_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function CarWashCenterData()
    {
        $this->db->select('*');
        $this->db->from('car_wash_center');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function deleteWashCenter($center_id)
    {
        $this->db->where('center_id', $center_id);
        $query = $this->db->delete('car_wash_center');
    }

    public function deleteWashServices($center_id)
    {
        $this->db->where('center_id', $center_id);
        $query = $this->db->delete('sub_services');
    }

    public function deleteGarage($garage_id)
    {
        $this->db->where('garage_id', $garage_id);
        $query = $this->db->delete('garage_data');
    }

    public function deleteMainService($service_id)
    {
        $this->db->where('service_id', $service_id);
        $query = $this->db->delete('service_type');
    }
    public function deleteGarageServices($service_id)
    {
        $this->db->where('service_id', $service_id);
        $query = $this->db->delete('service_garage');
    }

    public function addGarage($data)
    {
        $this->db->insert('garage_data', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function getMappingById($userId,$serviceId)
    {
        $this->db->select('*');
        $this->db->from('service_mapping');
        $this->db->where('user_id', $userId);
        $this->db->where('service_id', $serviceId);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function map_service($data)
    {
        $insert =$this->getMappingById($data['user_id'],$data['service_id']);

        if(!$insert )
        {
            $this->db->insert('service_mapping', $data);
            return true;
        }
        else{
            return false;
        }
    
        
    }
    public function deleteServiceMapping($mapping_id)
    {
        $this->db->where('service_mapping_id', $mapping_id);
        
        if($this->db->delete('sub_service_mapping'))
        {
            return true;
        }
        else{
            return false;
        }
    }
    public function getServiceMapping($user_id)
    {
        $this->db->select('*');
        $this->db->from('service_mapping');
        $this->db->join('service_type', 'service_type.service_id=service_mapping.service_id');
        $this->db->where('service_mapping.user_id', $user_id);
        $query = $this->db->get();
        
        return $query->result_array();

        // $this->db->select('* ');
        // $this->db->from('sub_service_mapping as mapping');
        // $this->db->join('sub_services', 'mapping.sub_service_id=sub_services.sub_service_id');
        // $this->db->where('sub_services.service_id IN (' . $type_id . ')');
        // // $this->db->where('status', 1);
        // $query = $this->db->get();
        // print_r($this->db->last_query());   
        // die();
        // return $query->result_array();
    }
    public function map_sub_service($data)
    {
        $insert =$this->getSubServiceMapping($data['user_id'],$data['sub_service_id']);

        if(!$insert )
        {
            $this->db->insert('sub_service_mapping', $data);
            return true;
        }
        else{
            return false;
        }
    
        
    }
    public function getSubServiceMapping($user_id,$service_id)
    {
        $this->db->select('mapping.*, sub_services.service_name, sub_services.service_price as default_price ');
        $this->db->from('sub_service_mapping as mapping');
        $this->db->join('sub_services', 'mapping.sub_service_id=sub_services.sub_service_id');
        $this->db->where('sub_services.service_id ='.$service_id);
        $this->db->where('mapping.user_id ='.$user_id);
        $query = $this->db->get();
        // print_r($this->db->last_query()); 
        // echo '<br>';
        // die();
        return $query->result_array();
    }
    public function getSubServices($service_id)
    {
        $this->db->select('* ');
        $this->db->from('sub_services');
        $this->db->where('sub_services.service_id ='.$service_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function addContent($data)
    {
        $this->db->insert('manage_content', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function addWashCenter($data)
    {
        $this->db->insert('car_wash_center', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function getGarageById($garage_id)
    {
        $this->db->select('*');
        $this->db->from('garage_data');
        $this->db->where('garage_data.garage_id', $garage_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function ServicesData($center_id)
    {
        $this->db->select('*');
        $this->db->from('sub_services');
        $this->db->join('car_wash_center', 'car_wash_center.center_id=sub_services.center_id');
        $this->db->where('sub_services.center_id', $center_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function GarageServicesData($center_id)
    {
        $this->db->select('*');
        $this->db->from('service_garage');
        // $this->db->join('garage_data', 'garage_data.garage_id=service_garage.garage_id');
        $this->db->where('service_garage.garage_id', $center_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function addServices($data)
    {
        $this->db->insert('sub_services', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function addMainServices($data)
    {
        $this->db->insert('service_type', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function addSubServices($data)
    {
        $this->db->insert('sub_services', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }
    public function updateSubServices($data,$sub_service_id)
    {
        $this->db->where('sub_service_id',$sub_service_id);
        $this->db->update('sub_services', $data);
        return true;
    }
    public function getSubserviceList($serviceId)
    {
        $this->db->select('*');
        $this->db->from('sub_services');
        $this->db->where('service_id',$serviceId);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getSubserviceById($subserviceId)
    {
        $this->db->select('*');
        $this->db->from('sub_services');
        $this->db->where('sub_service_id',$subserviceId);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function deleteSubservices($service_id)
    {
        $this->db->where('sub_service_id', $service_id);
        $query = $this->db->delete('sub_services');
        if($query)
        {
            return true;
        }
        else{
            return false;
        }
    }
    public function getSubServiceCount($serviceId)
    {
        $this->db->select('(select count(*) from sub_services where sub_services.service_id=service_type.service_id)');
        $this->db->from('sub_services');
        $this->db->where('service_id',$serviceId);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function addGarageServices($data)
    {
        $this->db->insert('service_garage', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function addCategory($data)
    {
        $this->db->insert('category_master', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function updateCategory($data,$id)
    {
        $this->db->where('id',$id);
        $this->db->update('category_master', $data);
        return true;
    }
    public function deleteCategory($category_id)
    {
        $this->db->where('id', $category_id);
        $query = $this->db->delete('category_master');
    }
    public function addProduct($data)
    {
        $this->db->insert('product_master', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function getProductData()
    {
        $this->db->select('*');
        $this->db->from('product_master');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function editProduct($content, $id)
    {
        $this->db->where('id', $id);
        $query = $this->db->update('product_master', $content);
    }
    public function deleteProduct($product_id)
    {
        $this->db->where('id', $product_id);
        $query = $this->db->delete('product_master');
    }

    public function addsubCategory($data)
    {
        $this->db->insert('sub_category_master', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function updateSubcategory($data,$id)
    {
        $this->db->where('id',$id);
        $this->db->update('sub_category_master', $data);
        return true;
    }
    public function deleteSubcategory($subcategory_id)
    {
        $this->db->where('id', $subcategory_id);
        $query = $this->db->delete('sub_category_master');
    }
    public function garage_booking_data()
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booking_master.booking_type', 2);
        $this->db->join('garage_data', 'garage_data.garage_id=booking_master.garage_id');
        $this->db->join('user_master', 'user_master.user_id=booking_master.booked_by');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function garageBooking_status($booking_id)
    {
        $this->db->set('payment_method', 'COD');
        $this->db->set('booking_status', 'CP');
        $this->db->set('is_paid', 1);
        $this->db->where('booking_master.booking_type', 2);
        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_master');
    }

    public function services_booking_data()
    {
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booking_master.booking_type', 3);
        $this->db->join('car_wash_center', 'car_wash_center.center_id=booking_master.center_id');
        $this->db->join('user_master', 'user_master.user_id=booking_master.booked_by');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function servicesBooking_status($booking_id)
    {
        $this->db->set('payment_method', 'COD');
        $this->db->set('booking_status', 'CP');
        $this->db->set('is_paid', 1);
        $this->db->where('booking_master.booking_type', 3);
        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_master');
    }
    public function getPromoData()
    {
        $this->db->select('*');
        $this->db->from('promotion_code');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getPromoDataByID($id)
    {
        $this->db->select('*');
        $this->db->from('promotion_code');
        $this->db->where('status', 1);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getContentData()
    {
        $this->db->select('*');
        $this->db->from('manage_content');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function addPromo($data)
    {
        $this->db->insert('promotion_code', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function editPromo($content, $id)
    {
        $this->db->where('id', $id);
        $query = $this->db->update('promotion_code', $content);
    }
    public function update_promo_status($status, $id)
    {
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->update('promotion_code');
    }
    public function getContentDataByID($id)
    {
        $this->db->select('*');
        $this->db->from('manage_content');
        $this->db->where('id', $id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function editContent($content, $id)
    {
        $this->db->where('id', $id);
        $query = $this->db->update('manage_content', $content);
    }
    public function past_Order()
    {
        $this->db->select('*,service_type.service_type As serviceName,user.user_id AS userId,user.user_first_name AS userFirstName,user.user_last_name AS userLastName, provider.user_id AS providerId, provider.user_first_name AS providerFirstName,provider.user_last_name AS providerLastName ');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        $this->db->join('user_master AS user', 'user.user_id=booking_master.booked_by');
        $this->db->join('user_master AS provider', 'provider.user_id=booking_master.garage_id');
        $this->db->where_in('booking_status', ['completed','cancel']);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function upcoming_Order()
    {
        $this->db->select('*,booking_master.service_type AS serviceName,service_type.service_type As serviceName,user.user_id AS userId,user.user_first_name AS userFirstName,user.user_last_name AS userLastName, provider.user_id AS providerId, provider.user_first_name AS providerFirstName,provider.user_last_name AS providerLastName ');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        
        $this->db->join('user_master AS user', 'user.user_id=booking_master.booked_by');
        $this->db->join('user_master AS provider', 'provider.user_id=booking_master.garage_id');

        $this->db->where_not_in('booking_master.booking_status', ['completed', 'cancel']);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getRequestRecoveryOrders()
    {
        $this->db->select('*,booking_master.service_type AS serviceName,service_type.service_type As serviceName,user.user_id AS userId,user.user_first_name AS userFirstName,user.user_last_name AS userLastName, provider.user_id AS providerId, provider.user_first_name AS providerFirstName,provider.user_last_name AS providerLastName ');
        $this->db->from('booking_request');
        $this->db->join('booking_master', 'booking_master.booking_id = booking_request.booking_id');
        $this->db->join('vehicle_master', 'booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master', 'model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master', 'make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('service_type', 'service_type.service_id=booking_master.service_type');
        $this->db->join('user_master AS user', 'user.user_id=booking_master.booked_by');
        $this->db->join('user_master AS provider', 'provider.user_id=booking_master.garage_id');
        $this->db->where('booking_request.status', 'request');
        $query = $this->db->get();
        return $query->result_array();
    }
}   
