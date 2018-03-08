<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
switch($act) {

case 'qqtask':
$qq=daddslashes($_GET['qq']);
$qqrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($qqrow['uid']!=$uid && $isadmin!=1)
{showmsg('你只能操作自己的QQ哦！',3);
}
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qqjob WHERE qq='{$qq}'");
$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}
?>
<div class="panel panel-default table-responsive" id="list">
<table class="table table-hover">
	<thead>
		<tr>
			<th>任务名称（<?php echo $gls?>）</th>
			<th>其他信息</th>
			<th>状态/操作</th>
		</tr>
	</thead>
	<tobdy>
	<tr>
		<td><b>已开启SID/SKEY自动更新</b></td><td>状态:<font color="green">正在运行</font><br/>上次更新:<font color="blue"><?php echo $qqrow['time']?></font></td><td><a class="btn btn-danger btn-sm" disabled><i class="fa fa-trash"></i>&nbsp;删除</a></td>
	</tr>
<?php
$i=0;
$rs=$DB->query("SELECT * FROM ".DBQZ."_qqjob WHERE qq='$qq' order by jobid desc limit $pageu,$pagesize");
while($myrow = $DB->fetch($rs))
{
	$i++;
	$pagesl = $i + ($page - 1) * $pagesize;

	$type=$myrow['type'];
	$qqjob=qqjob_decode($qq,$type,$myrow['method'],$myrow['data']);
	if(!$qqTaskNames[$type])continue;
	echo '<tr jobid="'.$myrow['jobid'].'"><td style="width:40%;"><b>'.$pagesl.'.'.$qqTaskNames[$type].'任务</b><br/>'.$qqjob['info'];
	echo '</td><td style="width:35%">状态:';
	if ($myrow['zt'] == '1'){
		echo '<font color="red">暂停运行...</font><br/>';
	}else{
		echo '<font color="green">正在运行</font><br/>';
	}
	echo '运行次数:<font color="red">'.$myrow['times'].'</font><br>上次执行:<font color="blue">'.dgmdate($myrow['lasttime']).'</font>';
	echo '<br/>运行时间:<font color="blue">'.$myrow['start'].'时 - '.$myrow['stop'].'时</font>';
	if($myrow['pl']!=0)
		echo '<br>运行频率:<font color="red">'.$myrow['pl'].'</font>秒/次';
	elseif(in_array($type,$qqSignTasks))
		echo '<br>运行频率:<font color="red">18000</font>秒/次';
	elseif(in_array($type,$qqLimitTasks) || in_array($type,$qqGuajiTasks))
		echo '<br>运行频率:<font color="red">600</font>秒/次';

	echo '</td><td style="width:25%">';
	if($myrow['data'] || $type=='3gqq')
		echo '<a href="#" onclick="qqjob_edit(\''.$qq.'\',undefined,\''.$myrow['jobid'].'\',\''.$page.'\')" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i>&nbsp;编辑</a><br/>';
	if ($myrow['zt'] == '1') {
		echo '<a href="#" onclick="job_edit(\'kq\','.$myrow['jobid'].',\'qqjob\',\''.$page.'\')" class="btn btn-success btn-sm"><i class="fa fa-play"></i>&nbsp;开启</a>';
	}else{
		echo '<a href="#" onclick="job_edit(\'zt\','.$myrow['jobid'].',\'qqjob\',\''.$page.'\')" class="btn btn-success btn-sm"><i class="fa fa-pause"></i>&nbsp;暂停</a>';
	}
	echo '<br/><a href="#" onclick="job_edit(\'del\','.$myrow['jobid'].',\'qqjob\',\''.$page.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;删除</a></div></td></tr>';
}

?>
	</tbody>
</table>
</div>
<?php 
break;

case 'signtask':
$gls=$DB->count("SELECT count(*) from ".DBQZ."_signjob WHERE uid='{$uid}'");
$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}
?>
<div class="panel panel-default table-responsive" id="list">
<table class="table table-hover">
	<thead>
		<tr>
			<th>任务名称</th>
			<th>其他信息</th>
			<th>状态/操作</th>
		</tr>
	</thead>
	<tobdy>
