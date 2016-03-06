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
require_once('./data/function.php');
require_once('./data/lastpos.php');
session_start();

if ($force_https and $_SERVER['HTTPS']!='on'){
	@header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	exit('Please use HTTPS connect the web.');
}

if ($access_key=='' or $_SESSION['key']!=$access_key){
	require_once('./data/login_form.php');
}

$useragent=$_SERVER['HTTP_USER_AGENT'];
$mob_disable=true;
$width_div='500px';
$box_width='500px';
$box_height='200px';
$box_top='20%';
if((stristr($useragent,'mobile')!==false or stristr($useragent,'iphone')!==false) and (stristr($useragent,'ipad')===false and stristr($useragent,'tab')===false)){
	$mob_disable=false;
	$width_div='100%';
	$box_width='100%';
	$box_height='100%';
	$box_top='0%';
}

$page=intval(@$_GET['page']);

if (isset($_GET['orderby'])){
	$orderby=intval(@$_GET['orderby']);
}else{
	$orderby=intval(@$orderby_lasttime);
}
if (isset($_GET['account'])){
	$account_id=intval(@$_GET['account']);
}else{
	$account_id=intval($accound_id_lasttime);
}

if($page<=0){
	$page=1;
}

if($orderby<0){
	$orderby=0;
}

if($account_id<=0 or $account_id>count($account_book_array)){
	$account_id=1;
}

write_last_pos($account_id,$orderby);

$acu='&nbsp;'.$account_unit_array[($account_id-1)];

$query="SELECT * FROM ${sqlpre}_record2 WHERE id='$account_id'";
$result=@mysqli_query($con,$query) or die('SQL語句執行失敗！'.(mysqli_error($con)));
$line = mysqli_fetch_assoc($result);
$remain_sum=(floatval($line['remain_sum']));

$query="SELECT SUM(money) FROM ${sqlpre}_record WHERE time>".(time()-86400)." AND account_id='$account_id'";
$result=@mysqli_query($con,$query) or die('SQL語句執行失敗！'.(mysqli_error($con)));
$line = mysqli_fetch_array($result);
$day_cost=floatval($line[0]);

$query="SELECT SUM(money) FROM ${sqlpre}_record WHERE time>".(time()-604800)." AND account_id='$account_id'";
$result=@mysqli_query($con,$query) or die('SQL語句執行失敗！'.(mysqli_error($con)));
$line = mysqli_fetch_array($result);
$week_cost=floatval($line[0]);

$query="SELECT SUM(money) FROM ${sqlpre}_record WHERE time>".(time()-2592000)." AND account_id='$account_id'";
$result=@mysqli_query($con,$query) or die('SQL語句執行失敗！'.(mysqli_error($con)));
$line = mysqli_fetch_array($result);
$moon_cost=floatval($line[0]);

$hidden_status='none';
if($page==1){
	$hidden_status='';
}


