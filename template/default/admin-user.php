<?php
if(!defined('IN_CRONLITE'))exit();
$title="注册用户管理";
include_once(TEMPLATE_ROOT."head.php");

$my=isset($_GET['my'])?$_GET['my']:null;
echo '<div class="col-md-9" role="main">';

if ($isadmin!=1)
{
header("location:index.php");
}
else
{

if($my==null){

if(isset($_GET['kw'])) {
	$sql=" `user` LIKE '%{$_GET['kw']}%'";
	$link='&kw='.$_GET['kw'];
	$rownum='包含'.$_GET['kw'].'的共有';
}
elseif(isset($_GET['id'])) {
	$sql=" `userid`='{$_GET['id']}'";
	$link='';
	$rownum='系统共有';
} else {
	$sql=' 1';
	$link='';
	$rownum='系统共有';
}

$numrows = $DB->count("select count(*) from wjob_user where".$sql);

echo '<h3>注册用户管理</h3>';
echo '<div class="alert alert-info">'.$rownum.$numrows.'个用户 [<a href="index.php?mod=reg">添加一个用户</a>][<a href="index.php?mod=admin-upuser">一键刷新用户任务数</a>]</div>';

$pagesize=$conf['pagesize'];
$pages=intval($numrows/$pagesize);
if ($numrows%$pagesize)
{
 $pages++;
 }
if (isset($_GET['page'])){
$page=intval($_GET['page']);
}
else{
$page=1;
}
$offset=$pagesize*($page - 1);

?>
<style>
.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space: pre-wrap;}
</style>
<div class="panel panel-default table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>UID</th>
			<th>用户名</th>
			<th>注册及登录信息</th>
			<th>数量</th>
		</tr>
	</thead>
	<tobdy>
<?php

$rs=$DB->query("select * from wjob_user where{$sql} order by userid desc limit $offset,$pagesize");
$i=0;
while ($myrow = $DB->fetch($rs))
{
$i++;
$pagesl=$i+($page-1)*$pagesize;

echo '<tr><td style="width:20%;"><b>'.$myrow['userid'].'</b><br/><a href="index.php?mod=admin-user&my=del&uid='.$myrow['userid'].'">删除</a>.<a href="index.php?mod=user&user='.$myrow['user'].'">管理</a></td><td style="width:30%">用户名:<a href="index.php?mod=admin-user&my=user&uid='.$myrow['userid'].'">'.$myrow['user'].'</a></td><td style="width:30%">注册日期:<font color="blue">'.$myrow['date'].'</font><br>最后登录:<font color="blue">'.$myrow['last'].'</font></td><td style="width:20%">任务:'.$myrow['num'].'(<a href="index.php?mod=admin-user&my=qk&uid='.$myrow['userid'].'">清空</a>)<br/>ＱＱ:'.$myrow['qqnum'].'(<a href="index.php?mod=admin-user&my=qkqq&uid='.$myrow['userid'].'">清空</a>)</td></tr>';
}
?>
	</tbody>
</table>
</div>

<?php
echo'<ul class="pagination">';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo '<li><a href="index.php?mod=admin-user&page='.$first.$link.'">首页</a></li>';
echo '<li><a href="index.php?mod=admin-user&page='.$prev.$link.'">&laquo;</a></li>';
} else {
echo '<li class="disabled"><a>首页</a></li>';
echo '<li class="disabled"><a>&laquo;</a></li>';
}
for ($i=1;$i<$page;$i++)
echo '<li><a href="index.php?mod=admin-user&page='.$i.$link.'">'.$i .'</a></li>';
echo '<li class="disabled"><a>'.$page.'</a></li>';
for ($i=$page+1;$i<=$pages;$i++)
echo '<li><a href="index.php?mod=admin-user&page='.$i.$link.'">'.$i .'</a></li>';
echo '';
if ($page<$pages)
{
echo '<li><a href="index.php?mod=admin-user&page='.$next.$link.'">&raquo;</a></li>';
echo '<li><a href="index.php?mod=admin-user&page='.$last.$link.'">尾页</a></li>';
} else {
echo '<li class="disabled"><a>&raquo;</a></li>';
echo '<li class="disabled"><a>尾页</a></li>';
}
echo'</ul>';
#分页
}
}
if($my=='user'){
$userid=$_GET['uid'];
$row=$DB->get_row("select * from wjob_user where userid='$userid' limit 1");
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">用户资料</h3></div>';
echo '<div class="panel-body box"><b>UID：</b>'.$row['userid'];
if ($conf['adminid']==$row['userid'])echo'<font color=blue>(管理员)</font>';
else echo'<font color=green>(普通会员)</font>';
echo '<br><b>用户名：</b>'.$row['user'].'<br><b>邮箱：</b>'.$row['email'].'<br><b>注册日期：</b>'.$row['date'].'<br><b>最后登录：</b>'.$row['last'].'<br><b>注册IP：</b><a href="http://wap.ip138.com/ip_search138.asp?ip='.$row['zcip'].'" target="_blank">'.$row['zcip'].'</a><br><b>登录IP：</b><a href="http://wap.ip138.com/ip_search138.asp?ip='.$row['dlip'].'" target="_blank">'.$row['dlip'].'</a><br><b>任务数量：</b>'.$row['num'].'(<a href="index.php?mod=admin-user&my=qk&user='.$row['user'].'">清空</a>)<br>
<b>ＱＱ数量：</b>'.$row['qqnum'].'(<a href="index.php?mod=set&my=qkqq&user='.$row['user'].'">清空</a>)</div>';

echo '<div class="panel-heading w h"><h3 class="panel-title">用户操作</h3></div>';
echo '<div class="panel-body box"><a href="index.php?mod=user&user='.$row['user'].'">管理网络任务</a>|<a href="index.php?mod=qqlist&user='.$row['user'].'">管理QQ账号</a><br><a href="index.php?mod=admin-user&my=qkjf&user='.$row['user'].'">清空所有积分</a>|<a href="index.php?mod=admin-user&my=del&uid='.$row['userid'].'">删除该用户</a></div></div>';
echo '<a href="index.php?mod=admin-user">>>返回用户管理</a>';
}

