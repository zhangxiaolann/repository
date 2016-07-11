<?php
/* 
互刷礼物
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where islw>0 and (nextlw<'$now' or nextlw IS NULL) and skeyzt=0");
while($row=$rs->fetch()){
	if($config['crontype']){
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
		include_once "mail.php";
		print_r($msg);
	}else{
		$urls[]="{$nurl}lw.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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