?><html>
<head>
<title><?php echo $title_name; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=350,maximum-scale=1.0, user-scalable=yes">
<meta name="author" content="Weil Jimmer">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="./css/page.css">
<script type="text/javascript">var lock_form = false;var side_enalbe = false;</script>
<script type="text/javascript" src="./js/main.js"></script>
<script type="text/javascript" src="./js/slideout.min.js"></script>
<script type="text/javascript" src="./js/chart.min.js"></script>
</head>
<body>
<nav id="menu" style="display:none;background-color:rgba(40,40,40,0.9)">
<header>
<h3>選單</h3>
<div class="page_button" style="margin-bottom:20px;text-align:left;color:#FF0000;">
<div><?php
echo '<select id="Select3" style="border: 0px solid #000;width:100%;height:28px;outline: 0;background: rgba(0,0,0,0);color: #00FFEA;" onChange="var Select3= document.getElementById(\'Select3\').options[document.getElementById(\'Select3\').selectedIndex];window.location=\'?type='.$type.'&orderby='.$orderby.'&account=\' + Select3.value;">';
for ($i=0;$i<count($account_book_array);$i++){
	if (($i+1)==$account_id){
		echo '<option style="background-color:#0B143C;color:#FF0000;" selected="selected" value="'.($i+1).'">'.$account_book_array[$i].'</option>'."\n";
	}else{
		echo '<option style="background-color:#0B143C;color:#FF0000;" value="'.($i+1).'">'.$account_book_array[$i].'</option>'."\n";
	}
}
echo '</select>';
?></div><hr>
<div><a onclick="add_item();" href="#titlex">新增紀錄</a></div><hr>
<div><a onclick="show_status()" href="#titlex">狀態</a></div><hr>
<div><a onclick="window.top.location.reload()" href="#">刷新</a></div><hr>
<div><a href="./action.php?mod=logout" target="Ix">登出</a></div>
</header>
</nav>
<div id="panel" style="height:100%;">
<div style="position:fixed;left:10px;top:10px;"><img src="./img/menu.png" alt="選單" onclick="button_toggle()"></div><h2 id="titlex">紀錄 - <?php echo $account_book_array[($account_id-1)]; ?></h2>
<div id="panel1" class="panel" style="width:<?php echo $box_width; ?>;height:<?php echo $box_height; ?>;top:<?php echo $box_top; ?>">
<h3>新增紀錄<span style="float:right"><button style="background-color:black;color:red;" onclick="close_panel()">關閉</button></span></h3>
<form class="form_style" action="action.php?mod=add" method="post" target="Ix" onsubmit="return submit_form()" id="form1">
<table class="table_css2"><tbody>
<tr><td style="width:50px;"><span class="star">*</span>金額：</td><td><input name="money" type="number" id="money_input" pattern="[0-9]*" step="0.00000000001" onfocus="money_check()" onblur="money_check()"></td></tr>
<tr><td><span class="star">*</span>標題：</td><td><input name="title" type="text" maxlength="20" id="title_input"></td></tr>
<tr><td>&nbsp;內容：</td><td><textarea name="content" type="text" maxlength="500" id="content_input" style="resize: none;height:100px;"></textarea></td></tr>
<tr><td>&nbsp;記錄人：</td><td><input name="name" type="text" maxlength="20" id="name_input"></td></tr>
<tr><td>&nbsp;日期：</td><td><input name="date" type="date" id="date_input"><input name="account_id" type="hidden" value="<?php echo $account_id; ?>"></td></tr>
<tr><td>&nbsp;時間：</td><td><input name="time" type="time" id="time_input"><input name="btime" type="hidden" id="btime_input" value=""></td></tr>
<tr><td colspan="2"><input id="submit_input" type="submit" value="送　　出　　紀　　錄" style="text-align:center;color:lime;height:40px"></td></tr>
</tbody></table>
</form>
</div>
<div style="width:<?php echo $width_div ; ?>;margin:auto">
<?php
if ($remain_sum<0){
	echo '<h1 style="color:#ff0000;text-align:center;">當前處於負債中！</h1>';
}else{
	if ($remain_sum==0){
		$remain_sum=1;
	}
	if ($day_cost>0){
		$day_cost='<span style="color:lime">＋'.$day_cost.$acu.'（今天：＋'.round($day_cost,1).$acu.'）</span>';
	}else{
		$day_cost=abs($day_cost);
		$day_cost='<span style="color:red">－'.$day_cost.$acu.'（今天：－'.round($day_cost,1).$acu.'）</span>';
	}
	if ($week_cost>0){
		$week_cost='<span style="color:lime">＋'.$week_cost.$acu.'（平均：＋'.round($week_cost/7,1).$acu.'）</span>';
	}else{
		$week_cost=abs($week_cost);
		$week_cost='<span style="color:red">－'.$week_cost.$acu.'（平均：－'.round($week_cost/7,1).$acu.'）</span>';
	}
	if ($moon_cost>0){
		$moon_cost='<span style="color:lime">＋'.$moon_cost.$acu.'（平均：＋'.round($moon_cost/30,1).$acu.'）</span>';
	}else{
		$moon_cost=abs($moon_cost);
		$moon_cost='<span style="color:red">－'.$moon_cost.$acu.'（平均：－'.round($moon_cost/30,1).$acu.'）</span>';
	}
	echo '<p id="status_panel" style="color:white;text-align:center;display:'.$hidden_status.'">日平衡：'.$day_cost.'<br>';
	echo '週平衡：'.$week_cost.'<br>';
	echo '月平衡：'.$moon_cost.'</p>';
}

echo '<canvas id="canvas" height="300px" width="500px" style="display:'.$hidden_status.'"></canvas>';

