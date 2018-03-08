<?php
 /*
　* 提取群成员
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="提取群成员";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">提取群成员</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">';

if($islogin==1){
$qq=daddslashes($_GET['qq']);
if(!$qq) {
	showmsg('参数不能为空！');
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
}
if ($row['status']!=1) {
	showmsg('SKEY已过期！');
}
if ($row['status2']!=1) {
	showmsg('superkey已过期！');
}
$skey=$row['skey'];
$pskey=$row['pskey'];
$superkey=$row['superkey'];

$gtk = getGTK($skey);
$cookie='pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$skey.'; p_uin=o0'.$qq.'; p_skey='.$pskey.';';
$ua='Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
$url='http://qun.qzone.qq.com/cgi-bin/get_group_list?callbackFun=_GetGroupPortal&uin='.$qq.'&ua=Mozilla%2F5.0%20(Windows%20NT%206.3%3B%20WOW64%3B%20rv%3A25.0)%20Gecko%2F20100101%20Firefox%2F25.0&random=0.946546206453239&g_tk='.$gtk;
$data = get_curl($url,0,'http://qun.qzone.qq.com/group',$cookie,0,$ua);
preg_match('/_GetGroupPortal_Callback\((.*?)\)\;/is',$data,$json);
$arr = json_decode($json[1],true);
//print_r($arr);exit;
if (!$arr) {
	showmsg('QQ群列表获取失败！');
}elseif ($arr["code"] == -3000) {
	showmsg('SKEY已过期！');
}

if(!$cookie_qun=$_SESSION[$qq.'_cookie_qun']){
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

if(isset($_GET['groupid'])) {
	$groupid=daddslashes($_GET['groupid']);
	//$url='http://qun.qzone.qq.com/cgi-bin/get_group_member?callbackFun=_GroupMember&uin='.$qq.'&groupid='.$groupid.'&neednum=1&r=0.973228807809788&g_tk='.$gtk.'&ua=Mozilla%2F5.0%20(Windows%20NT%206.3%3B%20WOW64%3B%20rv%3A25.0)%20Gecko%2F20100101%20Firefox%2F25.0&ptlang=2052';
	//$data = get_curl($url,0,'http://qun.qzone.qq.com/group',$cookie,0,$ua);
	$url='http://qun.qq.com/cgi-bin/qun_mgr/search_group_members';
	$post='gc='.$groupid.'&st=0&end=5000&sort=0&bkn='.$gtk_qun;
	$data = get_curl($url,$post,'http://qun.qq.com/member.html',$cookie_qun,0,$ua);
	//preg_match('/_GroupMember_Callback\((.*?)\)\;/is',$data,$json);
	$arrs = json_decode($data,true);
	//print_r($arrs);exit;
	if (!$arrs) {
		showmsg('QQ群成员获取失败！');
	}elseif ($arrs["ec"] == 1) {
		showmsg('SKEY已过期！');
	}elseif ($arrs["ec"]!=0){
		showmsg('QQ群成员获取失败！');
	}
}
?>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">提取群成员</h3>
	</div>
	<div class="panel-body box" align="left">
		<input type="hidden" name="cookie_qun" value="<?php echo $cookie_qun ?>">
		<form action="index.php" method="GET">
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">QQ群列表</div>
		<input type="hidden" name="mod" value="group">
		<input type="hidden" name="qq" value="<?php echo $qq ?>">
		<select name="groupid" class="form-control">
			<?php
			foreach($arr['data']['group'] as $row) {
				echo '<option value="'.$row['groupid'].'" '.($groupid==$row['groupid']?'selected="selected"':NULL).'>'.$row['groupid'].'_'.$row['groupname'].'</option>';
			}
			?>
			</select>
		</div></div>
		<div class="form-group">
		<input type="submit" class="btn btn-primary btn-block" value="提取群成员">
		</div>
		</form>
	</div>
</div>
<?php if($arrs){ ?>
<div class="panel panel-success">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">群成员列表</h3>
	</div>
	<table class="table table-bordered box">
		<tbody>
			<tr>
			<td><span style="color:silver;"><b>ＱＱ</b></span></td>
			<td><span style="color:silver;"><b>昵称</b></span></td>
			</tr>
			<?php
			echo '<tr><td colspan="2" align="center"><a href="index.php?mod=output&qq='.$qq.'&groupid='.$groupid.'&type=group">导出群成员列表为TXT</a></td></tr>';
			foreach($arrs['mems'] as $row) {
			echo '<tr><td uin="'.$row['uin'].'"><a href="tencent://message/?uin='.$row['uin'].'&amp;Site=&amp;Menu=yes">'.$row['uin'].'</a></td><td>'.$row['nick'].'</td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<?php
}
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>