elseif($my=='del'){
echo '<div class="w h">删除用户</div>';
$userid=$_GET['uid'];
if($userid==1)exit('你不能删除管理员！');
echo '<div class="box"><a href="index.php?mod=admin-user&my=del_ok&uid='.$userid.'">确定要删除吗？是！</a></div>'; 
echo '<a href="index.php?mod=admin-user">>>返回用户管理</a>';
}

elseif($my=='del_ok'){
echo '<div class="w h">删除用户</div><div class="box">';
$userid=$_GET['uid'];
if($userid==1)exit('你不能删除管理员！');
$row2=$DB->get_row("select * from wjob_user where userid='$userid' limit 1");
$sql=$DB->query("DELETE FROM wjob_user WHERE user='{$row2['user']}'");
$DB->query("DELETE FROM wjob_job WHERE lx='{$row2['user']}'");
$DB->query("DELETE FROM wjob_qq WHERE lx='{$row2['user']}'");
if($sql){echo '删除成功！';}
else{echo '删除失败！';}
echo '</div><a href="index.php?mod=admin-user">>>返回用户管理</a>';
}

elseif($my=='qk'){//清空任务
$userid=$_GET['uid'];
echo '<div class="box">您确认要清空用户uid '.$userid.' 的所有任务吗？清空后无法恢复！<br><a href="index.php?mod=admin-user&my=qk2&uid='.$userid.'">确认</a> | <a href="javascript:history.back();">返回</a></div>';
}
elseif($my=='qk2'){//清空任务结果
$userid=$_GET['uid'];
$row2=$DB->get_row("select * from wjob_user where userid='$userid' limit 1");
if($DB->query("DELETE FROM wjob_job WHERE lx='{$row2['user']}'")==true){
$DB->query("UPDATE wjob_user SET num= '0' WHERE user = '$user'");
echo '<div class="box">清空成功，</div>';
}else{
echo'<div class="box">清空失败，</div>';
}
}
elseif($my=='qkqq'){//清空QQ
$userid=$_GET['uid'];
echo '<div class="box">您确认要清空用户uid '.$userid.' 的所有ＱＱ吗？清空后无法恢复！<br><a href="index.php?mod=admin-user&my=qkqq2&uid='.$userid.'">确认</a> | <a href="javascript:history.back();">返回</a></div>';
}
elseif($my=='qkqq2'){//清空QQ结果
$userid=$_GET['uid'];
$row2=$DB->get_row("select * from wjob_user where userid='$userid' limit 1");
if($DB->query("DELETE FROM wjob_job WHERE lx='{$row2['user']}'")==true){
$DB->query("UPDATE wjob_user SET qqnum= '0' WHERE userid = '$userid'");
echo '<div class="box">清空成功，</div>';
}else{
echo'<div class="box">清空失败，</div>';
}
}
echo'<br><div class="copy">';
echo date("Y年m月d日 H:i:s");
echo'<br>';
echo'<a href="index.php?mod=admin">返回后台管理</a>-<a href="index.php">返回首页</a>';
include(ROOT.'includes/foot.php');
echo'</div></div></div></body></html>';
?>