switch($orderby){
	case 0:
		$sql_orderby=" ORDER BY --id DESC";
	break;
	case 1:
		$sql_orderby=" ORDER BY -id DESC";
	break;
	case 2:
		$sql_orderby=" ORDER BY --money DESC";
	break;
	case 3:
		$sql_orderby=" ORDER BY -money DESC";
	break;
	case 4:
		$sql_orderby=" ORDER BY --time DESC";
	break;
	case 5:
		$sql_orderby=" ORDER BY -time DESC";
	break;
	default:
		$sql_orderby=" ORDER BY --id DESC";
}

$query = "SELECT * FROM ${sqlpre}_record WHERE id>0 AND account_id='$account_id' $sql_orderby";
$query2 = "SELECT count(*) FROM ${sqlpre}_record WHERE id>0 AND account_id='$account_id' $sql_orderby";

$total_resultx = mysqli_query($con,$query2) or die("Query failed : " . mysqli_error($con)); 

$total = mysqli_fetch_row($total_resultx); 

$per = 15; 
$totalpage = ceil($total["0"]/$per); //總頁數

if ($totalpage=='0'){
	$totalpage='1';
}

if($page>$totalpage){
	$page=$totalpage;
}

$startrow = ($page-1)*$per; //每頁起始資料序號
$endrow = $startrow+$per;

$result = mysqli_query($con,$query) or die("Query failed : " . mysqli_error($con)); 

$orderby_array=Array('新增時間新到舊','新增時間舊到新','金額小到大','金額大到小','時間新到舊','時間舊到新');

echo '<div style="margin-bottom:10px;text-align:center;color:#FFFFFF;"><select id="Select2" style="border: 1px solid #C5E2FF;box-shadow:inset 0px 1px 6px #ECF3F5;outline: 0;width: 120px;height: 20px;background: rgba(0,0,0,0);color: #FFFFFF;" onChange="var Select2= document.getElementById(\'Select2\').options[document.getElementById(\'Select2\').selectedIndex];window.location=\'?type='.$type.'&page='.$page.'&account='.$account_id.'&orderby=\' + Select2.value;">';
for ($i=0;$i<count($orderby_array);$i++){
	if ($i==$orderby){
		echo '<option style="background-color:#0B143C;color:#FF0000;" selected="selected" value="'.$i.'">'.$orderby_array[$i].'</option>'."\n";
	}else{
		echo '<option style="background-color:#0B143C;color:#FF0000;" value="'.$i.'">'.$orderby_array[$i].'</option>'."\n";
	}
}
echo '</select>（<select id="Select1" style="border: 1px solid #C5E2FF;box-shadow:inset 0px 1px 6px #ECF3F5;outline: 0;width: 80px;height: 20px;background: rgba(0,0,0,0);color: #FFFFFF;" onChange="var Select1= document.getElementById(\'Select1\').options[document.getElementById(\'Select1\').selectedIndex];window.location=\'?type='.$type.'&orderby='.$orderby.'&account='.$account_id.'&page=\' + Select1.value;">';
for ($i=1;$i<=$totalpage;$i++){
	if ($i==$page){
		echo '<option style="background-color:#0B143C;color:#FF0000;" selected="selected" value="'.$i.'">第&nbsp;'.$i.'&nbsp;頁</option>'."\n";
	}else{
		echo '<option style="background-color:#0B143C;color:#FF0000;" value="'.$i.'">第&nbsp;'.$i.'&nbsp;頁</option>'."\n";
	}
}

echo '</select>';
echo '&nbsp;／'.$totalpage.'&nbsp;頁）</div>';

