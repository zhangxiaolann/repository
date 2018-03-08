<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
switch($act) {

case 'add':
$qq=daddslashes($_GET['qq']);
$jobid=daddslashes($_GET['jobid']);
$type=daddslashes($_GET['type']);

if(!$qq || !$type) {
	exit('{"code":-1,"msg":"参数不能为空！"}');
}
if(OPEN_OTHE==0 && ($type=='liuyan' || $type=='gift'))
	exit('{"code":-1,"msg":"当前站点未开启此功能！"}');
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin!=1 && $isdeputy!=1) {
	exit('{"code":-1,"msg":"你只能操作自己的QQ哦！"}');
}
if(in_array($type,$qqSignTasks))$func='qsign';else $func=$type;
if(in_array($func,$vip_func) && $isvip==0 && $isadmin==0) {
	exit('{"code":-1,"msg":"抱歉，您还不是网站VIP会员，无法使用此功能。"}');
}
$data=qqjob_encode(daddslashes($type));

$method=daddslashes($_POST['method']);
$sysid=isset($_POST['sys']) ? intval($_POST['sys']) : 1;
$start=isset($_POST['start']) ? intval($_POST['start']) : '0';
$stop=isset($_POST['stop']) ? intval($_POST['stop']) : '24';
$pl=isset($_POST['pl']) ? intval($_POST['pl']) : '0';

if($start>$stop)
{
	exit('{"code":0,"msg":"运行时间格式错误:开始时间大于结束时间"}');
}

if($type=='qcloud'){$start=12;$stop=13;}
$myrow=$DB->get_row("SELECT * FROM ".DBQZ."_qqjob WHERE qq='{$qq}' and type='{$type}' limit 1");

if($jobid!=0 || $jobid=$myrow['jobid']) {
	$sql="update `".DBQZ."_qqjob` set `method` ='{$method}',`data` ='{$data}',`pl`='$pl',`start`='$start',`stop`='$stop',`sysid`='$sysid' where `jobid`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务已成功修改！"}');
	}else{
		exit('{"code":0,"msg":"任务修改失败！'.$DB->error().'"}');
	}
} else {
	if(in_array($type,$qqSignTasks))$sign=1;else $sign=0;
	$sql="insert into `".DBQZ."_qqjob` (`uid`,`qq`,`type`,`sign`,`method`,`data`,`lasttime`,`nexttime`,`pl`,`start`,`stop`,`sysid`) values ('{$uid}','{$qq}','{$type}','{$sign}','{$method}','{$data}','".time()."','".time()."','{$pl}','{$start}','{$stop}','{$sysid}')";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"'.$qqTaskNames[$type].'任务已成功添加！"}');
	}else{
		exit('{"code":0,"msg":"任务添加失败！'.$DB->error().'"}');
	}
}


break;

