<?php
/* 
删留言  &  删说说
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where (isdelshuo>0 or isdelll>0) and (nextdelshuo<'$now' or nextdelshuo IS NULL) and skeyzt=0");
while($row=$rs->fetch()){
	if($config['crontype']){
		$uin=$row['qq'];
		$sid=$row['sid'];
		$skey=$row['skey'];
		$pc_p_skey=$row['pc_p_skey'];
		$cp_p_skey=$row['cp_p_skey'];
		$next=date("Y-m-d H:i:s",0);
		$next=date("Y-m-d H:i:s",time()+60*1-10);
		
		include_once "qzone.class.php";
		$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
		$sql='';
		if($row['isdelshuo']==2){
			$sql.="lastdelshuo='$now',";
			$qzone->shuodel('pc');
		}elseif($row['isdelshuo']==1){
			$sql.="lastdelshuo='$now',";
			$qzone->shuodel();
		}
		
		if($row['isdelll']==1){
			$sql.="lastdelll='$now',";
			$qzone->delll();
		}
		@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextdelshuo='$next' where qid='$qid'");
		include_once "mail.php";
	}else{
		$urls[]="{$nurl}del.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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