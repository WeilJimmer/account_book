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

require_once('./data/config.php');
require_once("./data/conn.php");
require_once('./data/access_key.php');
session_start();

if ($_SESSION['key']!=$access_key){
	require_once('./data/login_form.php');
}



?><html>
<head>
<title><?php echo $title_name; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=350,maximum-scale=1.0, user-scalable=yes">
<meta name="author" content="Weil Jimmer">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="./css/page.css">
</head>
<body style="color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);" alink="#000099" link="#000099" vlink="#990099">
<?php  
ini_set('date.timezone','Asia/Taipei');

//數據庫開始

if(file_exists("./install.lock")){
	echo "請勿重複安裝！";
	exit;
}

//創建表的請求

if (!$con){
	die('Could not connect: ' . mysqli_error($con));
}

function weil_ascii_html_en($str){
	$str=mb_convert_encoding($str, 'ucs-2', 'utf-8');
	for($i=0;$i<strlen($str);$i+=2){
		$str2.='&#'.str_pad((ord($str[$i])*256+ord($str[$i+1])),5,'0',STR_PAD_LEFT).';';
	}
	return $str2;
}

$sql = "CREATE TABLE ${sqlpre}_record
(
id bigint AUTO_INCREMENT,
money double,
remain_sum double,
title text,
content text,
time bigint,
name text,
ip text,
account_id bigint,
PRIMARY KEY (id)
)";
$result = mysqli_query($con,$sql) or die("Query failed : " . mysqli_error($con)); 

$sql = "CREATE TABLE ${sqlpre}_login_log
(
id bigint AUTO_INCREMENT,
time bigint,
ip text,
PRIMARY KEY (id)
)";
$result = mysqli_query($con,$sql) or die("Query failed : " . mysqli_error($con)); 

$sql = "CREATE TABLE ${sqlpre}_record2
(
id bigint AUTO_INCREMENT,
name_of_account text,
remain_sum bigint,
lasttime bigint,
PRIMARY KEY (id)
)";
$result = mysqli_query($con,$sql) or die("Query failed : " . mysqli_error($con)); 

for($i=0;$i<count($account_book_array);$i++){
	$query="INSERT INTO ${sqlpre}_record2 (name_of_account,remain_sum,lasttime) VALUE('".(weil_ascii_html_en($account_book_array[$i]))."','0','-9999999')";
	$result=@mysqli_query($con,$query) or die('插入帳本失敗FAIL');
}

mysqli_close($con);


@$fp=fopen("./install.lock","w");
@fclose($fp);

//數據庫結束


echo "<p><center><font color='red' size='+1'>表創建成功。</font><br><br><a href='./'><font color='red'>點擊此返回上一頁</font></a></center></p>";

 ?>
 
</body>
</html>