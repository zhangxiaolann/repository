<?php
/* 
互刷主页赞
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT qq FROM ".DB_PREFIX."qqs where qid='{$qid}' and iszyzan>0 and skeyzt=0 limit 1");
if($row=$rs->fetch()){
	$uin=$row['qq'];
	$now=date("Y-m-d-H:i:s");
	$next=date("Y-m-d H:i:s",time()+60*60*1-10);
	@$db->exec("update ".DB_PREFIX."qqs set lastzyzan='$now',nextzyzan='$next' where qid='$qid'");
	include_once "qzone.class.php";
	$rs2 = $db->query("SELECT * FROM ".DB_PREFIX."qqs where skeyzt=0 order by rand() limit 30");
	while($qq=$rs2->fetch()){
		$qid=$qq['qid'];
		$qzone=new qzone($qq['qq'],$qq['sid'],$qq['skey'],$qq['pc_p_skey'],$qq['cp_p_skey']);
		$qzone->zyzan($uin);
		print_r($qzone->msg);
		print_r($qzone->error);
		$row=$qq;
	}
	include_once "mail.php";
	exit('Ok!');
}else{
	exit('Qid Error!');
}
