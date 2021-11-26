<?php
if(isset($_SESSION['error']) && trim($_SESSION['error'])!=null)
{
    echo "<p class='alert  alert-warning text-center'>".$_SESSION['error']."</p>";
    unset($_SESSION['error']);
}
else if(isset($_SESSION['success']) && trim($_SESSION['success'])!=null)
{
    echo "<p class='alert alert-success text-center'>".$_SESSION['success']."</p>";
    unset($_SESSION['success']);
}
?>