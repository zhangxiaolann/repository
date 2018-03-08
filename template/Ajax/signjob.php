<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
switch($act) {

case 'add':
$type=daddslashes($_GET['type']);
$jobid=daddslashes($_GET['jobid']);
$data=daddslashes($_POST['data']);

if(!$type || !$data) {
	exit('{"code":-1,"msg":"参数不能为空！"}');
}
if(in_array('wsign1',$vip_func) && $isvip==0 && $isadmin==0) {
	exit('{"code":-1,"msg":"抱歉，您还不是网站VIP会员，无法使用此功能。"}');
}
$sysid=isset($_POST['sys']) ? intval($_POST['sys']) : 1;
$start=isset($_POST['start']) ? intval($_POST['start']) : '0';
$stop=isset($_POST['stop']) ? intval($_POST['stop']) : '24';
$pl=isset($_POST['pl']) ? intval($_POST['pl']) : '0';

if($start>$stop)
{
	exit('{"code":0,"msg":"运行时间格式错误:开始时间大于结束时间"}');
}

$myrow=$DB->get_row("SELECT * FROM ".DBQZ."_signjob WHERE data='{$data}' and type='{$type}' limit 1");

if($jobid!=0 || $jobid=$myrow['jobid']) {
	if($myrow['uid']!=$uid && $isadmin==0 && $isdeputy==0)
		exit('{"code":-1,"msg":"你只能操作自己的任务哦！"}');
	$sql="update `".DBQZ."_signjob` set `data` ='{$data}',`pl`='$pl',`start`='$start',`stop`='$stop',`sysid`='$sysid' where `jobid`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务已成功修改！"}');
	}else{
		exit('{"code":0,"msg":"任务修改失败！'.$DB->error().'"}');
	}
} else {
	$sql="insert into `".DBQZ."_signjob` (`uid`,`type`,`data`,`lasttime`,`nexttime`,`pl`,`start`,`stop`,`sysid`) values ('{$uid}','{$type}','{$data}','".time()."','".time()."','{$pl}','{$start}','{$stop}','{$sysid}')";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"'.$signTaskNames[$type].'任务已成功添加！"}');
	}else{
		exit('{"code":0,"msg":"任务添加失败！'.$DB->error().'"}');
	}
}


break;

case 'edit':
$type=daddslashes($_GET['type']);
$jobid=daddslashes($_GET['jobid']);
$page=daddslashes($_GET['page']);

if($jobid)
{
	$row1=$DB->get_row("SELECT * FROM ".DBQZ."_signjob where jobid='{$jobid}' limit 1");
	if($row1['uid']!=$uid && $isadmin==0 && $isdeputy==0)
		showmsg('你只能操作自己的任务哦！',3);
	$data=unserialize($row1['data']);
	$type=$row1['type'];
}else{
	$data=array('quest'=>0,'siteid'=>1000);
}
?>
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>添加<?php echo $signTaskNames[$type]?>任务</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('signtask',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
<?php
$display_time='<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">运行时段:</div>
<select class="form-control" style="width:40%;display:inline;float:none;" id="start"">
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
</select>&nbsp;时-&nbsp;<select class="form-control" style="width:40%;display:inline;float:none;" id="stop">
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
<input type="text" class="form-control" name="pl" value="'.$pl.'">
</div>
</div>';

$getcookie = ($apiserverid!=0) ? $apiserver.'sign/getcookie/' : '../sign/getcookie/';
$dzlogin = <<<HTML
<br/><font color="green">(密码模式仅适用于登录不需要验证码的论坛，cookie模式适用于所有论坛)</font><hr/>
<p><label>网站域名:（不要加“http://”）</label><br/>
<input type="text" class="form-control" name="u" value="{$data['u']}" required="required"/></p>
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}" required="required"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}" required="required"/></p>
<p><label>密码提示问题ID（没有的话默认为0）:</label><br/>
<input type="text" class="form-control" name="quest" value="{$data['quest']}"/></p>
<p><label>密码提示问题答案（没有的话默认为空）:</label><br/>
<input type="text" class="form-control" name="answ" value="{$data['answ']}"/></p>
HTML;
$dzlogin2 = <<<HTML
<br/><font color="green">(密码模式仅适用于登录不需要验证码的论坛，cookie模式适用于所有论坛)</font><hr/>
<p><label>Cookie-ID:(<a href="{$getcookie}" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}" required="required"/></p>
HTML;