$d=1;
while ($line = mysqli_fetch_assoc($result)) {
	if ($d>$startrow and $d<=$endrow){
		$id=$line['id'];
		echo '<table class="table_css2" style="width:100%"><tbody><tr><td style="width:58px;">ID</td><td style="width:120px;">金額</td><td>餘額<a href="action.php?mod=del&del_id='.$id.'" style="color:white;text-decoration:underline;float:right;text-align:right" onclick=\'ok=window.confirm("確定刪除嗎？\n之後無法復原！"); if(ok) { } else { return false; }\' target="Ix">刪除</a></td></tr>';
		$money=$line['money'];
		if ($money>0){
			$money='<span style="color:#00FF00">＋'.$money.$acu.'</span>';
		}else{
			$money='<span style="color:#FF0000">－'.substr($money,1).$acu.'</span>';
		}
		$title=$line['title'];
		$content=$line['content'];
		$name=$line['name'];
		$time=date('H:i:s l Y/m/d',$line['time']);
		$cal_time=date(time()-intval($line['time']));
		switch($cal_time){
			case $cal_time>=31557600:
				$cal_time_year=floor($cal_time/31557600);
				$time='<span style="color:#FFF700" id="record'.$d.'">'.$cal_time_year.' 年前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time_year.' 年前 - '.$time.'\');">詳細時間</span></span>';
			break;
			case $cal_time>=2592000:
				$cal_time_moon=floor($cal_time/2592000);
				$time='<span style="color:#FF00E6" id="record'.$d.'">'.$cal_time_moon.' 月前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time_moon.' 月前 - '.$time.'\');">詳細時間</span></span>';
			break;
			case $cal_time>=604800:
				$cal_time_week=floor($cal_time/604800);
				$time='<span style="color:#9100FF" id="record'.$d.'">'.$cal_time_week.' 週前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time_week.' 週前 - '.$time.'\');">詳細時間</span></span>';
			break;
			case $cal_time>=86400:
				$cal_time_day=floor($cal_time/86400);
				$time='<span style="color:#0044FF" id="record'.$d.'">'.$cal_time_day.' 日前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time_day.' 日前 - '.$time.'\');">詳細時間</span></span>';
			break;
			case $cal_time>=3600:
				$cal_time_hour=floor($cal_time/3600);
				$time='<span style="color:#00FFF2" id="record'.$d.'">'.$cal_time_hour.' 時前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time_hour.' 時前 - '.$time.'\');">詳細時間</span></span>';
			break;
			case $cal_time>=60:
				$cal_time_min=floor($cal_time/60);
				$time='<span style="color:#00FFF2" id="record'.$d.'">'.$cal_time_min.' 分前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time_min.' 分前 - '.$time.'\');">詳細時間</span></span>';
			break;
			case $cal_time>=0:
				$time='<span style="color:#00FFF2" id="record'.$d.'">'.$cal_time.' 秒前 - <span onclick="set_text(\'record'.$d.'\',\''.$cal_time.' 秒前 - '.$time.'\');">詳細時間</span></span>';
			break;
			default:
				$time='<span style="color:#D60AAA" id="record'.$d.'">預定 - <span onclick="set_text(\'record'.$d.'\',\'預定 - '.$time.'\');">詳細時間</span></span>';
		}
		$remain_sum=$line['remain_sum'];
		
		$short_content=$content;
		if (strlen($content)>120){
			$short_content=substr($content,0,120).'……<span style="color:#FFF98F" onclick="set_text(\'content'.$d.'\',\''.$content.'\');">詳情</span>';
		}
		
		if ($name==''){
			$name=$default_name;
		}
		
		echo '<tr><td>'.$id.'</td><td>'.$money.'</td><td>'.$remain_sum.$acu.'</td></tr>'."\n";
		echo '<tr><td>時間：</td><td colspan="2">'.$time.'</td></tr>'."\n";
		echo '<tr><td>標題：</td><td colspan="2">'.$title.'</td></tr>'."\n";
		echo '<tr><td>紀錄者：</td><td>'.$name.'</td><td>'.$line['ip'].'</td></tr>'."\n";
		echo '<tr><td>內容：</td><td colspan="2"><span id="content'.$d.'">'.$short_content.'</span></td></tr>'."\n";
		echo '</tbody></table>';
	}
	$d++;
}
echo '</tbody></table>';

