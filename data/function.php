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

function write_last_pos($account_id,$orderby){
	$fp=@fopen('./data/lastpos.php','w');
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

//紀錄習慣性儲存位置
//帳本ID
$accound_id_lasttime='.$account_id.';

//排序方法
$orderby_lasttime='.$orderby.';

?>');
		@fclose($fp);
}
?>