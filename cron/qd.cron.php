<?php
/* 
QQ空间签到 & CF签到 & 花腾服务 & 大乐斗签到 & QQ管家签到 & 微云签到 & 3366签到
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where (iscfqd>0 or isqd>0 or isht>0 or isdldqd>0 or isweiyunqd>0 or isqqgjqd>0 or isqd3366>0) and (nextqd<'$now' or nextqd IS NULL) and skeyzt=0");
while($row=$rs->fetch()){ 
	if($config['crontype']){
		$qid=$row['qid'];
		$uin=$row['qq'];
		$sid=$row['sid'];
		$skey=$row['skey'];
		$pc_p_skey=$row['pc_p_skey'];
		$cp_p_skey=$row['cp_p_skey'];
		$qdcon=get_con($row['qdcon']);
		$next=date("Y-m-d H:i:s",time()+60*60*8-10);
		
		include_once "qzone.class.php";
		$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
		if($row['isqd']==2){
			$sql.="lastqd='$now',";
			$qzone->qiandao('pc',$qdcon,10319);
		}elseif($row['isqd']==1){
			$sql.="lastqd='$now',";
			$qzone->qiandao(0,$qdcon,10319);
		}
		
		if($row['iscfqd']) {
			$sql.="lastcfqd='$now',";
			$qzone->cfqd();
		}
		
		if($row['isdldqd']) {
			$sql.="lastdldqd='$now',";
			$qzone->dldqd();
		}
		if($row['isweiyunqd']) {
			$sql.="lastweiyunqd='$now',";
			$qzone->weiyunqd();
		}
		if($row['isqqgjqd']) {
			$sql.="lastqqgjqd='$now',";
			$qzone->qqgjqd();
		}
		if($row['isqd3366']) {
			$sql.="lastqd3366='$now',";
			$qzone->qd3366();
		}
		if($row['isht']==1){
			$sql.="lastht='$now',";
			$qzone->cpflower();
		}elseif($row['isht']==2){
			$sql.="lastht='$now',";
			$qzone->pcflower();
		}
		@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextqd='$next' where qid='$qid'");
		include "mail.php";
		
	}else{
		$urls[]="{$nurl}qd.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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


