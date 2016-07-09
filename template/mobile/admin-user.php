<?php
if(!defined('IN_CRONLITE'))exit();
$title="注册用户管理";
include_once(TEMPLATE_ROOT."head.php");

$my=isset($_GET['my'])?$_GET['my']:null;

if ($isadmin!=1)
{
header("location:index.php");}
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

echo '<div class="w h">注册用户管理</div>';
echo '<div class="row">'.$rownum.$numrows.'个用户 [<a href="index.php?mod=reg">添加一个用户</a>][<a href="index.php?mod=admin-upuser">一键刷新用户任务数</a>]</div>';

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

$rs=$DB->query("select * from wjob_user where{$sql} order by userid desc limit $offset,$pagesize");
$i=0;
while ($myrow = $DB->fetch($rs))
{
$i++;
$pagesl=$i+($page-1)*$pagesize;
$iij = $i % 2;
if ($iij == 0) {
	echo '<div class="row">';
} else {
	echo '<div class="box">';
}
echo 'ID:'.$myrow['userid'].'(<a href="index.php?mod=user&user='.$myrow['user'].'">管理</a>|<a href="index.php?mod=admin-user&my=del&user='.$myrow['user'].'">删除</a>)<br>用户名:<a href="index.php?mod=admin-user&my=user&uid='.$myrow['userid'].'">'.$myrow['user'].'</a><br>密码:'.$myrow['pass'].'<br>注册日期:'.$myrow['date'].'<br>最后登录:'.$myrow['last'].'<br>任务数量:'.$myrow['num'].'(<a href="index.php?mod=admin-user&my=qk&user='.$myrow['user'].'">清空</a>)</div>';
}

echo'<div class="w">';
echo "共有".$pages."页(".$page."/".$pages.")<br>";
for ($i=1;$i<$page;$i++)
echo "<a href='index.php?mod=admin-user&page=".$i.$link."'>[".$i ."]</a> ";
echo "[".$page."]";
for ($i=$page+1;$i<=$pages;$i++)
echo "<a href='index.php?mod=admin-user&page=".$i.$link."'>[".$i ."]</a> ";
echo '<br>';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo "<a href='index.php?mod=admin-user&page=".$first.$link."'>首页</a>.";
echo "<a href='index.php?mod=admin-user&page=".$prev.$link."'>上一页</a>";
}
if ($page<$pages)
{
echo "<a href='index.php?mod=admin-user&page=".$next.$link."'>下一页</a>.";
echo "<a href='index.php?mod=admin-user&page=".$last.$link."'>尾页</a>";
}
echo'</div>';
##分页
}
}
if($my=='user'){
$userid=$_GET['uid'];
$myrow=$DB->get_row("select * from wjob_user where userid='$userid' limit 1");
echo '<div class="w h">用户资料</div>';
echo '<div class="box">用户ID:'.$myrow['userid'];
if ($conf['adminid']==$myrow['userid'])echo'<font color=blue>(管理员)</font>';
else echo'<font color=green>(普通会员)</font>';
echo '<br>用户名:'.$myrow['user'].'<br>密码:'.$myrow['pass'].'<br>注册日期:'.$myrow['date'].'<br>最后登录:'.$myrow['last'].'<br>注册IP:<a href="http://wap.ip138.com/ip_search138.asp?ip='.$myrow['zcip'].'" target="_blank">'.$myrow['zcip'].'</a><br>登录IP:<a href="http://wap.ip138.com/ip_search138.asp?ip='.$myrow['dlip'].'" target="_blank">'.$myrow['dlip'].'</a><br>任务数量:'.$myrow['num'].'(<a href="index.php?mod=admin-user&my=qk&user='.$myrow['user'].'">清空</a>)<br>
ＱＱ数量:'.$myrow['qqnum'].'(<a href="index.php?mod=set&my=qkqq&user='.$myrow['user'].'">清空</a>)</div>';
echo '<div class="w h"><h3>用户操作</h3></div>';
echo '<div class="box"><a href="index.php?mod=user&user='.$myrow['user'].'">管理网络任务</a>|<a href="index.php?mod=qqlist&user='.$myrow['user'].'">管理QQ账号</a><br><a href="index.php?mod=admin-user&my=qkjf&user='.$myrow['user'].'">清空所有积分</a>|<a href="index.php?mod=admin-user&my=del&user='.$myrow['user'].'">删除该用户</a></div>';
echo '<a href="user_list.php">>>返回用户管理</a>';
}

elseif($my=='del'){
echo '<div class="w h">删除用户</div>';
$user=$_GET['user'];
echo '<div class="box"><a href="index.php?mod=admin-user&my=del_ok&user='.$user.'">确定要删除吗？是！</a></div>'; 
echo '<a href="index.php?mod=admin-user">>>返回用户管理</a>';
}

elseif($my=='del_ok'){
echo '<div class="w h">删除用户</div><div class="box">';
$user=$_GET['user'];
$sql=$DB->query("DELETE FROM wjob_user WHERE user='$user'");
$DB->query("DELETE FROM wjob_job WHERE lx='$user'");
$DB->query("DELETE FROM wjob_qq WHERE lx='$user'");
if($sql){echo '删除成功！';}
else{echo '删除失败！';}
echo '</div><a href="index.php?mod=admin-user">>>返回用户管理</a>';
}

elseif($my=='qk'){//清空任务
$user=$_GET['user'];
echo '<div class="box">您确认要清空用户 '.$user.' 的所有任务吗？清空后无法恢复！<br><a href="index.php?mod=admin-user&my=qk2&user='.$user.'">确认</a> | <a href="javascript:history.back();">返回</a></div>';
}
elseif($my=='qk2'){//清空任务结果
$user=$_GET['user'];
if($DB->query("DELETE FROM wjob_job WHERE lx='$user'")==true){
$DB->query("UPDATE wjob_user SET num= '0' WHERE user = '$user'");
echo '<div class="box">清空成功，</div>';
}else{
echo'<div class="box">清空失败，</div>';
}
}
echo'<br><div class="copy">';
echo date("Y年m月d日 H:i:s");
echo'<br>';
echo'<a href="./">返回后台管理</a>-<a href="index.php">返回首页</a>';
include(ROOT.'includes/foot.php');
echo'</div></body></html>';
?>