switch($type) {
case 'klqd':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>网站域名:（不要加“http://”）</label><br/>
<input type="text" class="form-control" name="ym" value="{$data['ym']}"/></p>
<p><label>用户名/手机/id:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码: [<a href="#" onclick="signjob_edit('klqd2')">切换SID模式</a>]</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><label>签到内容:</label><br/>
<input type="text" class="form-control" name="txt" value="{$data['txt']}"/></p>
<p><label>siteid:</label><br/>
<input type="text" class="form-control" name="siteid" value="{$data['siteid']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.mrpyx.cn</font>
</div>
HTML;
break;

case 'klqd2':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>网站域名:（不要加“http://”）</label><br/>
<input type="text" class="form-control" name="u" value="{$data['u']}"/></p>
<p><label>签到内容(5字内):</label><br/>
<input type="text" class="form-control" name="content" value="{$data['content']}"/></p>
<p><div><label>siteid:</label></div>
<input type="text" class="form-control" name="siteid" value="{$data['siteid']}"/></p>
<p><div><label>SID: (<a href="{$apiserver}kelink/kelinksid/" target="_blank">提取SID</a>)[<a href="#" onclick="signjob_edit('klqd')">切换密码模式</a>]</label></div>
<input type="text" class="form-control" name="sid" value="{$data['sid']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.mrpyx.cn<br/>
建议使用<a href="#" onclick="signjob_edit('klqd')">密码模式</a>，sid模式可能会因为sid失效而无法签到。</font>
</div>
HTML;
break;

case 'klol':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>网站域名:（不要加“http://”）</label><br/>
<input type="text" class="form-control" name="ym" value="{$data['ym']}"/></p>
<p><label>siteid:</label><br/>
<input type="text" class="form-control" name="siteid" value="{$data['siteid']}"/></p>
<p><div><label>SID: (<a href="{$apiserver}kelink/kelinksid/" target="_blank">提取SID</a>)</label></div>
<input type="text" class="form-control" name="sid" value="{$data['sid']}"/></p>
<p><label>用户名/手机/id:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码: (用于在sid失效时自动更新)</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.mrpyx.cn</font>
</div>
HTML;
break;

case 'dzsign':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[密码模式][<a href="#" onclick="signjob_edit('dzsign2')">cookie模式</a>]
{$dzlogin}
<p>签到内容：论坛签到天天好心情</p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.wndflb.com<br/>
本签到机只针对Discuz!的DSU每日打卡插件。本身没有开放签到功能的论坛无法签到。如果点击签到后提示“签到失败”也不一定是真的失败，有可能是程序未检测到签到成功的页面，实际能否签到成功以论坛显示为准。</font>
</div>
HTML;
break;

case 'dzsign2':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[<a href="#" onclick="signjob_edit('dzsign')">密码模式</a>][cookie模式]
{$dzlogin2}
<p>签到内容：论坛签到天天好心情</p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>
本签到机只针对Discuz!的DSU每日打卡插件。本身没有开放签到功能的论坛无法签到。如果点击签到后提示“签到失败”也不一定是真的失败，有可能是程序未检测到签到成功的页面，实际能否签到成功以论坛显示为准。</font>
</div>
HTML;
break;

case 'dzdk':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[密码模式][<a href="#" onclick="signjob_edit('dzdk2')">cookie模式</a>]
{$dzlogin}
<p><label>打卡插件类型:</label><br/>
<select class="form-control" name="method">
<option value="amupper">DSU Amupper</option>
<option value="ljdaka">亮剑打卡</option>
<option value="singcere">S!签到</option>
</select></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.wndflb.com<br/>
本签到机支持Discuz!的dsu_amupper打卡插件和亮剑打卡插件。本身没有此类插件的论坛无法签到。如果点击签到后提示“签到失败”也不一定是真的失败，有可能是程序未检测到签到成功的页面，实际能否签到成功以论坛显示为准。</font>
</div>
HTML;
break;

case 'dzdk2':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[<a href="#" onclick="signjob_edit('dzdk')">密码模式</a>][cookie模式]
{$dzlogin2}
<p><label>打卡插件类型:</label><br/>
<select class="form-control" name="method">
<option value="amupper">DSU Amupper</option>
<option value="ljdaka">亮剑打卡</option>
<option value="singcere">S!签到</option>
</select></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>
本签到机支持Discuz!的dsu_amupper打卡插件和亮剑打卡插件。本身没有此类插件的论坛无法签到。如果点击签到后提示“签到失败”也不一定是真的失败，有可能是程序未检测到签到成功的页面，实际能否签到成功以论坛显示为准。</font>
</div>
HTML;
break;

case 'dztask':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[密码模式][<a href="#" onclick="signjob_edit('dztask2')">cookie模式</a>]
{$dzlogin}
<p><label>任务ID:</label><br/>
<input type="text" class="form-control" name="task" value="{$data['task']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.wndflb.com<br/>
利用本系统可以自动完成一些Discuz论坛的每日性领币任务，任务ID就是“申请任务”链接中“task=”后面的数字。</font>
</div>
HTML;
break;

case 'dztask2':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[<a href="#" onclick="signjob_edit('dztask')">密码模式</a>][cookie模式]
{$dzlogin2}
<p><label>任务ID:</label><br/>
<input type="text" class="form-control" name="task" value="{$data['task']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>
利用本系统可以自动完成一些Discuz论坛的每日性领币任务，任务ID就是“申请任务”链接中“task=”后面的数字。</font>
</div>
HTML;
break;

case 'dzol':
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
{$dzlogin2}
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>Discuz挂在线时长将会每5分钟运行一次</font>
</div>
HTML;
break;

case 'pwsign':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[密码模式][<a href="#" onclick="signjob_edit('pwsign2')">cookie模式</a>]
<br/><font color="green">(密码模式仅适用于登录不需要验证码的论坛，cookie模式适用于所有论坛)</font><hr/>
<p><label>网站域名:（不要加“http://”）</label><br/>
<input type="text" class="form-control" name="u" value="{$data['u']}"/></p>
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.fuliba.mobi<br/>
仅限PHPWind9.X系列。</font>
</div>
HTML;
break;

case 'pwsign2':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[<a href="#" onclick="signjob_edit('pwsign')">密码模式</a>][cookie模式]
<br/><font color="green">(密码模式仅适用于登录不需要验证码的论坛，cookie模式适用于所有论坛)</font><hr/>
<p><label>Cookie-ID:(<a href="{$getcookie}phpwind.php" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>
仅限PHPWind9.X系列。</font>
</div>
HTML;
break;

case 'pw2sign':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[密码模式][<a href="#" onclick="signjob_edit('pw2sign2')">cookie模式</a>]
<br/><font color="green">(密码模式仅适用于登录不需要验证码的论坛，cookie模式适用于所有论坛)</font><hr/>
<p><label>网站域名:（不要加“http://”）</label><br/>
<input type="text" class="form-control" name="u" value="{$data['u']}"/></p>
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><label>签到插件类型:</label><br/>
<select class="form-control" name="method">
<option value='1'>每日签到</option>
<option value='2'>每日心情签到</option>
<option value='3'>每日打卡</option>
</select></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网站域名格式如www.fuliba.mobi<br/>
仅限PHPWind8.X系列。</font>
</div>
HTML;
break;

case 'pw2sign2':
vipfunc_check('wsign1');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
[<a href="#" onclick="signjob_edit('pw2sign')">密码模式</a>][cookie模式]
<br/><font color="green">(密码模式仅适用于登录不需要验证码的论坛，cookie模式适用于所有论坛)</font><hr/>
<p><label>Cookie-ID:(<a href="{$getcookie}phpwind.php" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}"/></p>
<p><label>签到插件类型:</label><br/>
<select class="form-control" name="method">
<option value='1'>每日签到</option>
<option value='2'>每日心情签到</option>
<option value='3'>每日打卡</option>
</select></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>
仅限PHPWind8.X系列。</font>
</div>
HTML;
break;

case '115':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
登录你的115网盘账户：<br/>
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
HTML;
break;

case '360yunpan':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>Cookie-ID:(<a href="{$apiserver}sign/360cookie/" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>请先获取Cookie-ID再签到</font>
</div>
HTML;
break;

case 'vdisk':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
登陆你的新浪微博账户：<br/>
<p><label>用户名(邮箱):</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><label>是否转发微博:(签到后发微博会获得更多空间)</label><br/>
<select class="form-control" id="weibo" ivalue="false"><option value="false">否</option><option value="true">是</option></select></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>网络任务执行频率一天1～2次即可，过于频繁地登录会出现提示“登录次数过于频繁”而签到失败，严重的会被冻结账号。<br/><br/>如果出现“<b>需要输入验证码</b>”，请在新浪微博的[账号设置]—[账号安全]—[登录保护]里将本服务器所在地“<b>杭州</b>”加入不需要输入验证码，如下图。</font><img src="http://cyun.aliapp.com/sign/vdisk/screenshot.jpg">
</div>
HTML;
break;

case 'xiami':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
登录你的虾米账户：<br/>
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
HTML;
break;

case 'fuliba':
vipfunc_check('wsign2');
$cookie=substr(md5(time().rand()),8,16);
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>Cookie-ID:(<a href="{$getcookie}?u=www.wndflb.com&cookie={$cookie}" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>本签到工具仅适用于福利吧论坛（www.wndflb.com）签到。<br/>
请先点击获取Cookie-ID，获取过程中如果提示获取失败请多试几次。</font>
</div>
HTML;
break;

case '52pojie':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>Cookie-ID:(<a href="{$getcookie}" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>本签到工具仅适用于吾爱破解论坛（www.52pojie.cn）签到。<br/>
请先点击获取Cookie-ID，获取过程中如果提示获取失败请多试几次。</font>
</div>
HTML;
break;

case 'ucsign':
vipfunc_check('wsign2');
$cookie=substr(md5(time().rand()),8,16);
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>Cookie-ID:(<a href="{$apiserver}sign/getcookie/?u=bbs.uc.cn&cookie={$cookie}" target="_blank">点击获取Cookie-ID</a>)</label><br/>
<input type="text" class="form-control" name="id" value="{$data['id']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>本签到工具仅适用于UC官方论坛（bbs.uc.cn）签到。<br/>
请先点击获取Cookie-ID，获取过程中如果提示获取失败请多试几次。</font>
</div>
HTML;
break;

case 'xiaomi':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>用户名:</label><br/>
<input type="text" class="form-control" name="user" value="{$data['user']}"/></p>
<p><label>密码:</label><br/>
<input type="text" class="form-control" name="pwd" value="{$data['pwd']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>本签到工具仅适用于小米论坛（bbs.xiaomi.cn）签到。</font>
</div>
HTML;
break;

case '3gwen':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>Myid:(<a href="http://cyun.aliapp.com/sign/3gwen/myid.php" target="_blank">提取Myid</a>)</label><br/>
<input type="text" class="form-control" name="myid" value="{$data['myid']}"/></p>
<p><label>签到内容:</label><br/>
<input type="text" class="form-control" name="txt" value="{$data['txt']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="签到"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
[ <a href="#" onclick="showlist('signtask',1)">返回上一页</a> ]
</div>
<div class="well">
<font color="blue">提示：<br/>Myid可在文网登录后的网址中直接获得。</font>
</div>
HTML;
break;

case 'qiu':
vipfunc_check('wsign2');
echo <<<HTML
<div class="panel-body">
<form action="#" role="form">
<p><label>请输入邀请链接:</label><br/>
<input type="text" class="form-control" name="url" value="{$data['url']}"/></p>
<p><input type="button" class="btn btn-primary btn-block" id="signjob_edit" value="提交"/>
<input type="reset" class="btn btn-default btn-block" value="重填" /></p></form>
<a href="#" onclick="showlist('signtask',1)">返回上一页</a>
</div>
<div class="well">
<font color="blue">提示：
可用于刷球球大作战的棒棒糖，每天5个</font>
</div>
HTML;
break;

}
?>
<script>
$(document).ready(function(){
$('#signjob_edit').click(function()
{
	$("#signjob_edit").val('loading');
	ajax.post("ajax.php?mod=signdo&type=<?php echo $type?>&jobid=<?php echo $jobid?>&page=<?php echo $page?>",$('form').serialize(),"html",function(data,status){
		$('#list').html(data);
	});
});
});
</script>
</div>
<?php

break;

}
}else{
	showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}