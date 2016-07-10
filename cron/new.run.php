<?php
/* 
圈说图片 & 转发说说 & 赞 & 评论
*/
set_time_limit(0);
include_once "conn.php";
$qid=is_numeric($_GET['qid'])?$_GET['qid']:exit('No Qid!');
$rs = $db->query("SELECT * FROM ".DB_PREFIX."qqs where qid='{$qid}' and (iszan>0 or isreply>0 or iszf >0 or isqt >0) and skeyzt=0 limit 1");
if($row=$rs->fetch()){
	$uin=$row['qq'];
	$sid=$row['sid'];
	$skey=$row['skey'];
	$pc_p_skey=$row['pc_p_skey'];
	$cp_p_skey=$row['cp_p_skey'];
	//$next=date("Y-m-d H:i:s",time()+60*$row['zanrate']-10);
	$next=date("Y-m-d H:i:s",$row['zanrate']);
	$sql='';
	if($row['iszan']) $sql.="lastzan='$now',";
	if($row['isreply']) $sql.="lastreply='$now',";
	if($row['iszf']) $sql.="lastzf='$now',";
	if($row['isqt']) $sql.="lastqt='$now',";
	@$db->exec("update ".DB_PREFIX."qqs set {$sql}nextzan='$next' where qid='$qid'");

	$zfok='';
	if($row['iszf']){
		$zfs = $db->query("SELECT zfuin,zfdate FROM ".DB_PREFIX."zfdates where uin='$uin'");
		while($zfrow=$zfs->fetch()){
			if(strtotime($zfrow['zfdate'])>time()){
				$zfok.=",".$zfrow['zfuin'];
			}
		}
	}

	include_once "qzone.class.php";
	$qzone=new qzone($uin,$sid,$skey,$pc_p_skey,$cp_p_skey);
	if($shuos=$qzone->getnew()){
		foreach($shuos as $shuo){
			$appid=$shuo['comm']['appid'];
			$typeid=$shuo['comm']['feedstype'];
			$curkey=urlencode($shuo['comm']['curlikekey']);
			$uinkey=urlencode($shuo['comm']['orglikekey']);
			$touin=$shuo['userinfo']['user']['uin'];
			$from=$shuo['userinfo']['user']['from'];
			$abstime=$shuo['comm']['time'];
			$cellid=$shuo['id']['cellid'];
			$qzone->touin=$touin;

			$albumid='';$lloc='';$picheight=50;$picwidth=50;$fauin=$uin;
			if($shuo['original']){
				$albumid=$shuo['original']['cell_pic']['albumid'];
				$lloc=$shuo['original']['cell_pic']['picdata'][0]['lloc'];
			}
			if($shuo['pic']){
				$albumid=$shuo['pic']['albumid'];
				$lloc=$shuo['pic']['picdata'][0]['lloc'];
			}
			if($row['isqt'] && $albumid){
				$qtrs = $db->query("SELECT id FROM ".DB_PREFIX."quanrens where uin='$uin' and albumid='$albumid' limit 1");
				if(!$qtrs->fetch()){
					$url="http://app.photo.qq.com/cgi-bin/app/cgi_annotate_face?g_tk=".$qzone->gtk;
					$post="format=json&uin=$uin&hostUin=$touin&faUin=$fauin&faceid=&oper=0&albumid=$albumid&lloc=$lloc&facerect=10_10_50_50&extdata=&inCharset=GBK&outCharset=GBK&source=qzone&plat=qzone&facefrom=moodfloat&faceuin=$fauin&writeuin=$uin&facealbumpage=quanren&qzreferrer=http://user.qzone.qq.com/$uin/infocenter?via=toolbar";
					$json=$qzone->get_curl($url,$post,'http://user.qzone.qq.com/'.$uin.'/infocenter?via=toolbar',$qzone->pc_cookie);
					$json=mb_convert_encoding($json, "UTF-8", "GBK");
					$arr=json_decode($json,true);
					if(!@array_key_exists('code',$arr)){
						$qzone->error[]="圈{$touin}的图{$albumid}失败，原因：获取结果失败！";
					}elseif($arr['code']==0){
						$qzone->msg[]="圈{$touin}的图{$albumid}成功";
					}else{
						$qzone->error[]="圈{$touin}的图{$albumid}失败，原因：".$arr['message'];
					}
					@$db->exec("INSERT INTO ".DB_PREFIX."quanrens (uin,albumid,addtime) VALUE('$uin','$albumid','$now')");
				}
			}
			
			if($row['iszan']){
				$like=$shuo['like']['isliked'];
				if($like==0){
					if($row['iszan']==2){
						$qzone->pclike($touin,$curkey,$uinkey,$from,$appid,$typeid,$abstime,$cellid);
						//$qzone->newpclike();
						if($qzone->skeyzt) break;
					}else{
						$qzone->cplike($touin,$appid,$uinkey,$curkey);
					}
				}
			}
			if($row['iszf'] && $zfok){
				if(stripos($zfok,$touin)){
					$zfrs = $db->query("SELECT id FROM ".DB_PREFIX."zhuanfas where uin='$uin' and cellid='$cellid' limit 1");
					if(!$zfrs->fetch()){
						if($row['iszf'] == 2){
							$qzone->pczhuanfa(get_con($row['zfcon']),$touin,$cellid);
							if($qzone->skeyzt) break;
						}else{
							$qzone->cpzhuanfa(get_con($row['zfcon']),$touin,$cellid);
							if($qzone->sidzt) break;
						}
						@$db->exec("INSERT INTO ".DB_PREFIX."zhuanfas (uin,cellid,addtime) VALUE('$uin','$cellid','$now')");
					}
				}
			}
			/*if($row['isreply']){
				switch($row['isreply']){
					case 1;
						$qzone->reply('cp',get_con($row['replycon']));
					break;

					case 2;
						$qzone->reply('pc',get_con($row['replycon']));
					break;
				}
			}*/

		}
	}
	include_once "mail.php";
	exit('Ok!');
}else{
	exit('Qid Error!');
}