<?php
$i=0;
$rs=$DB->query("SELECT * FROM ".DBQZ."_signjob WHERE uid='$uid' order by jobid desc limit $pageu,$pagesize");
while($myrow = $DB->fetch($rs))
{
	$i++;
	$pagesl = $i + ($page - 1) * $pagesize;

	$type=$myrow['type'];
	$signjob=signjob_decode($type,$myrow['data']);
	echo '<tr jobid="'.$myrow['jobid'].'"><td style="width:40%;"><b>'.$pagesl.'.'.$signTaskNames[$type].'任务</b><br/>签到数据：'.$signjob['data'].$signjob['info'];
	echo '</td><td style="width:35%">状态:';
	if ($myrow['zt'] == '1'){
		echo '<font color="red">暂停运行...</font><br/>';
	}else{
		echo '<font color="green">正在运行</font><br/>';
	}
	echo '运行次数:<font color="red">'.$myrow['times'].'</font><br>上次执行:<font color="blue">'.dgmdate($myrow['lasttime']).'</font>';

	echo '</td><td style="width:25%">';
	if($myrow['data'])
		echo '<a href="#" onclick="signjob_edit(\''.$type.'\',\''.$myrow['jobid'].'\',\''.$page.'\')" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i>&nbsp;编辑</a><br/>';
	if ($myrow['zt'] == '1') {
		echo '<a href="#" onclick="job_edit(\'kq\','.$myrow['jobid'].',\'signjob\',\''.$page.'\')" class="btn btn-success btn-sm"><i class="fa fa-play"></i>&nbsp;开启</a>';
	}else{
		echo '<a href="#" onclick="job_edit(\'zt\','.$myrow['jobid'].',\'signjob\',\''.$page.'\')" class="btn btn-success btn-sm"><i class="fa fa-pause"></i>&nbsp;暂停</a>';
	}
	echo '<br/><a href="#" onclick="job_edit(\'del\','.$myrow['jobid'].',\'signjob\',\''.$page.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;删除</a></div></td></tr>';
}

?>
	</tbody>
</table>
</div>
<?php 
break;

case 'wztask':
$sysid=daddslashes($_GET['sysid']);
if(is_numeric($sysid))
	$sqls=" and sysid='{$sysid}'";
elseif(!empty($sysid))
	$sqls=" and `url` LIKE '%{$sysid}%'";
else
	$sqls=null;
$gls=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE uid='{$uid}'{$sqls}");
$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}
?>
<div class="panel panel-default table-responsive" id="list">
<table class="table table-hover">
	<thead>
		<tr>
			<th>任务名称/网址</th>
			<th>其他信息</th>
			<th>状态/操作</th>
		</tr>
	</thead>
	<tobdy>
