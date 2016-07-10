<?php
/* 
扣群签到
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where qid='{$qid}' and (isqunqd>0 or isqunwenwenqd>0) and skeyzt=0 limit 1");
if($row=$rs->fetch()){
	$uin=$row['qq'];
	$sid=$row['sid'];
	$skey=$row['skey'];
	$pc_p_skey=$row['pc_p_skey'];
	$qunqdcon=$row['qunqdcon'];
	$next=date("Y-m-d H:i:s",time()+60*60*8-10);
	include_once "qzone.class.php";
	$qzone=new qzone($uin,$sid,$skey,$pc_p_skey);
	$sql='';
	if($row['isqunqd']){
		$sql.="lastqunqd='{$now}',";
		$qzone->qunqd($qunqdcon);
	}
	if($row['isqunwenwenqd']){
		$sql.="lastqunwenwenqd='{$now}',";
		$qzone->qunwenwenqd();
	}
	@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextqunqd='{$next}' where qid='{$qid}'");
	include_once "mail.php";
	exit('Ok!');
}else{
	exit('Qid Error!');
}