<?php
/* 
挂宠物 & 挂QQ & 牧场签到 & 蓝钻签到
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where qid='{$qid}' and (is3gqq>0 or isgcw>0 or ismcqd>0 or isgamevipqd>0) and sidzt=0 limit 1");
if($row=$rs->fetch()){
	$uin=$row['qq'];
	$sid=$row['sid'];
	$next=date("Y-m-d H:i:s",time()+60*5-10);
	
	include_once "qzone.class.php";
	$qzone=new qzone($uin,$sid);
	$sql='';
	if($row['isgcw']){
		$sql.="lastgcw='$now',";
		$qzone->gcw();
	}
	if($row['is3gqq']){
		$sql.="last3ggq'$now',";
		$qzone->gq();
	}
	if($row['ismcqd']){
		$sql.="lastmcqd='$now',";
		$qzone->mcqd();
	}
	if($row['isgamevipqd']){
		$sql.="lastgamevipqd='$now',";
		$qzone->gamevipqd();
	}
	@$db->exec("update ".DB_PREFIX."qqs set {$sql}next3gqq='$next' where qid='$qid'");
	include_once "mail.php";
	
	exit('Ok!');
}else{
	exit('Qid Error!');
}