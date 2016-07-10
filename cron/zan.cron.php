<?php
/* 
赞
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where zannet='$n' and iszan>0 and (nextzan<'$now' or nextzan IS NULL) and skeyzt=0");
while($row=$rs->fetch()){ 
	if($config['crontype']){
		$qid=$row['qid'];
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
		include "mail.php";
	}else{
		$urls[]="{$nurl}zan.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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
