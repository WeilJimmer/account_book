/*===================================================
* 
* Account Book PHP Script
*
* Developed Date : 2016/03/03
*
* Powered By : Weil Jimmer
* 
*==================================================*/

function add_item(){
	var panelx=document.getElementById("panel1");
	document.getElementById("form1").reset();
	panelx.style.display="block";
	slideout.toggle();
}
function close_panel(){
	var panelx=document.getElementById("panel1");
	panelx.style.display="none";
}
function money_check(){
	var money_str=money_input.value.toString();
	if (money_str.length>12 || money_str=="" || money_str=="0"){
		money_input.style.borderColor="#FF0000";
		return;
	}
	if (isNaN(money_str)){
		money_input.style.borderColor="#FF0000";
		return;
	}
	if (parseFloat(money_str)==0){
		money_input.style.borderColor="#FF0000";
		return;
	}
	money_input.value=parseFloat(money_str);
	money_input.style.borderColor="";
}
function submit_form(){
	var panelx=document.getElementById("panel1");
	var money_input=document.getElementById("money_input");
	var title_input=document.getElementById("title_input");
	var date_str=document.getElementById("date_input").value;
	var time_str=document.getElementById("time_input").value;
	var btime_input=document.getElementById("btime_input");
	
	var money_str=money_input.value.toString();
	if (isNaN(money_str)){
		money_input.style.borderColor="#FF0000";
		alert("金額有誤！請重新輸入！");
		return false;
	}
	if (parseFloat(money_str)==0){
		alert("金額有誤！請重新輸入！不可為0！");
		return false;
	}
	if (money_str.length>12 || money_str=="" || money_str=="0"){
		money_input.style.borderColor="#FF0000";
		alert("金額有誤！請重新輸入！長度不可大於12或小於0");
		return false;
	}
	if (title_input.value.toString().length==0){
		title_input.style.borderColor="#FF0000";
		alert("標題必填，不可留空。");
		return false;
	}
	if (date_str!=undefined || time_str!=undefined){
		var date_str_array = date_str.split("-");
		if ((date_str!=undefined && date_str!='') && (time_str!=undefined && time_str!='')){
			btime_input.value=date_str_array[1] + "/" + date_str_array[2] + "/" + date_str_array[0] + " " + time_str + ":00";
		}else if ((date_str==undefined || date_str=='') && (time_str!=undefined && time_str!='')){
			btime_input.value=padLeft((new Date()).getMonth() + 1,2) + "/" + padLeft((new Date()).getDate(),2) + "/" + (new Date()).getFullYear() + " " + time_str + ":00";
		}else if ((date_str!=undefined && date_str!='') && (time_str==undefined || time_str=='')){
			btime_input.value=date_str_array[1] + "/" + date_str_array[2] + "/" + date_str_array[0] + " " + padLeft((new Date()).getHours(),2) + ":" + padLeft((new Date()).getMinutes(),2) + ":" + padLeft((new Date()).getSeconds(),2);
		}
	}
	panelx.style.display="none";
	return true;
}
function set_text(objid,text){
	document.getElementById(objid).innerText=document.getElementById(objid).innerText=text;
}
function button_toggle(){
	slideout.toggle();
	if(side_enalbe){
		side_enalbe=false;
		document.getElementById('panel').style.overflowY='';
	}else{
		side_enalbe=true;
		document.getElementById('panel').style.overflowY='scroll';
	}
}
function show_status(){
	if (document.getElementById('canvas').style.display==""){
		document.getElementById('canvas').style.display='none';
		document.getElementById('status_panel').style.display='none';
	}else{
		document.getElementById('canvas').style.display='';
		document.getElementById('status_panel').style.display='';
	}
}
function padLeft(str,lenght){
	str=str.toString();
	if(str.length >= lenght){
		return str;
	}else{
		return padLeft("0" + str,lenght); 
	}
}
