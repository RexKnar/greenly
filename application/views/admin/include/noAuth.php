<?php
if(isset($this->session->userdata['logged_in'])) 
{
    redirect('admin/admin/manage_user');
}

?>