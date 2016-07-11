<?php
/* 
赞
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where qid='{$qid}' and iszan>0 and skeyzt=0 limit 1");
if($row=$rs->fetch()){ 
	$uin=$row['qq'];
	$sid=$row['sid'];
	$skey=$row['skey'];
	$pc_p_skey=$row['pc_p_skey'];
	$cp_p_skey=$row['cp_p_skey'];
	$do=$row['iszan'];
	$next=date("Y-m-d H:i:s",time()+$row['zanrate']);
	@$db->exec("update ".DB_PREFIX."qqs set lastzan='$now',nextzan='$next' where qid='$qid'");
	include_once "qzone.class.php";
	$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
	if($do==1){
		$qzone->like(1);
	}elseif($do==2){
		$qzone->like(2);
	}elseif($do==3){
		$qzone->like(3);
	}elseif($do==4){
		$qzone->newpclike();
	}elseif($do==5){
		$qzone->newpclike2();
	}
	include_once "mail.php";
	exit('Ok!');
}else{
	exit('Qid Error!');
}
