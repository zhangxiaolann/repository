<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
switch($act) {

case 'add':
$type=daddslashes($_GET['type']);
$jobid=daddslashes($_GET['jobid']);
$sysid=$_GET['sysid']?daddslashes($_GET['sysid']):1;
//$type=isset($_POST['type']) ? intval($_POST['type']) : 0;
$name=daddslashes($_POST['name']);
if(!$name)$name='网址挂刷任务';
$url=daddslashes($_POST['url']);
$post=daddslashes($_POST['post']);
$postfields=daddslashes($_POST['postfields']);
$cookie=daddslashes($_POST['cookie']);
$usep=daddslashes($_POST['usep']);
$proxy=daddslashes($_POST['proxy']);
$referer=daddslashes($_POST['referer']);
$useragent=daddslashes($_POST['useragent']);
$start=isset($_POST['start']) ? intval($_POST['start']) : '0';
$stop=isset($_POST['stop']) ? intval($_POST['stop']) : '24';
$pl=isset($_POST['pl']) ? intval($_POST['pl']) : '0';
$urlp='!^(http|https)://(.+\.)+.+!i';
$DB->query("ALTER TABLE `".DBQZ."_wzjob` ADD COLUMN `realip` varchar(32) DEFAULT NULL");
$realip=isset($_POST['realip']) ? daddslashes(trim($_POST['realip'])) : null;

if(!preg_match($urlp,$url))
{
	exit('{"code":0,"msg":"网址不合法！必须包含且只能包含一个http://"}');
}
if($conf['block'] && preg_match('/('.$conf['block'].')/',$url)){//关键词屏蔽
	exit('{"code":0,"msg":"添加任务失败：网址中包含系统禁止的关键词！"}');
}
if(strpos($url,$siteurl.'cron/')!==false)
{
	exit('{"code":0,"msg":"添加失败：禁止自己监控自己！"}');
}
if($start>$stop)
{
	exit('{"code":0,"msg":"运行时间格式错误:开始时间大于结束时间"}');
}
if($realip && !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $realip))
{
	exit('{"code":0,"msg":"真实IP地址填写不正确"}');
}

if(!$jobid){
	$myrow=$DB->get_row("SELECT * FROM ".DBQZ."_wzjob WHERE url='{$url}' limit 1");
	if($myrow['url']){
		exit('{"code":0,"msg":"添加任务失败：每个网址只能添加一次，此任务网址已存在于系统中！"}');
	}
}else{
	$myrow=$DB->get_row("SELECT * FROM ".DBQZ."_wzjob WHERE jobid='{$jobid}' limit 1");
}

if ($usep=='1' && !preg_match('!.+:\d{2,}!i',$proxy)){
	exit('{"code":0,"msg":"如果你想使用代理请设置正确的代理ip及端口哦！"}');
}

if ($post=='1' && !preg_match('!.+=.+!i',$postfields)){
	exit('{"code":0,"msg":"如果你想POST数据请填写格式正确的POST数据哦，例如:user=***&pass=***"}');
}

if($pl && !preg_match('/[0-9]/',$pl)){
	exit('{"code":0,"msg":"运行频率只能是数字哦！"}');
}

$servernum2=$DB->count("SELECT count(*) FROM ".DBQZ."_wzjob WHERE sysid='{$sysid}'");
if($servernum2>=$conf['max']){
	exit('{"code":0,"msg":"添加任务失败：系统任务数量已饱和！"}');
}

