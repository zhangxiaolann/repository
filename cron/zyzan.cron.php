<?php
/* 
互刷主页赞
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where iszyzan>0 and (nextzyzan<'$now' or nextzyzan IS NULL) and skeyzt=0");
while($row=$rs->fetch()){
	if($config['crontype']){
		$uin=$row['qq'];
		$next=date("Y-m-d H:i:s",time()+60*60*1-10);
		@$db->exec("update ".DB_PREFIX."qqs set lastzyzan='$now',nextzyzan='$next' where qid='$qid'");
		include_once "qzone.class.php";
		$rs2 = $db->query("SELECT * FROM ".DB_PREFIX."qqs where skeyzt=0 order by rand() limit 30");
		while($qq=$rs2->fetch()){
			$qid=$qq['qid'];
			$qzone=new qzone($qq['qq'],$qq['sid'],$qq['skey'],$qq['pc_p_skey'],$qq['cp_p_skey']);
			$qzone->zyzan($uin);
			$row=$qq;
			print_r($qzone->msg);
			print_r($qzone->error);
			
		}
		include_once "mail.php";
	}else{
		$urls[]="{$nurl}zyzan.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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
