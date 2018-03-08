<?php
if(!defined('IN_CRONLITE'))exit();
$title="首页";
include_once(TEMPLATE_ROOT."head.php");

if($is_fenzhan==1) $logoname = DBQZ;else $logoname = ''; 
if(!file_exists(ROOT.'images/'.$logoname.'logo.png')) $logoname='';
echo '
<div class="col-md-12" style="margin: 0 auto;max-width:580px;">
<div class="panel panel-primary">
	<div class="panel-body" style="text-align: center;">
		<img src="images/'.$logoname.'logo.png">
	</div>
</div>';

$gg=$conf['gg'];
if(!empty($gg))
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title" align="center">公告栏</h3></div><div class="panel-body"><!--ggS-->'.$gg.'<!--ggE--></font></div>
</div>';

if($islogin==1)
{
echo '<div class="panel panel-primary">
<div class="list-group-item reed" style="background:#64b2ca;"> <h3 class="panel-title" align="center">
<img src="'.(($row['qq'])?'//q1.qlogo.cn/g?b=qq&nk='.$row['qq'].'&s=100&t='.date("Ymd"):'assets/img/user.png').'" &amp;spec=100&amp;url_enc=0&amp;referer=bu_interface&amp;term_type=PC" class="qqlogo" width="100px">
<br><font color="#fff"><b>'.$row['user'].' [UID:'.$row['userid'].']&nbsp;'.usergroup().'</b></font></h3></div>
';
if(OPEN_QQOL==1)echo '
<div class="list-group-item reed"><font color="#808080">
<i class="fa fa-qq"></i>&nbsp;&nbsp;ＱＱ数量<span class="pull-right">'.$row['qqnum'].'个</small>
</font></div>';
if(OPEN_SIGN==1)echo '
<div class="list-group-item reed"><font color="#808080">
<i class="fa fa-tags"></i>&nbsp;&nbsp;签到任务<span class="pull-right">'.$row['signjobnum'].'个</small>
</font></div>';
if(OPEN_CRON==1)echo '
<div class="list-group-item reed"><font color="#808080">
<i class="fa fa-qq"></i>&nbsp;&nbsp;监控任务<span class="pull-right">'.$row['wzjobnum'].'个</small>
</font></div>';
echo '
<div class="list-group-item reed"><font color="#808080">
<i class="fa fa-skype"></i>&nbsp;&nbsp;&nbsp;'.$conf['coin_name'].'<span class="pull-right">'.$row['coin'].'</small>
</font></div>';
echo '
<div class="list-group-item reed"><font color="#808080">
<i class="fa fa-vimeo"></i>&nbsp;&nbsp;会员状态<span class="pull-right">
';
if ($isvip == 1) $vipstatus = '<center>' . $row['vipdate'] . '  到期</center> ';
                    elseif ($isvip == 2) $vipstatus = '<center>永久VIP </center>';
                    else $vipstatus = '<center>非VIP会员</center>';
  echo '
		<center>' . $vipstatus . '</center>
</small>
</font></div>';
if($conf['peie_open'])echo '
<div class="list-group-item reed"><font color="#808080">
<i class="glyphicon glyphicon-tint"></i>&nbsp;&nbsp;ＱＱ配额<span class="pull-right">'.$row['peie'].'个</small>
</font></div>';
echo '
<table class="table table-bordered">
<tbody>
<tr height=50>
	<td><a href="index.php?mod=userinfo" class="btn btn-block btn-info">修改资料</a></td>
	<td><a href="index.php?mod=qqlist" class="btn btn-block btn-success">ＱＱ管理</a></td>
	<td><a href="index.php?mod=invite" class="btn btn-block btn-primary">邀请好友</a></td>
</tr>';
echo '<tr height=50>
	<td><a href="index.php?mod=qd" class="btn btn-block btn-primary">有奖签到</a></td>
        <td><a href="index.php?mod=shop&kind=2" class="btn btn-block btn-warning">自助购买</a></td>
	<td><a href="./?my=loginout" class="btn btn-block btn-danger">安全退出</a></td>
</tr>';
echo '</tbody>
</table>
</div>';
}
else
{
?>
<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">登录你的账号:</h3></div>
<div class="panel-body">
<form action="?" method="get">
<input type="hidden" name="my" value="login">
<div class="input-group">
<span class="input-group-addon">账号</span>
<input type="text" class="form-control" name="user" value="">
</div><br/>
<div class="input-group">
<span class="input-group-addon">密码</span>
<input type="password" class="form-control" name="pass" value="">
</div><br/>
<div class="login-button">
<input type="checkbox" name="ctime" id="ctime" checked="checked" value="2592000" >&nbsp;<label for="ctime">下次自动登录</label>
<a href="index.php?mod=findpwd" class="pull-right" for="ctime">忘记密码？</a><br/><br/>
<button type="submit" class="btn btn-primary btn-block">马上登录</button><br/></form>
<a href="index.php?mod=zhuce2" class="btn btn-success btn-block">注册用户</a><hr/>
<?php if($conf['oauth_open']){
	echo '<div class="text-center col-md-12">';
	$oauth_option=explode("|",$conf['oauth_option']);
	if(in_array('qqdenglu',$oauth_option))echo '<a href="social.php?type=qqdenglu"><img src="assets/img/social/qqdenglu.png"></a>&nbsp;';
	if(in_array('baidu',$oauth_option))echo '<a href="social.php?type=baidu"><img src="assets/img/social/baidu.png"></a>&nbsp;';
	if(in_array('sinaweibo',$oauth_option))echo '<a href="social.php?type=sinaweibo"><img src="assets/img/social/sinaweibo.png"></a>&nbsp;';
	if(in_array('qqweibo',$oauth_option))echo '<a href="social.php?type=qqweibo"><img src="assets/img/social/qqweibo.png"></a>&nbsp;';
	if(in_array('renren',$oauth_option))echo '<a href="social.php?type=renren"><img src="assets/img/social/renren.png"></a>&nbsp;';
	if(in_array('kaixin',$oauth_option))echo '<a href="social.php?type=kaixin"><img src="assets/img/social/kaixin.png"></a>&nbsp;';
	echo '</div>';
}?>
</div>
</div>
</div>
<?php
}
if(OPEN_QQOL==1 && OPEN_WALL==1){
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<a href="index.php?mod=wall">ＱＱ展示</a></h3></div>
<ul align="center" class="list-group" style="list-style:none;">
	<li class="list-group-item"><div class="wrapper2">
		<div id="menubar" class="fix-menu">
			<div class="menu-list">
';
$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE 1 order by id desc limit 15");
while($row = $DB->fetch($rs))
{
	$qq=$row['qq'];
	echo '<a href="index.php?mod=search&q='.$qq.'" target="_blank"><img class="qqlogo" src="//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$qq.'&src_uin='.$qq.'&fid='.$qq.'&spec=100&url_enc=0&referer=bu_interface&term_type=PC" width="80px" height="80px" alt="'.$qq.'" title="'.$qq.'|添加时间:'.$row['time'].' ★点击查看详情★"></a>';
}
echo '</div></div>
			</div></li></ul>
</div>';
}
if(OPEN_CHAT==1){
##交流社区start
$row12=$DB->get_row("select * from ".DBQZ."_chat order by id desc limit 1");
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-commenting"></i>&nbsp;&nbsp;<a href="index.php?mod=chat">交流社区</a></h3></div>';
echo '<div class="panel-body">'; 
if($row12['nr']==''){
echo '还没有友友说话哦 <a href="index.php?mod=chat">聊天</a>';
}else{
if($row12['user']==$gl){ 
echo '<a href="index.php?mod=chat&to='.$row12['user'].'">我</a>';
}else{
echo '<a href="index.php?mod=chat&to='.$row12['user'].'">'.$row12['user'].'</a>';
}
$n=$row12['nr'];
$n = htmlspecialchars($n, ENT_QUOTES);
echo ' 说:'.$n.'('.$row12['sj'].') <a href="index.php?mod=chat">聊天</a>';
}
echo '</div></div>';
##交流社区end
}
$strtotime=strtotime($conf['build']);//获取开始统计的日期的时间戳
$now=time();//当前的时间戳
$yxts=ceil(($now-$strtotime)/86400);//取相差值然后除于24小时(86400秒)
$qqs=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1");
$zongs=$qqjobs+$signjobs+$wzjobs;
$users=$DB->count("SELECT count(*) from ".DBQZ."_user WHERE 1");
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-bar-chart"></i>&nbsp;&nbsp;<a href="index.php?mod=all">运行数据</a></h3></div>
<table class="table table-bordered">
<tbody>
<tr>
	<td align="center"><font color="#808080"><b>平台已经运营</b></br><i class="fa fa-hourglass-2 fa-2x"></i></br>'.$yxts.'天</font></td>
	<td align="center"><font color="#808080"><b>注册会员人数</b></br><span class="fa fa-users fa-2x"></span></br>'.$users.'位</font></td>
</tr>
<tr height=50>
         <td align="center"><font color="#808080"><b>正在挂机ＱＱ</b></br><i class="fa fa-qq fa-2x"></i></span></br>'.$qqs.'个</font></td>
	<td align="center"><font color="#808080"><b>系统累计运行</b></br><i class="fa fa-pie-chart fa-2x"></i></span></br>'.$info['times'].'次</font></td>
</tr>
</tbody>
</table>
</div>';

echo '<div class="panel panel-primary"><div class="panel-body" style="text-align: center;">';
echo '<!--bottomS-->';
echo $conf['bottom'];
echo '<!--bottomE--><br>';
$week=array("天","一","二","三","四","五","六");
echo date("Y年m月d日 H:i:s").' 星期'.$week[date("w")];
echo '</div></div></div>';
include TEMPLATE_ROOT."foot.php";

?>