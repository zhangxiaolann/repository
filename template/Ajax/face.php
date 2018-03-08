<?php
if(!defined('IN_CRONLITE'))exit();
$qq=daddslashes($_GET['qq']);
$vip=daddslashes($_GET['vip']);


$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	exit('{"code":0,"msg":"你只能操作自己的QQ哦！"}');
}
if ($row['status2']!=1) {
	exit('{"code":0,"msg":"SKEY已过期"}');
}

if($vip==1){
	$data=file_get_contents(ROOT.'qq/face_dynamic.txt');
	$type='dynamic';
}else{
	$data=file_get_contents(ROOT.'qq/face_static.txt');
	$type='static';
}
$array=explode(',',$data);
$rand=array_rand($array,1);
$array=explode('||',$array[$rand]);
$id=$array[0];
$name=$array[1];

$cookie="uin=o0".$qq."; skey=".$row["skey"].";";
$data=get_curl("http://face.qq.com/client/webface.php","item_id=$id&size=1&cmd=set_".$type."_face&g_tk=".getGTK2($row["skey"])."&callback=callback",'http://face.qq.com/ajax.proxy.html',$cookie);
preg_match('/callback\((.*?)\)\;/is',$data,$json);
$arr=json_decode($json[1],true);
if(array_key_exists('result',$arr) && $arr['result']==0){
	exit('{"code":1,"msg":"更换头像成功","id":"'.$id.'","name":"'.$name.'"}');
}elseif($arr['result']==1002){
	exit('{"code":0,"msg":"需要QQ会员"}');
}elseif($arr['result']==1001){
	exit('{"code":-1,"msg":"SKEY已失效"}');
}else{
	exit('{"code":0,"msg":"未知[code='.$arr['result'].']"}');
}

function getGTK2($skey){
	$salt = 5381;
	$md5key = 'tencentQQVIP123443safde&!%^%1282';
	$hash = array();
	$hash[] = ($salt << 5);
	for($i = 0; $i < strlen($skey); $i ++)
	{
		$ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
		$ASCIICode = hexdec(bin2hex($ASCIICode));
		$hash[] = (($salt << 5) + $ASCIICode);
		$salt = $ASCIICode;
	}
	$md5str = md5(implode($hash) . $md5key);
	return $md5str;
}