if($myrow['jobid']) {
	if($myrow['uid']!=$uid && $isadmin==0 && $isdeputy==0)
		exit('{"code":-1,"msg":"你只能操作自己的任务哦！"}');
	if($isadmin==1)
	$sql="update `".DBQZ."_wzjob` set `type` ='0',`name` ='$name',`url` ='$url',`post` ='$post',`postfields` ='$postfields',`cookie` ='$cookie',`usep` ='$usep',`proxy` ='$proxy',`referer` ='$referer',`useragent` ='$useragent',`start`='$start',`stop`='$stop',`pl`='$pl',`realip`='$realip' where `jobid`='$jobid'";
	else
	$sql="update `".DBQZ."_wzjob` set `type` ='0',`name` ='$name',`url` ='$url',`post` ='$post',`postfields` ='$postfields',`cookie` ='$cookie',`usep` ='$usep',`proxy` ='$proxy',`referer` ='$referer',`useragent` ='$useragent',`start`='$start',`stop`='$stop',`pl`='$pl',`realip`='$realip',`lasttime`='".time()."' where `jobid`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务已成功修改！"}');
	}else{
		exit('{"code":0,"msg":"任务修改失败！'.$DB->error().'"}');
	}
} else {
	$sql="insert into `".DBQZ."_wzjob` (`uid`,`sysid`,`name`,`type`,`url`,`post`,`postfields`,`cookie`,`lasttime`,`nexttime`,`usep`,`proxy`,`referer`,`useragent`,`start`,`stop`,`pl`,`realip`) values ('".$uid."','".$sysid."','".$name."','0','".$url."','".$post."','".$postfields."','".$cookie."','".time()."','".time()."','".$usep."','".$proxy."','".$referer."','".$useragent."','".$start."','".$stop."','".$pl."','".$realip."')";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务已成功添加！"}');
	}else{
		exit('{"code":0,"msg":"任务添加失败！'.$DB->error().'"}');
	}
}


break;

case 'bulk':
$sysid=$_GET['sysid']?daddslashes($_GET['sysid']):1;
if(isset($_FILES['file']))
{$url=file_get_contents($_FILES['file']['tmp_name']);
} else {
$name=daddslashes($_POST['name']);
$url=$_POST['url'];
}
$start=isset($_POST['start']) ? intval($_POST['start']) : '0';
$stop=isset($_POST['stop']) ? intval($_POST['stop']) : '24';
$pl=isset($_POST['pl']) ? intval($_POST['pl']) : '0';
$urlp='!^(http|https)://(.+\.)+.+!i';
$DB->query("ALTER TABLE `".DBQZ."_wzjob` ADD COLUMN `realip` varchar(32) DEFAULT NULL");
$realip=isset($_POST['realip']) ? daddslashes(trim($_POST['realip'])) : null;

$url = str_replace(array("\r\n", "\r", "\n"), "[br]", $url);
$match=explode("[br]",$url);


$servernum2=$DB->count("SELECT count(*) FROM ".DBQZ."_wzjob WHERE sysid='{$sysid}'");
if($servernum2>=$conf['max']){
	exit('{"code":0,"msg":"添加任务失败：系统任务数量已饱和！"}');
}
if(count($match)>$conf['bulk'] && $isadmin==0){
	exit('{"code":0,"msg":"网址数量超过'.$conf['bulk'].'个了！"}');
}
if($realip && !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $realip))
{
	exit('{"code":0,"msg":"真实IP地址填写不正确"}');
}

$display='<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>批量添加任务</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist(\'wztask\',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">';

foreach($match as $val)
{
	$url=daddslashes($val);
	if($url=='')continue;
	$display.='<p class="bg-success" style="padding: 5px;">'.$url.'<br/>';
	if(!preg_match($urlp,$url))
	{
		$display.='<font color="red">网址不合法！必须至少包含一个http://</font>';
		continue;
	}
	if($conf['block'] && preg_match('/('.$conf['block'].')/',$url)){//关键词屏蔽
		$display.='<font color="red">网址中包含系统禁止的关键词！</font>';
		continue;
	}
	if(strpos($url,$siteurl.'cron/')!==false)
	{
		$display.='<font color="red">添加失败：禁止自己监控自己！</font>';
		continue;
	}

	if(!$name)$name='网址挂刷任务';
	$rowm1=$DB->get_row("SELECT * FROM ".DBQZ."_wzjob WHERE url='{$url}' limit 1");
	if($rowm1['url']==''){
		$sql="insert into `".DBQZ."_wzjob` (`sysid`,`name`,`url`,`uid`,`lasttime`,`nexttime`,`start`,`stop`,`pl`,`realip`) values ('".$sysid."','".$name."','".$url."','".$uid."','".time()."','".time()."','".$start."','".$stop."','".$pl."','".$realip."')";

		if($DB->query($sql))
			$display.='<font color="green">已成功添加!</font>';
		else
			$display.='<font color="red">任务添加失败!</font>'.$DB->error();
	}
	else
		$display.='<font color="red">任务添加失败!此网址已存在于系统中!</font>';
	$display.='</p>';
}
$display.='<p class="bg-warning" style="padding: 10px;">';
if(isset($_FILES['file']))$display.='<a href="#" onclick="wzjob_edit(\'upload\',0,\''.$sysid.'\')"">>>继续添加</a><br/><a href="#" onclick="showlist(\'wztask\',1)"><< 返回我的任务列表</a>';
else $display.='<a href="#" onclick="wzjob_edit(\'bulk\',0,\''.$sysid.'\')">>>继续添加</a><br/><a href="#" onclick="showlist(\'wztask\',1)"><< 返回我的任务列表</a>';
$display.='</p></div></div>';

