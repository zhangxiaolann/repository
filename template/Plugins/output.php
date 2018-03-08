<?php
//导出任务为TXT文件
if(!defined('IN_CRONLITE'))exit();

if($_GET['type']=='group') {
	$qq=daddslashes($_GET['qq']);
	$groupid=daddslashes($_GET['groupid']);
	if(!$qq || !$groupid) {
		exit('Something is blank');
	}
	$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
	if($row['uid']!=$uid && $isadmin==0) {
		exit('No permission');
	}
	if(!$cookie_qun=$_SESSION[$qq.'_cookie_qun']){
		$skey=$row['skey'];
		$superkey=$row['superkey'];
		include ROOT.'qq/qqsign.class.php';
		$qzone=new qqsign($qq,$sid,$skey);
		$cookie_qun=$qzone->qqqun($superkey);
		if(!$cookie_qun){
			showmsg('superkey已失效！');
		}
		$_SESSION[$qq.'_cookie_qun']=$cookie_qun;
	}
	preg_match('/skey=(.{10});/',$cookie_qun,$skey_qun);
	$gtk_qun = getGTK($skey_qun[1]);
	$ua='Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
	$url='http://qun.qq.com/cgi-bin/qun_mgr/search_group_members';
	$post='gc='.$groupid.'&st=0&end=5000&sort=0&bkn='.$gtk_qun;
	$data = get_curl($url,$post,'http://qun.qq.com/member.html',$cookie_qun,0,$ua);
	$arrs = json_decode($data,true);
	if (!$arrs) {
		exit('Failed');
	}elseif ($arrs["ec"] == 1) {
		exit('login error');
	}elseif ($arrs["ec"]!=0){
		exit('Failed');
	}
	$file_name='group_member_'.$groupid.'.txt';
	$output='';
	foreach($arrs['mems'] as $row) {
		$output.=$row['uin']."\r\n";
	}
} else {
	if(isset($_GET['sys']))
	{
		$sysid=daddslashes($_GET['sys']);
		$rs=$DB->query("SELECT * FROM ".DBQZ."_wzjob WHERE uid='{$uid}' and sysid='{$sysid}' order by jobid desc");
		$file_name='output_sys'.$sysid.'_'.date("YmdHis").'.txt';
	} else {
		$rs=$DB->query("SELECT * FROM ".DBQZ."_wzjob WHERE uid='{$uid}' order by jobid desc");
		$file_name='output_'.date("YmdHis").'.txt';
	}
	$output='';
	while($myrow = $DB->fetch($rs))
	{
		$output.=$myrow['url']."\r\n";
	}
}
$file_size=strlen($output);
header("Content-Description: File Transfer");
header("Content-Type:application/force-download");
header("Accept-Ranges: bytes");
header("Content-Length: {$file_size}");
header("Content-Disposition:attachment; filename={$file_name}");
print($output);
?>