<?php
/* 
扣群签到
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where (isqunqd>0 or isqunwenwenqd>0) and (nextqunqd<'$now' or nextqunqd IS NULL) and skeyzt=0");
while($row=$rs->fetch()){ 
	if($config['crontype']){
		$qid=$row['qid'];
		$uin=$row['qq'];
		$sid=$row['sid'];
		$skey=$row['skey'];
		$qunqdcon=$row['qunqdcon'];
		$next=date("Y-m-d H:i:s",time()+60*60*8-10);
		
		include_once "qzone.class.php";
		$qzone=new qzone($uin,$sid,$skey);
		$sql='';
		if($row['isqunqd']){
			$sql.="lastqunqd='$now',";
			$qzone->qunqd($qunqdcon);
		}
		if($row['isqunwenwenqd']){
			$sql.="lastqunwenwenqd='$now',";
			$qzone->qunwenwenqd();
		}
		@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextqunqd='$next' where qid='$qid'");
		include_once "mail.php";
		
	}else{
		$urls[]="{$nurl}qunqd.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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
