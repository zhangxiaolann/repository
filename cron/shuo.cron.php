<?php
/* 
定时说说
*/
set_time_limit(0);
include_once "conn.php";
$n=is_numeric($_GET['n'])?$_GET['n']:exit('No Net!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where isshuo>0 and (nextshuo<'$now' or nextshuo IS NULL) and skeyzt=0");
while($row=$rs->fetch()){ 
	if($config['crontype']){
		$qid=$row['qid'];
		$uin=$row['qq'];
		$sid=$row['sid'];
		$skey=$row['skey'];
		$pc_p_skey=$row['pc_p_skey'];
		$cp_p_skey=$row['cp_p_skey'];
		$do=$row['isshuo'];
		$next=date("Y-m-d H:i:s",time()+60*$row['shuorate']-10);
		@$db->exec("update ".DB_PREFIX."qqs set lastshuo='$now',nextshuo='$next' where qid='$qid'");

		$sname=$row['shuophone'];
		$con=get_con($row['shuoshuo']);
		$pic=urlencode($row['shuopic']);
		if($pic==1){
			$row=file('../data/pic.txt');
			shuffle($row);
			$pic=$row[0];
			$type=0;
		}else{
			$type=stripos('z'.$pic,'http')?0:1;
		}
		$pic=trim($pic);
		include_once "qzone.class.php";
		$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
		if($do==2){
			$qzone->shuo('pc',$con,$pic,$type,$sname);
		}else{
			$qzone->shuo(0,$con,$pic,$type,$sname);
		}
		include "mail.php";
	}else{
		$urls[]="{$nurl}shuo.run.php?cron=".$_GET['cron']."&qid={$row['qid']}{$look}";
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