exit(json_encode(array('code'=>2,'msg'=>rawurlencode($display))));

break;
case 'edit':
$type=daddslashes($_GET['type']);
$jobid=daddslashes($_GET['jobid']);
$sysid=daddslashes($_GET['sysid']);
$page=daddslashes($_GET['page']);
$toact=($type=='add'||$type=='edit')?'add':'bulk';
if($jobid)
{
	$row1=$DB->get_row("SELECT *FROM ".DBQZ."_wzjob where jobid='{$jobid}' limit 1");
	if($row1['uid']!=$uid && $isadmin==0 && $isdeputy==0)
		showmsg('你只能操作自己的任务哦！',3);
}
?>
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>添加网址监控任务</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('wztask',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
<?php
echo '<div class="panel-body">';
if($type=='add')
echo '<h4>创建一个新任务</h4>
<form action="#" method="post">
<div class="form-group">
<label>名称:</label><font color="green">(可不填)</font><br/>
<input type="text" class="form-control" name="name" value="">
</div>
<div class="form-group">
<label>网址:</label><font color="green">(必须包含且只能包含一个http://)</font><br/>
<textarea class="form-control" name="url" rows="3"></textarea>
</div>';
elseif($type=='edit')
echo '<h4>修改任务</h4>
<form action="#" method="post">
<div class="form-group">
<label>名称:</label><font color="green">(可不填)</font><br>
<input type="text" class="form-control" name="name" value="'.$row1['name'].'">
</div>
<div class="form-group">
<label>网址:</label><font color="green">(必须包含且只能包含一个http://)</font><br>
<textarea class="form-control" name="url" rows="3">'.$row1['url'].'</textarea>
</div>';
elseif($type=='bulk')
echo '<h4>批量添加任务</h4>
<form action="#" method="post">
<div class="form-group">
<label>名称:</label><font color="green">(可不填)</font><br>
<input type="text" class="form-control" name="name" value="">
</div>
<div class="form-group">
<label>网址:</label><br><font color="green">每行一个，最多'.$conf['bulk'].'个(管理员无限制)，分别以 http:// 开头</font><br>
<textarea name="url" class="form-control" rows="6"></textarea><br><font color="green">结尾不要有空行，否则也算一个</font></div>';
elseif($type=='upload')
echo '<h4>从文件导入任务</h4>
<label>导入文本格式：</label><br/><font color="green">每行一个网址，最多'.$conf['bulk'].'个(管理员无限制)，分别以 http:// 开头。</font>
<form action="#" method="post" enctype="multipart/form-data" id="fileupload">
<div class="form-group"><input type="file" class="form-control" name="file" id="file"/></div>';

echo '<div class="form-group">
<label>任务运行时段:</label><br/>
<select class="form-control" style="width:40%;display:inline;float:none;" name="start" ivalue="'.$row1['start'].'">
<option value="00">00</option>
<option value="01">01</option>
<option value="02">02</option>
<option value="03">03</option>
<option value="04">04</option>
<option value="05">05</option>
<option value="06">06</option>
<option value="07">07</option>
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
</select>&nbsp;时-&nbsp;<select class="form-control" style="width:40%;display:inline;float:none;" name="stop" ivalue="'.$row1['stop'].'">
<option value="24">24</option>
<option value="00">00</option>
<option value="01">01</option>
<option value="02">02</option>
<option value="03">03</option>
<option value="04">04</option>
<option value="05">05</option>
<option value="06">06</option>
<option value="07">07</option>
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
</select>&nbsp;时<br><font color="green">运行时间段设置<br>如:01小时-01小时(每天在02时停止)。</font>
</div>
<div class="form-group">
<label>运行频率(秒/次):</label><br>
<input type="text" class="form-control" name="pl" value="'.$row1['pl'].'"><font color="green">运行频率最高无法高于本系统的运行频率(可留空)</font>
</div>';
echo '<div class="form-group">
<label>真实IP地址:</label><br>
<input type="text" class="form-control" name="realip" value="'.$row1['realip'].'"><font color="green">填写目标真实IP地址可以避免开启CDN后而导致的无法监控，如果不知道IP请不要填写</font></div>';
if($type=='add'||$type=='edit') {
echo '<a id="openadvance" onclick=\'$("#advance").slideDown("fast");$("#openadvance").hide();\' class="btn btn-default btn-block">显示高级功能</a>
<div id="advance" style="display:none;">
<a id="closeadvance" onclick=\'$("#advance").slideUp("fast");$("#openadvance").show();\' class="btn btn-default btn-block">隐藏高级功能</a>
<font color=red>如果你什么都不懂，请不要使用以下功能！</font><br>
<div class="form-group">
<label>使用代理:</label><br>';
if($row1['usep']=='1'){ 
echo '<select class="form-control" name="usep">
<option value="1">是</option> 
<option value="0">否</option>
<option value="2">使用PCTOWAP中转</option>
</select></div>';
}else{
echo '<select class="form-control" name="usep">
<option value="0">否</option>
<option value="1">是</option>
<option value="2">使用PCTOWAP中转</option>
</select></div>';
}
echo '<div class="form-group">
<label>代理ip及端口号:</label><br><font color="green">格式:000.000.000.000:00</font><br><input type="text" class="form-control" name="proxy" value="'.$row1['proxy'].'"><font color="green">注意:不需要代理时千万不要随便填写</font></div>
<div class="form-group"><label>POST模拟:</label><br>';
if($row1['post']=='1'){ 
echo '<select class="form-control" name="post"><option value="1">开启</option><option value="0">关闭</option></select></div>';
}else{
echo '<select class="form-control" name="post"><option value="0">关闭</option><option value="1">开启</option></select></div>';
}
echo '<div class="form-group">
<label>POST数据:</label><br>
<font color="green">格式:user=***&pass=***</font><br>
<input type="text" class="form-control" name="postfields" value="'.$row1['postfields'].'"><font color="green">不启用POST时此项可留空</font></div>
<div class="form-group">
<label>Cookie数据:</label><br>
<font color="green">格式:token=***;pass=***;</font><br>
<input type="text" class="form-control" name="cookie" value="'.$row1['cookie'].'"><font color="green">不启用Cookie时此项可留空</font></div>
<div class="form-group"><label>来源地址:</label><br>
<input type="text" class="form-control" name="referer" value="'.$row1['referer'].'"><font color="green">不需要设置来源地址时请不要填写</font></div>
<div class="form-group"><label>模拟浏览器UA:</label><br>
<input type="text" class="form-control" name="useragent" value="'.$row1['useragent'].'">
<font color="green">不需要模拟浏览器时请不要填写</font></div>
</div><br/>';
}
echo '<input type="button" class="btn btn-primary btn-block" id="wzjob_edit" value="提交"/><br/>
</form>
[ <a href="#" onclick="showlist(\'wztask\',1)">返回上一页</a> ]
</div>';
echo <<<HTML
<script>
$(document).ready(function(){
$('#wzjob_edit').click(function()
{
	$("#wzjob_edit").val('loading');
	if(!document.getElementById("fileupload"))
	{
	ajax.post("ajax.php?mod=wzjob&act={$toact}&type={$type}&sysid={$sysid}&jobid={$jobid}",$('form').serialize(),"json",function(arr,status){
		if(arr.code==1){
			alert(arr.msg);
			showlist('wztask',{$page});
		}else if(arr.code==2){
			$('#list').html(decodeURIComponent(arr.msg));
		}else{
			alert(arr.msg);
		}
	});
	}else{
		$.ajaxFileUpload({
			url:"ajax.php?mod=wzjob&act={$toact}&type={$type}&sysid={$sysid}&jobid={$jobid}",
			fileElementId:'file',
			secureuri:false,
			type:'post',
			data:$('form').serialize(),
			dataType:"json",
			success: function(arr, status){
				if(arr.code==1){
					alert(arr.msg);
					showlist('wztask',{$page});
				}else if(arr.code==2){
					$('#list').html(decodeURIComponent(arr.msg));
				}else{
					alert(arr.msg);
				}
			},error: function (arr, status, e){
				alert('文件上传失败！');
			}
		});  
	}
});
});
</script>
HTML;

?>
</div>
<?php

break;

}
}else{
	showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}