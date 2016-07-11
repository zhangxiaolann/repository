<?php
/* 
互刷礼物
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT qq FROM ".DB_PREFIX."qqs where qid='{$qid}' and islw>0 and skeyzt=0 limit 1");
if($row=$rs->fetch()){
	$uin=$row['qq'];
	$now=date("Y-m-d-H:i:s");
	$next=date("Y-m-d H:i:s",time()+60*60*1-10);
	@$db->exec("update ".DB_PREFIX."qqs set lastlw='$now',nextlw='$next' where qid='$qid'");
	include_once "qzone.class.php";
	$rs2 = $db->query("SELECT * FROM ".DB_PREFIX."qqs where skeyzt=0 and islw>0 order by rand() limit 30");
	while($qq=$rs2->fetch()){
		$qid=$qq['qid'];
		$con=get_con();
		$qzone=new qzone($qq['qq'],0,$qq['skey']);
		$qzone->gift($uin,$con);
		$row=$qq;
		$msg[]=$qzone->msg[0];
		$msg[]=$qzone->error;
	}
	print_r($msg);
	include_once "mail.php";
	exit('Ok!');
}else{
	exit('Qid Error!');
}