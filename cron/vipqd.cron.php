<?php
/* 
会员签到 & 绿钻签到 & 钱包签到 & 图书签到 & 黄钻签到 & 超扣签到 & 粉钻签到 & 流量豆领取
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where (isvipqd>0 or islz>0 or isqb>0 or ists>0 or isyqd>0 or iscqqd>0 or isfzqd>0 or isvideoqd>0 or isnianvipcj>0 or islld>0) and (nextvipqd<'$now' or nextvipqd IS NULL) and skeyzt=0");
while($row=$rs->fetch()){ 
	if($config['crontype']){
		$qid=$row['qid'];
		$uin=$row['qq'];
		$sid=$row['sid'];
		$skey=$row['skey'];
		$pc_p_skey=$row['pc_p_skey'];
		$cp_p_skey=$row['cp_p_skey'];
		$next=date("Y-m-d H:i:s",time()+60*60*8-10);
		
		include_once "qzone.class.php";
		$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
		$sql='';
		if($row['isvipqd']) {
			$sql.="lastvipqd='$now',";
			$qzone->vipqd();
		}
		if($row['islz']) {
			$sql.="lastlz='$now',";
			$qzone->lzqd();
		}
		if($row['isyqd']) {
			$sql.="lastyqd='$now',";
			$qzone->yqd();
		}
		if($row['isqb']) {
			$sql.="lastqb='$now',";
			$qzone->pqd();
		}
		if($row['ists']){
			$sql.="lastts='$now',";
			$qzone->cpscqd();
		}
		if($row['iscqqd']){
			$sql.="lastcqqd='$now',";
			$qzone->cpcqqd();
		}
		if($row['isfzqd']){
			$sql.="lastfzqd='$now',";
			$qzone->fzqd();
		}
		if($row['isvideoqd']){
			$sql.="lastvideoqd='$now',";
			$qzone->videoqd();
		}
		if($row['isnianvipcj']){
			$sql.="lastnianvipcj='$now',";
			$qzone->nianvipcj();
		}
		if($row['islld']){
			$sql.="lastlld='$now',";
			$qzone->lld();
		}
		@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextvipqd='$next' where qid='$qid'");
		include_once "mail.php";
		
	}else{
		$urls[]="{$nurl}vipqd.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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