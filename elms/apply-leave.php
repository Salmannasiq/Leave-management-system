<?php
session_start();
ini_set('display_startup_errors',1); 
ini_set('display_errors',1);
error_reporting(-1);
include('includes/config.php');
if(strlen($_SESSION['emplogin'])==0)
    {   
header('location:index.php');
}
else{
if(isset($_POST['apply']))
{
$empid=$_SESSION['eid'];
 $leavetype=$_POST['leavetype'];
$fromdate=$_POST['fromdate'];  
$todate=$_POST['todate'];
$description=$_POST['description'];  
$status=0;
$isread=0;
?>

<?php 
if($fromdate > $todate){
                $error=" ToDate should be greater than FromDate ";
           }

?>
<?php

?>


<?php
$con=mysqli_connect("localhost","root","","elms");
$sql1="select * from static_date where id='$empid'";
$query1=mysqli_query($con,$sql1);
$date_diff=0;
$yr="";
$mt="";
while($row=mysqli_fetch_array($query1))
{
	$yr=$row['yr'];
	$mt=$row['mth'];
}
?>

<?php
$from=date_create($fromdate);
$to=date_create($todate);
$year = date('Y', strtotime($fromdate));

$month = date('F', strtotime($fromdate));
$finalFrom=$year.$month;
$date_diff=date_diff($from,$to);
	$date_diff=$date_diff->format("%a");
	if($date_diff<=$mt && $date_diff<=$yr)
	{
$sql3="select * from dynamic_date where id='$empid' and tot='$finalFrom'";
$query2=mysqli_query($con,$sql3);
$month_total=0;
while($row3=mysqli_fetch_array($query2))
{
	$month_total+=$row3['mt'];
}
?>
			

<?php
if($date_diff<=$mt-$month_total)
{
	$sql="INSERT INTO tblleaves(LeaveType,ToDate,FromDate,Description,Status,IsRead,empid) VALUES(:leavetype,:todate,:fromdate,:description,:status,:isread,:empid)";
$query = $dbh->prepare($sql);
$query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query->bindParam(':todate',$todate,PDO::PARAM_STR);
$query->bindParam(':description',$description,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->bindParam(':isread',$isread,PDO::PARAM_STR);
$query->bindParam(':empid',$empid,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
$sql4="insert into dynamic_date(id,yr,mt,tot) values('$empid','$yr','$date_diff','$finalFrom')";
mysqli_query($con,$sql4);
$final_diff=$yr-$date_diff;
$sql2="update static_date set yr='$final_diff' where id='$empid'";
mysqli_query($con,$sql2);

$msg="Leave applied successfully";
$S1="select * from admin";
$QUR=mysqli_query($con,$S1);
$id;
while($R=mysqli_fetch_array($QUR))
{
	$id=$R['id'];
}
$S2="select * from email where id='$id'";
$QUR1=mysqli_query($con,$S2);
$email="";
while($R1=mysqli_fetch_array($QUR1))
{
	$email=$R1['mail'];
}
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$SQLLL="select * from tblemployees where id='$empid'";
$QUER=mysqli_query($con,$SQLLL);
while($ROW=mysqli_fetch_array($QUER))
{
	$empid=$ROW['EmpId'];
}

$msg="<b><h2 style='color:#4CAF50'>Leave Request From an Employee</h2><br><br><table border='1'><tr><th>Employee ID</th><th>Leave Type</th><th>Description of Leave</th><th>From Date</th><th>To Date</th></tr>";
$msg.="<tr><td>'$empid'</td><td>'$leavetype'</td><td>'$description'</td><td>'$fromdate'</td><td>'$todate'</td></tr>";
mail($email,"Leave Request",$msg,$headers);
?>
			<script>alert("Leave Applied Successfully");</script>


<?php

	}
	else
	{
		?>
			<script>alert("Leave Limit Exceeded !!!");</script>

<?php	}
}
else{
	?>
	<script>alert("Leave Limit Exceeded !!!");</script>

	

<?php
}
}
    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Employee | Apply Leave</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
  <style>
        .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
        </style>
 


    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Apply for Leave</div>
                    </div>
                    <div class="col s12 m12 l8">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" method="post" name="addemp">
                                    <div>
                                        <h3>Apply for Leave</h3>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <div class="col m12">
                                                        <div class="row">
     

 <div class="input-field col  s12">
<select  name="leavetype" autocomplete="off">
<option value="">Select leave type...</option>
<?php $sql = "SELECT  LeaveType from tblleavetype";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{   ?>                                            
<option value="<?php echo htmlentities($result->LeaveType);?>"><?php echo htmlentities($result->LeaveType);?></option>
<?php }} ?>
</select>
</div>


<div class="input-field col m6 s12">
<label for="fromdate">From  Date</label>
<input placeholder="" id="mask1" name="fromdate" class="masked" type="date" required>
</div>
<div class="input-field col m6 s12">
<label for="todate">To Date</label>
<input placeholder="" id="mask1" name="todate" class="masked" type="date" required>
</div>
<div class="input-field col m12 s12">
<label for="birthdate">Description</label>    

<textarea id="textarea1" name="description" class="materialize-textarea" length="500" required></textarea>
</div>
</div>
      <button type="submit" name="apply" id="apply" class="waves-effect waves-light btn indigo m-b-xs">Apply</button>                                             

                                                </div>
                                            </div>
                                        </section>
                                     
                                    
                                        </section>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        <script src="assets/js/pages/form_elements.js"></script>
          <script src="assets/js/pages/form-input-mask.js"></script>
                <script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
    </body>
</html>
<?php } ?> 