<?php
/*===================================================
* 
* Account Book PHP Script
*
* Developed Date : 2016/03/03
*
* Powered By : Weil Jimmer
* 
*==================================================*/

if ($access_key==""){
	
	if (@$_POST['reset_pw']==""){
		echo '<html>
<head>
<title>'.$title_name.'</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=350,maximum-scale=1.0, user-scalable=yes">
<meta name="author" content="Weil Jimmer">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="./css/login.css">
</head>
<body>
<div style="width:340px;margin:auto;text-align:center;">
<h1 style="color:#750000;text-shadow:#0900FF 1px 1px 25px;margin-top:50px;">登入'.$title_name.'</h1>
<form action="" method="post" style="text-align:center"><input type="password" name="reset_pw"></form>
<h3 style="color:#BD0000;text-shadow:#000457 1px 1px 8px;text-shadow:#0805B8 -1px -1px 3px;">密碼未設置！請設置一個新密碼。</h3>
</div>
</body>
</html>';
		exit;
	}else{
		$access_key=password_hash($_POST['reset_pw'],PASSWORD_DEFAULT,Array('cost' => 10));
		$fp=@fopen('./data/access_key.php','w');
		@fputs($fp,'<?php
/*===================================================
* 
* Account Book PHP Script
*
* Developed Date : 2016/03/03
*
* Powered By : Weil Jimmer
* 
*==================================================*/

//Access key
//creat by "password_hash($access_key,PASSWORD_DEFAULT,Array(\'cost\' => 10));"
$access_key=\''.$access_key.'\';

?>');
		@fclose($fp);
		$query="INSERT INTO ${sqlpre}_login_log (time,ip) VALUE('".(time())."','".($_SERVER['REMOTE_ADDR'])."-pw-reset')";
		@mysqli_query($con,$query);
		$_SESSION['key']=$access_key;
	}
}else{

	if (password_verify($_POST['pw'],$access_key)){
		$_SESSION['key']=$access_key;
		$query="INSERT INTO ${sqlpre}_login_log (time,ip) VALUE('".(time())."','".($_SERVER['REMOTE_ADDR'])."-login')";
		@mysqli_query($con,$query);
	}else{
		if ($_POST['pw']!=""){
			echo '<html>
<head>
<title>'.$title_name.'</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=350,maximum-scale=1.0, user-scalable=yes">
<meta name="author" content="Weil Jimmer">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="./css/login.css">
</head>
<body>
<div style="width:340px;margin:auto;text-align:center;">
<h1 style="color:#750000;text-shadow:#0900FF 1px 1px 25px;margin-top:50px;">登入'.$title_name.'</h1>
<form action="" method="post" style="text-align:center"><input type="password" name="pw"></form>
<h3 style="color:#BD0000;text-shadow:#000457 1px 1px 8px;text-shadow:#0805B8 -1px -1px 3px;">登入失敗！</h3>
</div>
</body>
</html>';
			exit;
		}else{
			echo '<html>
<head>
<title>'.$title_name.'</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=350,maximum-scale=1.0, user-scalable=yes">
<meta name="author" content="Weil Jimmer">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="./css/login.css">
</head>
<body>
<div style="width:340px;margin:auto;text-align:center;">
<h1 style="color:#750000;text-shadow:#0900FF 1px 1px 25px;margin-top:50px;">登入'.$title_name.'</h1>
<form action="" method="post" style="text-align:center"><input type="password" name="pw"></form>
</div>
</body>
</html>';
			exit;
		}
	}


}

?>