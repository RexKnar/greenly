<?php
if (isset($this->session->userdata['logged_in'])) 
{
 $sesionData=$this->session->userdata['logged_in'];
 $userName=$sesionData['admin_email'];
}
else
{
    redirect('admin/admin/index');
}
?>