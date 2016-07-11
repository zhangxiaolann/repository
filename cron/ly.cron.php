<?php
/* 
互刷留言
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where isly>0 and (nextly<'$now' or nextly IS NULL) and skeyzt=0");
while($row=$rs->fetch()){
	if($config['crontype']){
		$uin=$row['qq'];
		$lycon=$row['lycon'];
		$now=date("Y-m-d-H:i:s");
		$next=date("Y-m-d H:i:s",time()+60*60*1-10);
		@$db->exec("update ".DB_PREFIX."qqs set lastly='$now',nextly='$next' where qid='$qid'");
		include_once "qzone.class.php";
		$rs2 = $db->query("SELECT * FROM ".DB_PREFIX."qqs where skeyzt=0 and isly>0 order by rand() limit 30");
		while($qq=$rs2->fetch()){
			$qid=$qq['qid'];
			$con=get_con($lycon);
			$qzone=new qzone($qq['qq'],0,$qq['skey'],$qq['pc_p_skey'],$qq['cp_p_skey']);
			$qzone->liuyan($uin,$con);
			$row=$qq;
			$msg[]=$qzone->msg[0];
			$msg[]=$qzone->error;
		}
		include_once "mail.php";
	}else{
		$urls[]="{$nurl}ly.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
	}
	print_r($msg);
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