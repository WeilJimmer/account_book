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

//資料庫連接主機位置。
$sqlhost='127.0.0.1';

//資連庫連接用戶名稱。
$sqluser='root';

//資連庫連接用戶密碼。
$sqlpassword='';

//連接資連庫名稱。
$sqldb='account_book';

//資連庫表前驟。
$sqlpre='ab';

//網站名稱。
$title_name='Weil的帳本';

//預設紀錄者(紀錄者為空時所顯示的字串)
$default_name='System';

//帳本陣列
$account_book_array=Array('帳本A','帳本B','帳本C');

//金錢單位
$account_unit_array=Array('元','元','元');

//強制HTTPS加密連線
$force_https=false;

//設定時區台北+8
@ini_set('date.timezone','Asia/Taipei');

?>