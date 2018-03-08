<?php
if(!defined('IN_CRONLITE'))exit();
$qq=daddslashes($_GET['qq']);
$content=daddslashes($_GET['content']);


$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	exit('{"code":0,"msg":"你只能操作自己的QQ哦！"}');
}
if ($row['status2']!=1) {
	exit('{"code":0,"msg":"SKEY已过期"}');
}

$content=str_replace('｜','|',$content);
$array=explode('|',$content);

if(!$_SESSION['nick_num']) $_SESSION['nick_num']=0;
if($_SESSION['nick_num'] >= count($array)){
		$_SESSION['nick_num']=0;
}
$nick=$array[$_SESSION['nick_num']];

$cookie='pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$row["skey"].'; p_skey='.$row["pskey"].'; p_uin=o0'.$qq.';';

$url="http://w.qzone.qq.com/cgi-bin/user/cgi_apply_updateuserinfo_new?g_tk=".getGTK($row["pskey"]);
$data="qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fv6%2Fsetting%2Fprofile%2Fprofile.html%3Ftab%3Dbase&nickname=".urlencode($nick)."&emoji=&sex=1&birthday=2015-01-01&province=0&city=PAR&country=FRA&marriage=6&bloodtype=5&hp=0&hc=PAR&hco=FRA&career=&company=&cp=0&cc=0&cb=&cco=0&lover=&islunar=0&mb=1&uin=".$qq."&pageindex=1&nofeeds=1&fupdate=1&format=json";
$return=get_curl($url,$data,$url,$cookie);
$arr=json_decode($return,true);
if(@array_key_exists('code',$arr) && $arr['code']==0){
	$_SESSION['nick_num']++;
	exit('{"code":1,"msg":"更换昵称成功","nick":"'.$nick.'"}');
}elseif($arr['code']==-3000){
	exit('{"code":0,"msg":"SKEY已失效"}');
}else{
	exit('{"code":0,"msg":"'.$arr['message'].'"}');
}