case 'edit':
$qq=daddslashes($_GET['qq']);
$type=daddslashes($_GET['type']);
$jobid=daddslashes($_GET['jobid']);
$page=daddslashes($_GET['page']);
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin!=1 && $isdeputy!=1)
{showmsg('你只能操作自己的QQ哦！',3);
}
if($jobid)
{
	$row1=$DB->get_row("SELECT *FROM ".DBQZ."_qqjob where jobid='{$jobid}' limit 1");
	$qqrow=@unserialize($row1['data']);
	$qq=$row1['qq'];
	$type=$row1['type'];
}else{
	$qqrow=array('msg'=>'您好！我在挂Q，暂时无法回复您。','content'=>'[随机]','img'=>'','ua'=>'iPhone 6 Plus');
}
?>
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>添加<?php echo $qqTaskNames[$type]?>任务</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('qqtask',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
<?php
$display_time='<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">运行时段:</div>
<select class="form-control" style="width:40%;display:inline;float:none;" id="start" default="'.$row1['start'].'">
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
</select>&nbsp;时-&nbsp;<select class="form-control" style="width:40%;display:inline;float:none;" id="stop" default="'.$row1['stop'].'">
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
</select>&nbsp;时</div>
</div>';
if($type=='shuo'||$type=='zfss')$pl='600';else $pl='0';
$pl=isset($row1['pl'])?$row1['pl']:$pl;
$display_pl='<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">运行频率(秒/次)</div>
<input type="text" class="form-control" id="pl" value="'.$pl.'">
</div>
</div>';
if($conf['multisys']) {
$display_sys='
<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">服务器</div><select class="form-control" name="sys">';
$show=explode('|',$conf['show']);
for($i=0;$i<$conf['sysnum'];$i++){
	$sysid=$i+1;
	$all_sys=$DB->count("SELECT count(*) from ".DBQZ."_qqjob WHERE sysid='$sysid'");
	if($all_sys>=$conf['max']){$sysnum=-1;$addstr='已满';}
	else {$sysnum=$sysid;$addstr=$all_sys.'人';}
	$display_sys.='<option value="'.$sysnum.'" '.($sysid==$row1['sysid']?'selected="selected"':NULL).'>'.$sysid.'号服务器('.$addstr.')</option>';
}
$display_sys.='</select><br/></div>
</div>';
}
switch($type) {
case 'zan':
vipfunc_check('zan');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">秒赞协议</div>
<select class="form-control" id="method">
<option value="3">PC版协议[推荐]</option>
<option value="2">触屏版协议</option>
<option value="4">PC版协议2</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">不秒赞的QQ</div>
<input type="text" class="form-control" id="forbid" value="{$qqrow['forbid']}" placeholder="多个QQ号之间用|隔开"/>
</div>
</div>
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<script>
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	if($('#sys').val()=='-1') {
		alert("该系统任务数量已满，请重新选择一个系统！");
		return false;
	}
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		method:$('#method').val(),forbid:$('#forbid').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'pl':
vipfunc_check('pl');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">秒评协议</div>
<select class="form-control" id="method">
<option value="3">PC版协议</option>
<option value="2">触屏版协议</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">评论内容</div>
<input type="text" class="form-control" id="content" value="{$qqrow['content']}"/>
</div>
</div>
<div class="list-group-item">
<font color="blue">选填内容：<a href="#" onclick="Addstr('[随机]');return false">[随机]</a>，如自定义多条内容请用|隔开</font>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">图片地址</div>
<input type="text" class="form-control" id="img" value="{$qqrow['img']}" placeholder="不需要图片请留空"/>
</div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">不秒评的QQ</div>
<input type="text" class="form-control" id="forbid" value="{$qqrow['forbid']}" placeholder="多个QQ号之间用|隔开"/>
</div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">只秒评的QQ</div>
<input type="text" class="form-control" id="only" value="{$qqrow['only']}" placeholder="多个QQ号之间用|隔开" placeholder="留空则秒评全部"/>
</div>
</div>
{$display_pl}
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/><font color="green">频率默认为0，即当前系统的最快运行频率</font><br/>运行频率视自己需要而定。只有PC协议才支持发表图片</font><font color="red">发言过于频繁可能会被腾讯禁言或QQ空间被封！</font>
</div>
<script>
function Addstr(str) {
	$("#content").val(str);
}
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	if($('#sys').val()=='-1') {
		alert("该系统任务数量已满，请重新选择一个系统！");
		return false;
	}
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		method:$('#method').val(),pl:$('#pl').val(),content:$('#content').val(),img:$('#img').val(),forbid:$('#forbid').val(),only:$('#only').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'kjqd':
vipfunc_check('kjqd');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">签到协议</div>
<select class="form-control" id="method">
<option value="3">PC版协议</option>
<option value="2">触屏版协议</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">签到内容</div>
<input type="text" class="form-control" id="content" value="{$qqrow['content']}"/>
</div>
</div>
<div class="list-group-item">
<font color="blue">选填内容：<a href="#" onclick="Addstr('[随机]');return false">[随机]</a>、<a href="#" onclick="Addstr('[笑话]');return false">[笑话]</a>、<a href="#" onclick="Addstr('[表情]');return false">[表情]</a>、<a href="#" onclick="Addstr('[时间]');return false">[时间]</a></font>
</div>
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<script>
function Addstr(str) {
	$("#content").val(str);
}
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		method:$('#method').val(),content:$('#content').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'shuo':
vipfunc_check('shuo');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">说说协议</div>
<select class="form-control" id="method">
<option value="2">触屏版协议</option>
<option value="3">PC版协议</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">说说内容</div>
<input type="text" class="form-control" id="content" value="{$qqrow['content']}"/>
</div>
</div>
<div class="list-group-item">
<font color="blue">选填内容：<a href="#" onclick="Addstr('[随机]');return false">[随机]</a>、<a href="#" onclick="Addstr('[笑话]');return false">[笑话]</a>、<a href="#" onclick="Addstr('[表情]');return false">[表情]</a>、<a href="#" onclick="Addstr('[时间]');return false">[时间]</a></font>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">图片地址</div>
<input type="text" class="form-control" id="img" value="{$qqrow['img']}" placeholder="不需要请留空"/>
</div>
</div>
<div class="list-group-item">
<font color="blue">选填内容：<a href="#" onclick="Addstr2('随机');return false">随机</a>、或自定义图片URL。</font>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">同时删除上一条说说</div>
<select class="form-control" id="delete">
<option value="0">0_否</option>
<option value="1">1_是</option>
</select></div>
</div>
{$display_pl}
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="red">运行频率视自己需要而定。发言过于频繁可能会被腾讯禁言！</font>
</div>
<script>
function Addstr(str) {
	$("#content").val(str);
}
function Addstr2(str) {
	$("#img").val(str);
}
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	if($('#sys').val()=='-1') {
		alert("该系统任务数量已满，请重新选择一个系统！");
		return false;
	}
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		method:$('#method').val(),content:$('#content').val(),img:$('#img').val(),delete:$('#delete').val(),pl:$('#pl').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'zfss':
vipfunc_check('zfss');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">转发协议</div>
<select class="form-control" id="method">
<option value="2">触屏版协议</option>
<option value="3">PC版协议</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">好友ＱＱ</div>
<input type="text" class="form-control" id="uin" value="{$qqrow['uin']}" placeholder="多个QQ号之间用|隔开"/>
</div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">转发原因</div>
<input type="text" class="form-control" id="reason" value="{$qqrow['reason']}" placeholder="可留空"/>
</div>
</div>
{$display_pl}
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">好友QQ栏若不填写则转发全部好友的说说。</font>
<font color="red">运行频率视自己需要而定，发言过于频繁可能会被腾讯禁言！</font>
</div>
<script>
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	if($('#sys').val()=='-1') {
		alert("该系统任务数量已满，请重新选择一个系统！");
		return false;
	}
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		method:$('#method').val(),pl:$('#pl').val(),uin:$('#uin').val(),reason:$('#reason').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'qunqd':
vipfunc_check('qsign');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">签到模板</div>
<select class="form-control" id="template">
<option value="1">天气</option>
<option value="2">连续签到</option>
<option value="3">早安</option>
<option value="4">晚安</option>
<option value="5">心情</option>
<option value="6">自定义</option>
<option value="7">女神节</option>
<option value="8">运势</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">签到内容</div>
<input type="text" class="form-control" id="content" value="{$qqrow['content']}" placeholder=""/>
</div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">自定义签到地点</div>
<input type="text" class="form-control" id="poi" value="{$qqrow['poi']}" placeholder=""/>
</div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">屏蔽以下群的签到</div>
<input type="text" class="form-control" id="forbid" value="{$qqrow['forbid']}" placeholder="多个群号之间用|隔开"/>
</div>
</div>
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">自定义签到地点填写示例：深圳市 · 腾讯大厦<br/>不使用自定义签到地点请留空&nbsp;<a href="http://lbs.qq.com/tool/getpoint/index.html" target="_balnk">在线地图经度纬度查询</a></font>
</div>
<script>
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		forbid:$('#forbid').val(),poi:$('#poi').val(),template:$('#template').val(),content:$('#content').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'wenwen':
vipfunc_check('qsign');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">签到模式</div>
<select class="form-control" id="method">
<option value="0">只签到搜索问问</option>
<option value="1">搜索问问签到+群问问签到</option>
</select></div>
</div>
<div class="list-group-item" id="forbid_frame" style="display:none">
<div class="input-group"><div class="input-group-addon">屏蔽以下群的签到</div>
<input type="text" class="form-control" id="forbid" value="{$qqrow['forbid']}" placeholder="多个群号之间用|隔开"/>
</div>
</div>
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<script>
$(document).ready(function(){
$("#method").change(function(){
	if($(this).val() == 0){
		$("#forbid_frame").css("display","none");
	}else{
		$("#forbid_frame").css("display","inherit");
	}
});
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		method:$('#method').val(),forbid:$('#forbid').val(),start:'10'
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'qlsend':
vipfunc_check('qsign');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">发送蜜语动作</div>
<select class="form-control" id="content">
<option value="1">爱你</option>
<option value="2">棒棒糖</option>
<option value="3">亲亲</option>
<option value="4">挑逗传情</option>
<option value="5">送花</option>
<option value="6">快理我</option>
<option value="7">想你</option>
<option value="8">大猪头</option>
<option value="9">求抱抱</option>
<option value="10">咬你</option>
</select>
</div>
</div>
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<script>
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		content:$('#content').val(),pl:"9600"
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case '3gqq':
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
{$display_time}
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">使用3GQQ需要先关闭设备锁；如开启了QQ等级代挂功能请不要使用3GQQ，以免被挤掉线！</font>
</div>
<script>
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		start:$('#start').val(),stop:$('#stop').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'nick':
vipfunc_check('qsign');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">昵称</div>
<input type="text" class="form-control" id="nick" value="{$qqrow['nick']}" placeholder="多个昵称之间用|隔开"/>
</div>
</div>
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<script>
$(document).ready(function(){
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		nick:$('#nick').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;

case 'webqq':
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">运行方式</div>
<select class="form-control" id="method">
<option value="1">不自动回复，只挂机</option>
<option value="2">只回复私人消息</option>
<option value="3">只回复群消息</option>
<option value="4">回复私人消息和群消息</option>
</select></div>
</div>
<div class="list-group-item">
<div class="input-group"><div class="input-group-addon">机器人选择</div>
<select class="form-control" id="robot">
<option value="1">智能机器人默认API</option>
<option value="2">智能机器人DIY版API</option>
<option value="0">自定义回复内容</option>
</select></div>
</div>
<div class="list-group-item" id="frame_set1" style="display:none">
<div class="input-group"><div class="input-group-addon">自定义回复内容</div>
<input type="text" class="form-control" id="msg" value="{$qqrow['msg']}"/>
</div>
</div>
<div class="list-group-item" id="frame_set2" style="display:inherit">
<div class="input-group"><div class="input-group-addon">机器人昵称</div>
<input type="text" class="form-control" id="nick" value="{$qqrow['nick']}" placeholder="为你的机器人起一个昵称吧"/>
</div>
</div>
<div class="list-group-item" id="frame_set3" style="display:none">
<div class="input-group"><div class="input-group-addon">Api Key</div>
<input type="text" class="form-control" id="apikey" value="{$qqrow['apikey']}" placeholder="茉莉机器人的ApiKey"/>
</div>
<div class="input-group"><div class="input-group-addon">Api Secret</div>
<input type="text" class="form-control" id="apisecret" value="{$qqrow['apisecret']}" placeholder="茉莉机器人的APISecret"/>
</div>
</div>
{$display_time}
<p><input type="button" id="qqjob_edit" class="btn btn-primary btn-block" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('qqtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">WEBQQ机器人，与电脑QQ和手机QQ可以共存，并自动帮你回复消息。<br/>如果你选择了智能机器人DIY版API，可对不同消息自定义回复内容，需要到茉莉机器人官网获取APIKEY等信息</font>
</div>
<script>
$(document).ready(function(){
$("#robot").change(function(){
	if($(this).val() == 0){
		$("#frame_set1").css("display","inherit");
		$("#frame_set2").css("display","none");
		$("#frame_set3").css("display","none");
	}else if($(this).val() == 1){
		$("#frame_set1").css("display","none");
		$("#frame_set2").css("display","inherit");
		$("#frame_set3").css("display","none");
	}else{
		$("#frame_set1").css("display","none");
		$("#frame_set2").css("display","none");
		$("#frame_set3").css("display","inherit");
	}
});
$('#qqjob_edit').click(function()
{
	$("#qqjob_edit").val('loading');
	ajax.post("ajax.php?mod=qqjob&act=add&qq={$qq}&type={$type}&jobid={$jobid}",
	{
		start:$('#start').val(),stop:$('#stop').val(),method:$('#method').val(),robot:$('#robot').val(),msg:$('#msg').val(),nick:$('#nick').val(),apikey:$('#apikey').val(),apisecret:$('#apisecret').val()
	},"json",function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',{$page});
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>
HTML;
break;
}
?>
</div>
<?php

break;

}
}else{
	showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}