echo '<div class="page_button2" style="margin:10px;margin:auto;text-align:center">';
if ($page>8){
	echo '<a href="'."?type=$type&orderby=$orderby&account=$account_id&page=1\">最前頁</a>\n"; 
}
if ($page>1){
	echo '<a href="'."?type=$type&orderby=$orderby&account=$account_id&page=".($page-1)."\">上一頁</a>\n"; 
}
echo '&nbsp;';
$i=1;
if ($page <= 7){
	$x=1;
}elseif ($page > ($totalpage-8)){
	if ($totalpage<15){
		$x=1;
	}else{
		$x=$totalpage-14;
	}
}else{
	if ($totalpage<15){
		$x=1;
	}else{
		$x=-7+$page;
	}
}
$xa_i=0;
for($k=$x; $k<=$totalpage; $k++){
	if ($k!=$page){
		echo '<a href="'."?type=$type&orderby=$orderby&account=$account_id&page=".$k.'">'.$k.'</a>'."\n"; 
	}else{
		echo '<span>'.$k.'</span>'."\n";
	}
	$xa_i=($xa_i+1);
	$i++;
	if ($i>15){
		break;
	}else{
	}
}

if ($page<$totalpage){
	echo '<a href="'."?type=$type&orderby=$orderby&account=$account_id&page=".($page+1)."\">下一頁</a>\n"; 
}
if (($totalpage-$page)>=8 and $xa_i==15){
	echo '<a href="'."?type=$type&orderby=$orderby&account=$account_id&page=".$totalpage."\">最終頁</a>\n"; 
}
echo '</div><br><br><br>';

for ($i=1;$i<=15;$i++){
	$query="SELECT SUM(money) FROM ${sqlpre}_record WHERE time>".(time()-(86400*($i)))." AND time<".(time()-(86400*($i-1)))." AND account_id='$account_id'";
	$result=@mysqli_query($con,$query) or die('SQL語句執行失敗！'.(mysqli_error($con)));
	$line = mysqli_fetch_array($result);
	if ($line[0]==''){
		$chart_money[]='null';
	}else{
		$chart_money[]=(floatval($line[0]));
	}
	
	$query="SELECT remain_sum FROM ${sqlpre}_record WHERE time>".(time()-(86400*($i)))." AND time<".(time()-(86400*($i-1)))." AND account_id='$account_id' ORDER BY --time DESC";
	$result=@mysqli_query($con,$query) or die('SQL語句執行失敗！'.(mysqli_error($con)));
	$line = mysqli_fetch_array($result);
	if ($line[0]==''){
		$chart_total[]='null';
	}else{
		$chart_total[]=(floatval($line[0]));
	}
	
}
$chart_money=array_reverse($chart_money);
$chart_total=array_reverse($chart_total);
function get_time(){
	for($i=0;$i<15;$i++){
		$label[]=date("'m/d'",time()-86400*($i));
	}
	$label=array_reverse($label);
	return implode(',',$label);
}

?>
</div>
<div style="position:fixed;width:100%;right:5px;bottom:3px;"><span style="margin-top:40px;float:right;color:yellow"><strong>Powered ByWBFTeam</strong></span></div>
</div>
<iframe id="Ix" name="Ix" src="about:blank" style="display:none;width:0px;height:0px"></iframe>
<script>
document.getElementById("menu").style.display="";
var slideout = new Slideout({
    'panel': document.getElementById('panel'),
    'menu': document.getElementById('menu'),
    'padding': 80,
    'tolerance': 70
});
var lineChartData = {
	labels : [<?php echo get_time(); ?>],
	datasets : [
		{
			label: "日平衡",
			fillColor : "rgba(14,0,0,0.2)",
			strokeColor : "rgba(255,0,0,1)",
			pointColor : "rgba(255,100,100,1)",
			pointStrokeColor : "#808080",
			pointHighlightFill : "#FF0000",
			pointHighlightStroke : "rgba(255,255,255,1)",
			data : [<?php echo implode(',',$chart_money); ?>]
		},
		{
			label: "總金額",
			fillColor : "rgba(0,0,14,0.2)",
			strokeColor : "rgba(0,0,255,1)",
			pointColor : "rgba(100,100,255,1)",
			pointStrokeColor : "#808080",
			pointHighlightFill : "#0000FF",
			pointHighlightStroke : "rgba(255,255,255,1)",
			data : [<?php echo implode(',',$chart_total); ?>]
		}
	]
}
window.onload = function(){
	var ctx = document.getElementById("canvas").getContext("2d");
	window.myLine = new Chart(ctx).Line(lineChartData, {
		scaleLineColor: "rgba(255,255,255,0.8)",
		scaleFontColor: "rgba(255,255,255,0.8)",
		responsive:true
	});
	slideout.disableTouch();
}
</script>
</body>
</html>
<?php exit;?>