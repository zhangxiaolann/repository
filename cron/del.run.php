<?php
/* 
删留言  &  删说说
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where qid='{$qid}' and (isdelshuo>0 or isdelll>0) and skeyzt=0 limit 1");
if($row=$rs->fetch()){
	$uin=$row['qq'];
	$sid=$row['sid'];
	$skey=$row['skey'];
	$pc_p_skey=$row['pc_p_skey'];
	$cp_p_skey=$row['cp_p_skey'];
	$next=date("Y-m-d H:i:s",0);
	$next=date("Y-m-d H:i:s",time()+60*1-10);
	
	include_once "qzone.class.php";
	$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
	$sql='';
	if($row['isdelshuo']==2){
		$sql.="lastdelshuo='$now',";
		$qzone->shuodel('pc');
	}elseif($row['isdelshuo']==1){
		$sql.="lastdelshuo='$now',";
		$qzone->shuodel();
	}
		
	if($row['isdelll']==1){
		$sql.="lastdelll='$now',";
		$qzone->delll();
	}
	@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextdelshuo='$next' where qid='$qid'");
	include_once "mail.php";
	exit('Ok!');
}else{
	exit('Qid Error!');
}