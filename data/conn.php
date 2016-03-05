<?php
$con = mysqli_connect("$sqlhost", "$sqluser", "$sqlpassword") or die("Could not connect.無法連接資料庫！" . mysqli_error($con)); 
$select = mysqli_select_db($con,"$sqldb") or die("Could not select database.無法選擇資料庫！" . mysqli_error($con));

?>