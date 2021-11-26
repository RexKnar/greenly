
    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0 ">
<meta name="format-detection" content="telephone=no">
<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css2?family=Baloo+Bhai+2:wght@400;500;700&display=swap" rel="stylesheet">

<!--<![endif]-->
<style type="text/css">
body {
  margin: 0 !important;
  padding: 0 !important;
  -webkit-text-size-adjust: 100% !important;
  -ms-text-size-adjust: 100% !important;
  -webkit-font-smoothing: antialiased !important;
  font-family: 'Baloo Bhai 2', cursive;
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
    font-family: 'Baloo Bhai 2', cursive;
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
</head>
<?php //echo "<pre> dsdsd"; print_r($order_detail);?>
<body class="em_body" style="margin:0px; padding:0px;" bgcolor="#efefef">
<table class="em_full_wrap" valign="top" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#efefef" align="center">
  <tbody><tr>
    <td valign="top" align="center"><table class="em_main_table" style="width:750px;background-color: #fff;background-size: 120px;background-repeat: no-repeat;background-position: top right;"  cellspacing="0" cellpadding="0" border="0" align="center">
        <tbody>

        <tr>
          <td valign="top" align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
              <tbody>
                <tr class="em_padd" valign="top" >
                  <td width="100%" style="padding: 0px;text-align: center;"><img src="<?php echo base_url();?>assets/img/logo.png" alt="Logo" width="60" style="float: right;"></td>
                </tr>
            </tbody></table></td>
        </tr>
        <tr>
            <td style="font-size: 30px; text-align: center; font-weight: 600;padding: 8px 0;">Electronic Invoice Number : <?php echo $order_detail->user_first_name; ?> </td>
        </tr>
        <tr>
            <td style="">
                <div style="width: 60%;margin: 0 auto;font-weight: 600;font-size: 14px;border: 1px solid;padding: 4px 10px;">SERVICE PROVIDER NUMBER : <?php echo $order_detail->user_first_name; ?> </div>
            </td>
        </tr>
        <tr>
            <td class="em_h20" style="font-size:0px; line-height:0px; height:70px;" >&nbsp;</td>
        </tr>
        
         <tr>
          <td style="padding:10px 45px 0px;" class="em_padd" valign="top"  align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
              <tbody>
              <tr style="width: 100%;">
                <td style="font-family:'Baloo Bhai 2', cursive;font-size: 16px;color: #000000;text-align: left;" valign="top" width="100%">
                    
                  <table style="font-size: 15px;" width="100%">
                    <tr>
                      <td width="50%" style="padding: 5px 0;"> 
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Customer Name :</div>
                        <div style=""><?php echo $order_detail->user_first_name; ?> <?php echo $order_detail->user_last_name; ?></div></td>
                      <td width="50%" style="padding: 5px 0;">
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">DATE :</div>
                        <div style=""><?php echo $order_detail->booked_on; ?> </div>
                      </td>
                    </tr>
                   <tr>
                      <td width="50%" style="padding: 5px 0;"> 
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Contact Number :</div>
                        <div style=""><?php echo $order_detail->user_phone; ?> </div></td>
                      <td width="50%" style="padding: 5px 0;">
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Car Make :</div>
                        <div style=""><?php echo $order_detail->make; ?> </div>
                      </td>
                    </tr>
                   <tr>
                      <td width="50%" style="padding: 5px 0;"> 
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Email Address :</div>
                        <div style=""><?php echo $order_detail->user_email ?? ''; ?> </div></td>
                      <td width="50%" style="padding: 5px 0;">
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Model :</div>
                        <div style=""><?php echo $order_detail->model; ?> </div>
                      </td>
                    </tr>
                    <tr>
                      <td width="50%" style="padding: 5px 0;"> 
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Order Number :</div>
                        <div style=""><?php echo $order_detail->booking_id; ?> </div></td>
                      <td width="50%" style="padding: 5px 0;">
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Year :</div>
                        <div style=""></div>
                      </td>
                    </tr>

                    <tr>
                      <td width="50%" style="padding: 5px 0;"> 
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Service Type :</div>
                        <div style=""><?php echo $order_detail->service_type; ?> </div></td>
                      <td width="50%" style="padding: 5px 0;">
                        <div style="width: 125px; float: left; text-align: center; border: 1px solid;  margin-right: 20px; font-size: 13px;">Plate Number :</div>
                        <div style=""><?php echo $order_detail->vehicle_engine; ?> - <?php echo $order_detail->vehicle_plate_no; ?></div>
                      </td>
                    </tr>
                  </table>
                </td>
                
              </tr>
              <tr>
                <td style="font-size:0px; line-height:0px; height:40px;">&nbsp;</td>
              </tr>
              <tr>
                <td style="">
                    <div style="text-align:center; font-weight: 400;font-size: 24px;padding: 4px 10px;">Service Details</div>
                    </td>
                </tr>
              <tr>
                <td style="font-family:'Open Sans', Arial, sans-serif;font-size:18px;line-height:22px;color: #000000;padding-bottom:12px;" valign="top" align="left">
                  
                  <table class="billing_table" width="100%" border="1px">
                    <tr>
                      <td width="12%" style="padding: 8px 0;"><b>ITEM NO</b></td>
                      <td width="57%" style="padding: 8px 0;"><b>Discription</b></td>
                      <td width="6%" style="padding: 8px 0;"><b>Qty</b></td>
                      <td width="10%" style="padding: 8px 0;"><b>Price</b></td>
                      <td width="15%" style="padding: 8px 0;"><b>Amount</b></td>
                    </tr>
                    <tr style="height: 270px;vertical-align: baseline;">
                      <td>
                        <div>1</div>
                        <div>2</div>
                        <div>3</div>
                      </td>
                      <td>
                        <div style="text-align: left;">Lorem Ipsum</div>
                        <div style="text-align: left;">Lorem Ipsum</div>
                        <div style="text-align: left;">Lorem Ipsum</div>
                      </td>
                      <td>
                        <div>3</div>
                        <div>2</div>
                        <div>1</div>
                      </td>
                      <td>
                        <div>3000</div>
                        <div>2000</div>
                        <div>1000</div>
                      </td>
                      <td>
                        <div>3000</div>
                        <div>2000</div>
                        <div>1000</div>
                      </td>
                    </tr>
                    <tr class="nrightbord">
                      <td colspan="3" class="sdf" style="font-weight: 500; text-align: right;font-size: 13px; border-right: 0px; padding: 10px 6px;">
                            <div>SubTotal</div>
                            <div>VAT 5%</div>
                            <div>Promotional code</div>
                            <div>Total</div>
                      </td>
                      <td colspan="2">
                            <div style="text-align: left;">: 24,000</div>
                            <div style="text-align: left;">: 345</div>
                            <div style="text-align: left;">: FIRST002</div>
                            <div style="text-align: left;">: 24,345</div>

                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            
             <tr>
                <td class="em_h20" style="font-size:0px; line-height:0px; height:10px;" >&nbsp;</td>
              </tr>
               <tr>
                <td style="font-family:'Baloo Bhai 2', cursive;font-size:11px;line-height:18px;color: #000000;padding-bottom:2px;font-weight: 500; " valign="top" align="center">شكراً لإستخدامكم خدمات أوردر جريـنلـي و نتمني لكم يوماً سعيداً</td>
              </tr>
              <tr>
                <td style="font-family:'Baloo Bhai 2', cursive;font-size:11px;line-height:18px;color: #000000;padding-bottom:12px;font-weight: 500; " valign="top" align="center">Thank you for using Order Greenly Services and we wish you a happy day
                </td>
              </tr>
              <tr>
                <td class="em_h20" style="font-size:0px; line-height:0px; height:12px;" >&nbsp;</td>
<!--—this is space of 25px to separate two paragraphs ---->
              </tr>
                    
            </tbody></table></td>
        </tr>       
       
      </tbody></table></td>
  </tr>
</tbody></table>