<?php
$i=0;
$rs=$DB->query("SELECT * FROM ".DBQZ."_wzjob WHERE uid='$uid'{$sqls} order by jobid desc limit $pageu,$pagesize");
while($myrow = $DB->fetch($rs))
{
	$i++;
	$pagesl = $i + ($page - 1) * $pagesize;

	echo '<tr jobid="'.$myrow['jobid'].'"><td style="width:40%;"><b>'.$pagesl.'.'.$myrow['name'].'</b><br/><a href="'.$myrow['url'].'" target="_blank">'.$myrow['url'].'</a><br>';
	if(!empty($myrow['realip']))echo '{真实IP:'.$myrow['realip'].'}';
	if($myrow['usep']==1)echo '{代理IP}';
	if($myrow['post']==1)echo '{模拟POST}';
	if($myrow['cookie']!='')echo '{模拟Cookie}';
	if($myrow['referer']!='')echo '{模拟来源}';
	if($myrow['useragent']!='')echo '{模拟浏览器}';

	echo '</td><td style="width:35%">状态:';
	if ($myrow['zt'] == '1'){
		echo '<font color="red">暂停运行...</font><br/>';
	}else{
		echo '<font color="green">正在运行</font><br/>';
	}
	echo '运行次数:<font color="red">'.$myrow['times'].'</font><br>上次执行:<font color="blue">'.dgmdate($myrow['lasttime']).'</font><br/>运行时间:<font color="blue">';
	echo $myrow['start'].'时 - '.$myrow['stop'].'时</font>';
	if($myrow['pl']!=0)
		echo '<br>运行频率:<font color="red">'.$myrow['pl'].'</font>秒/次';

	echo '</td><td style="width:25%">';
	echo '<a href="#" onclick="wzjob_edit(\'edit\',\''.$myrow['jobid'].'\',\''.$myrow['sysid'].'\',\''.$page.'\')" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i>&nbsp;编辑</a><br/>';
	if ($myrow['zt'] == '1') {
		echo '<a href="#" onclick="job_edit(\'kq\','.$myrow['jobid'].',\'wzjob\',\''.$page.'\')" class="btn btn-success btn-sm"><i class="fa fa-play"></i>&nbsp;开启</a>';
	}else{
		echo '<a href="#" onclick="job_edit(\'zt\','.$myrow['jobid'].',\'wzjob\',\''.$page.'\')" class="btn btn-success btn-sm"><i class="fa fa-pause"></i>&nbsp;暂停</a>';
	}
	echo '<br/><a href="#" onclick="job_edit(\'del\','.$myrow['jobid'].',\'wzjob\',\''.$page.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;删除</a></div></td></tr>';
}

?>
	</tbody>
</table>
</div>
<?php
break;

case 'qqlist':
if(isset($_GET['super']) && $isadmin==1) {
	if(isset($_GET['qq']))
		$gls=1;
	else
		$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1");
} else
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE uid='{$uid}'");
$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}
?>
<div class="panel panel-default table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>QQ账号</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
	</thead>
	<tobdy>
<?php
$i=0;
if(isset($_GET['super']) && $isadmin==1) {
	if($qq=daddslashes($_GET['qq']))
		$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' order by id desc limit $pageu,$pagesize");
	else
		$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE 1 order by id desc limit $pageu,$pagesize");
	$link.='&super=1';
} else
$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE uid='{$uid}' order by id desc limit $pageu,$pagesize");
while($myrow = $DB->fetch($rs))
{
$i++;
$pagesl = $i + ($page - 1) * $pagesize;
  echo '<tr><td style="width:40%;" id="menubar"><a href="index.php?mod=list-qq&qq='.$myrow['qq'].$link.'" title="进入任务管理"><img src="//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$myrow['qq'].'&src_uin='.$myrow['qq'].'&fid='.$myrow['qq'].'&spec=100&url_enc=0&referer=bu_interface&term_type=PC" class="qqlogo" width="80px"><label>'.$myrow['qq'].'</label></a>';
  if(isset($_GET['super']) && $isadmin==1)echo '<br/>所属用户:<a href="index.php?mod=admin-user&my=user&uid='.$myrow['uid'].$link.'">ID '.$myrow['uid'].'</a>';
  if($conf['vipmode']==1) {
	if($myrow['vip']==1){
		if(strtotime($myrow['vipdate'])>time()){
			$vipstatus='到期时间:<font color="green">'.$myrow['vipdate'].'</font>[<a href="index.php?mod=shop&act=1&qq='.$myrow['qq'].'">续期</a>]';
		}else{
			$vipstatus='<font color="red">已过期，请及时<a href="index.php?mod=shop&act=1&qq='.$myrow['qq'].$link.'">续费</a></font>';
		}
	}elseif($myrow['vip']==2){
		$vipstatus='<font color="green">永久 VIP</font>';
	}else{
		$vipstatus='<font color="red">已过期，请及时<a href="index.php?mod=shop&act=1&qq='.$myrow['qq'].'">续费</a></font>';
	}
	echo '<br/>'.$vipstatus;
  }
  echo '</td><td style="width:30%">';
if ($myrow['status'] == '1')
echo '<font color="green">SKEY 正常</font><br/><font color="green">P_skey 正常</font>';
else
echo '<font color="red">SKEY 已失效</font><br/><font color="red">P_skey 已失效</font>';
if ($myrow['status2'] == '1')
echo '<br/><font color="green">Superkey 正常</font>';
else
echo '<br/><font color="red">Superkey 已失效</font>';
echo '</td><td style="width:30%"><a class="btn btn-primary" href="index.php?mod=list-qq&qq='.$myrow['qq'].$link.'" title="添加任务"><i class="fa fa-edit"></i>&nbsp;任务</a>&nbsp;&nbsp;<a class="btn btn-success" href="#" onclick="addqq(\'update\',\''.$myrow['qq'].'\')" title="更新SID&SKEY"><i class="fa fa-refresh"></i>&nbsp;更新</a>&nbsp;&nbsp;<a class="btn btn-danger" href="#" onclick="addqq(\'del\',\''.$myrow['qq'].'\')" title="删除此QQ" onclick="return confirm(\'你确实要删除此QQ号及此QQ下所有挂机任务吗？\');"><i class="fa fa-trash">&nbsp;删除</i></a></td></tr>';}

