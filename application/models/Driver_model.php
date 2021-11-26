<?php

class driver_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_authenticate($token)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id',$token);
        $this->db->where('user_type','0');
        $this->db->where('user_status',1);
        $this->db->where('is_verified',1);
        $query = $this->db->get();
        return $query->row();
    }

    public function ResetCode_exists($number)
	{
		$this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('verification_code',$number);
        return $this->db->count_all_results();
    }

    public function insert($userData)
    {
        $query=$this->db->insert('user_master', $userData); 
        return  $insert_id=$this->db->insert_id();
    }

    public function verify_driver($user_id,$verification_code)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id',$user_id);
        $this->db->where('verification_code',$verification_code);
        $query = $this->db->get();
        return $query->row();
    }

    public function user_update($user_data,$user_id,$tableName)
    {
        $this->db->where('user_id',$user_id);	
        $query=$this->db->update($tableName, $user_data); 
    }

    public function userLogin($user_type,$user_phone,$user_password)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_type',$user_type);
        $this->db->where('user_phone',$user_phone);
        $this->db->where('user_password',$user_password);
        $query = $this->db->get();
        return $query->row();
    }

    public function insert_certificate($certificateData)
    {
        $query=$this->db->insert('driver_certificate',$certificateData); 
        return  $insert_id=$this->db->insert_id();
    }

    public function getDriver($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id',$user_id);
        $this->db->where('user_status',1);
        $query = $this->db->get();
        return $query->row();
    }

    public function old_password_authenticate($token,$oldPassword)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_id',$token);
        $this->db->where('user_status',1);
        $query = $this->db->get();
        $userData=$query->row();

        if($userData)
        {
            $pass = trim($userData->user_password);
            if(md5($oldPassword)==$pass) 
            {
                return true;
            } 
            else 
            {
                return false;
            }
        }
    }

    public function status_update($booking_id,$token,$status_data)
    {
        $this->db->where('booking_id',$booking_id);	
        // $this->db->where('booked_to',$token);	
        $query=$this->db->update('booking_master',$status_data); 
    }

    public function booked_id($booking_id)
	{
		$this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booking_id',$booking_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function driverBookingAmountAvail($driver_id)
	{
		$this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$driver_id);
        $this->db->where('is_amount_avail',0);
        $this->db->where('booking_status','CP');
        // $query = $this->db->get();
        return $this->db->count_all_results();
        // return $query->row();
    }

    public function insertWalletHistory($historyData)
    {
        $query=$this->db->insert('wallet_history',$historyData); 
        return  $insert_id=$this->db->insert_id();
    }

    public function is_avail_update($booked_to,$isAvailData)
    {
        $this->db->where('booked_to',$booked_to);	
        $query=$this->db->update('booking_master',$isAvailData); 
    }

    public function getOrders($booked_to)
	{
		$this->db->select('*');
        $this->db->from('booking_master');
        $this->db->join('vehicle_master','booking_master.vehicle_id=vehicle_master.vehicle_id');
        $this->db->join('model_master','model_master.model_id=vehicle_master.vehicle_model_id');
        $this->db->join('make_master','make_master.make_id=vehicle_master.vehicle_make_id');
        $this->db->join('plan_master','plan_master.plan_id=booking_master.plan_id');
        $this->db->where('booked_to',$booked_to);
		$query = $this->db->get();
		return $query->result_array();
    }

    public function getTodayOrderCount($booked_to)
	{
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$booked_to);
        $this->db->where('from_unixtime(booked_on) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)');
		return $this->db->count_all_results();
    }

    public function getYestedayOrderCount($booked_to)
	{
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$booked_to);
        $this->db->where('from_unixtime(booked_on) BETWEEN DATE_ADD(CURDATE(), INTERVAL -1 day) AND CURDATE()');
		return $this->db->count_all_results();
    }
    
    public function getthisWeekOrderCount($booked_to)
	{
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$booked_to);
        $this->db->where('from_unixtime(booked_on) BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)');
		return $this->db->count_all_results();
    } 
    
    public function getLastWeekOrderCount($booked_to)
	{
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$booked_to);
        $this->db->where('from_unixtime(booked_on) >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND from_unixtime(booked_on) < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY');
		return $this->db->count_all_results();
    }
    
    public function getCurrentMonthOrderCount($booked_to)
	{
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$booked_to);
        $this->db->where('from_unixtime(booked_on) BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())');
		return $this->db->count_all_results();
    }
    
    public function getLastMonthOrderCount($booked_to)
	{
        $this->db->select('*');
        $this->db->from('booking_master');
        $this->db->where('booked_to',$booked_to);
        $this->db->where('YEAR(from_unixtime(booked_on)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(from_unixtime(booked_on)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND LAST_DAY(NOW())');
		return $this->db->count_all_results();
    }

    public function getWalletHistory($driver_id)
	{
		$this->db->select('*');
        $this->db->from('wallet_history');
        $this->db->where('driver_id',$driver_id);
		$query = $this->db->get();
		return $query->result_array();
    }
    
     public function checkPhone($user_phone)
    {
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('user_phone',$user_phone);
        $this->db->where('user_type',0);
        $query = $this->db->get();
        return $query->row();
    }
}
?>