<?php

error_reporting(0);
function get_curl($url, $post, $referer, $cookie, $header, $ua, $nobaody=0)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if ($referer) {
		curl_setopt($ch, CURLOPT_REFERER, "http://m.qzone.com/infocenter?g_f=");
	}
	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
	}
	else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080");
	}
	if ($nobaody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
function getGTK($skey){
		$len = strlen($skey);
		$hash = 5381;
		for($i = 0; $i < $len; $i++){
			$hash += ((($hash << 5) & 0x7fffffff) + ord($skey[$i])) & 0x7fffffff;
			$hash&=0x7fffffff;
		}
		return $hash & 0x7fffffff;//计算g_tk
}

header("Content-type: text/html; charset=utf-8"); 
$uin = $_GET["uin"];

$skey = $_GET["skey"];
$pc_p_skey = $_GET["pc_p_skey"];
$touin = $_GET["touin"];
if(!$uin||!$skey||!$touin)exit;
$gtk=getGTK($pc_p_skey);
$cookie = 'pt2gguin=o' . $uin . '; uin=o' . $uin . '; skey=' . $skey . '; p_uin=o'. $uin .'; p_skey='.$pc_p_skey.';';
$url='http://w.qzone.qq.com/cgi-bin/tfriend/friend_delete_qqfriend.cgi?g_tk='.$gtk;
$post='uin='.$uin.'&fupdate=1&num=1&fuin='.$touin.'&qzreferrer=http://user.qzone.qq.com/'.$uin.'/myhome/friends';
$data=get_curl($url,$post,0,$cookie);
preg_match('/callback\((.*?)\)\;/is',$data,$json);
$arr=json_decode($json[1],true);
if(@array_key_exists('code',$arr) && $arr['code']==0) {
	if($arr['data']['ret']==0) exit('<font color="white" size="1">成功</font>');
	else exit('<font color="white" size="1">失败</font>');
} elseif($arr['code']==-3000) {
	exit("<script language='javascript'>alert('SKEY已失效，请更新SKEY！');</script>");
} else {
	exit('<font color="white" size="1">失败</font>');
}
?>
