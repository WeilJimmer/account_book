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

if ($force_https and $_SERVER['HTTPS']!='on'){
	@header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	exit('Please use HTTPS connect the web.');
}

if ($_SESSION['key']!=$access_key or $access_key==''){
	echo '<script>alert("未登入！");</script>';
	exit;
}

function weil_ascii_html_en($str){
	$str=mb_convert_encoding($str, 'ucs-2', 'utf-8');
	for($i=0;$i<strlen($str);$i+=2){
		$str2.='&#'.str_pad((ord($str[$i])*256+ord($str[$i+1])),5,'0',STR_PAD_LEFT).';';
	}
	return $str2;
}

if(isset($_REQUEST['mod'])){
	switch($_REQUEST['mod']){
		case 'logout':
			if(@session_destroy()){
				echo '<script>alert("登出成功！");window.top.location="./";</script>';
			}else{
				echo '<script>alert("登出失敗！");</script>';
			}
		break;
		case 'add':
			
			if (strlen($_POST['content'])>1500 or strlen($_POST['title'])>60 or strlen($_POST['name'])>60 or strlen($_POST['btime'])>19 or strlen($_POST['money'])>12){
				//預防攻擊者預先注入極長字串，影響到 weil_ascii_html_en 函數的運算速度。
				echo '<script>alert("某項欄位長度異常！不處理！")</script>';
				exit;
			}
			
			$myip=$_SERVER['REMOTE_ADDR'];
			$money=floatval($_POST['money']);
			$title=weil_ascii_html_en($_POST['title']);
			$content=weil_ascii_html_en($_POST['content']);
			$name=weil_ascii_html_en($_POST['name']);
			$btime=($_POST['btime']);
			$account_id=intval($_POST['account_id']);
			
			$content=preg_replace("/(&#00013;&#00010;){3,}/","&#00010;&#00010;",$content);
			$content=str_replace("&#00010;","<br>",$content);
			
			if (strlen($money)>12){
				echo '<script>alert("金額位數有誤！");</script>';
				exit;
			}
			
			if (strlen($title)<8 or strlen($title)>160){
				echo '<script>alert("標題有誤！");</script>';
				exit;
			}
			
			if (strlen($content)>4000){
				echo '<script>alert("內容有誤！");</script>';
				exit;
			}
			
			if (strlen($name)>160){
				echo '<script>alert("紀錄者名稱有誤！");</script>';
				exit;
			}
			
			if (strlen($btime)!=19 and strlen($btime)!=0){
				echo '<script>alert("時間有誤！");</script>';
				exit;
			}
			
			if (@strtotime($btime)===false and $btime!=''){
				echo '<script>alert("時間有誤！");</script>';
				exit;
			}
			
			if ($account_id<0 or $account_id>=count($account_book_array)){
				echo '<script>alert("帳戶ID有誤！");</script>';
				exit;
			}
			
			if ($btime==''){
				$btime=time();
			}else{
				$btime=strtotime($btime);
			}
			
			$query="SELECT * FROM ${sqlpre}_record2 WHERE id='$account_id'";
			$result=@mysqli_query($con,$query) or die('<script>alert("新增失敗！\n'.(mysqli_error($con)).'")</script>');
			$line = mysqli_fetch_assoc($result);
			
			$remain_sum=(floatval($line['remain_sum'])+$money);
			
			$query="UPDATE ${sqlpre}_record2 SET remain_sum='$remain_sum' WHERE id='$account_id'";
			$result=@mysqli_query($con,$query) or die('<script>alert("新增失敗！\n'.(mysqli_error($con)).'")</script>');
			
			$query="INSERT INTO ${sqlpre}_record (money,remain_sum,title,content,time,name,ip,account_id) VALUE('$money','$remain_sum','$title','$content','$btime','$name','$myip','$account_id')";
			$result=@mysqli_query($con,$query) or die('<script>alert("新增失敗！\n'.(mysqli_error($con)).'")</script>');
			
			echo '<script>alert("新增成功！");window.top.location.reload();</script>';
			
		break;
		default:
	}
}

exit;?>