?>
	</tbody>
</table>
</div>
<?php
break;

case 'damalist':
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE status2='4'");
$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}
?>
<div class="panel panel-default table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>QQ账号</th>
			<th>状态/操作</th>
		</tr>
	</thead>
	<tobdy>
<?php
$i=0;
$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE status='4' or status2='4' order by id desc limit $pageu,$pagesize");
while($myrow = $DB->fetch($rs))
{
$i++;
$pagesl = $i + ($page - 1) * $pagesize;
  echo '<tr><td style="width:50%;"><img src="//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$myrow['qq'].'&src_uin='.$myrow['qq'].'&fid='.$myrow['qq'].'&spec=100&url_enc=0&referer=bu_interface&term_type=PC" class="qqlogo" width="80px"><label>'.$myrow['qq'].'</label>';
  echo '</td><td style="width:50%">';
if ($myrow['status'] == '1')
echo '<font color="green">SKEY 正常</font><br/><font color="green">P_skey 正常</font>';
else
echo '<font color="red">SKEY 已失效</font><br/><font color="red">P_skey 已失效</font>';
if ($myrow['status2'] == '1')
echo '<br/><font color="green">Superkey 正常</font>';
else
echo '<br/><font color="red">Superkey 已失效</font>';
echo '<br/><a href="#" onclick="addqq(\'dama\',\''.$myrow['qq'].'\')" title="协助打码" class="btn btn-success btn-sm">协助打码</a></td></tr>';
}

?>
	</tbody>
</table>
</div>
<?php
break;
}
$s = ceil($gls / $pagesize);
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$s;
echo '<ul class="pagination">';
if ($page>1)
{
echo '<li><a href="#" onclick="showlist(\''.$act.'\','.$first.')">首页</a></li>';
echo '<li><a href="#" onclick="showlist(\''.$act.'\','.$prev.')">&laquo;</a></li>';
} else {
echo '<li class="disabled"><a>首页</a></li>';
echo '<li class="disabled"><a>&laquo;</a></li>';
}
for ($i=1;$i<$page;$i++)
echo '<li><a href="#" onclick="showlist(\''.$act.'\','.$i.')">'.$i .'</a></li>';
echo '<li class="disabled"><a>'.$page.'</a></li>';
for ($i=$page+1;$i<=$s;$i++)
echo '<li><a href="#" onclick="showlist(\''.$act.'\','.$i.')">'.$i .'</a></li>';
echo '';
if ($page<$s)
{
echo '<li><a href="#" onclick="showlist(\''.$act.'\','.$next.')">&raquo;</a></li>';
echo '<li><a href="#" onclick="showlist(\''.$act.'\','.$last.')">尾页</a></li>';
} else {
echo '<li class="disabled"><a>&raquo;</a></li>';
echo '<li class="disabled"><a>尾页</a></li>';
}
echo '</ul>';

}else{
	showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}