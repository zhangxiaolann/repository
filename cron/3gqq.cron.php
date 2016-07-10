<?php
/* 
挂宠物 & 挂QQ & 牧场签到 & 蓝钻签到
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where (is3gqq>0 or isgcw>0 or ismcqd>0 or isgamevipqd>0) and (next3gqq<'$now' or next3gqq IS NULL) and sidzt=0");
while($row=$rs->fetch()){ 
	if($config['crontype']){
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
	
	}else{
		$urls[]="{$nurl}3gqq.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
	}
}

if(!$config['crontype']){
	if($urls){
		$get=duo_curl($urls);
	}
	if($_GET['get']==1){
		print_r($get);
	}
}
exit('Ok!');