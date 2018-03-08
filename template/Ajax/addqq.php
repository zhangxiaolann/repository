<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
if($conf['active']!=0)phone_check();
switch($act) {

case 'add':
	$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):daddslashes($_POST['qq']);
	$qpwd=isset($_GET['qpwd'])?daddslashes($_GET['qpwd']):daddslashes($_POST['qpwd']);
	$qsid=isset($_GET['qsid'])?daddslashes($_GET['qsid']):daddslashes($_POST['qsid']);
	$skey=isset($_GET['skey'])?daddslashes($_GET['skey']):daddslashes($_POST['skey']);
	$pskey=isset($_GET['pskey'])?daddslashes($_GET['pskey']):daddslashes($_POST['pskey']);
	$superkey=isset($_GET['superkey'])?daddslashes($_GET['superkey']):daddslashes($_POST['superkey']);
	$qpwd=authcode($qpwd,'ENCODE',SYS_KEY);
	if($qq==""||$skey==""||$pskey==""){
		exit('{"code":-1,"msg":"参数不能为空！"}');
	}
	$rowm1=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
	if($rowm1['qq']==''){}else{
		qqtx($qq,$skey);
		if($rowm1['uid']!=$uid && $isadmin!=1) {
			$sqls=",uid='$uid'";
			$DB->query("UPDATE ".DBQZ."_qqjob SET uid='$uid' WHERE qq='$qq'");
		}
		if($rowm1['qqlevel'] && $_POST['qpwd']){
			get_curl($dgapi.'api/submit.php?act=update&id='.$rowm1['qqlevel'].'&qq='.$qq.'&pwd='.urlencode(authcode($_POST['qpwd'],'ENCODE','CLOUDKEY')).'&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode);
		}
		if(!$_POST['qpwd'])$qpwd=$rowm1['pw'];
		$sql="update `".DBQZ."_qq` set `pw` ='$qpwd',`sid` ='$qsid',`skey` ='$skey',`pskey` ='$pskey',`superkey` ='$superkey',`status` ='1',`status2` ='1',`time`='$date'{$sqls} where `qq`='$qq'";
		$sds=$DB->query($sql);
		$DB->query("UPDATE ".DBQZ."_qqjob SET fail='0' WHERE qq='$qq'");
		if($sds)exit('{"code":1,"msg":"成功更新SID&SKEY&P_skey！"}');
		else exit('{"code":0,"msg":"更新SID&SKEY&P_skey失败！"}');
	}
	if(in_array($qq,explode("|",$conf['qqblock']))){
		exit('{"code":0,"msg":"您所添加的QQ不在系统允许范围内，请联系网站管理员！"}');
	}
	$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE uid='{$uid}'");
	if($conf['peie_open']==1){
		if($gls>=$row['peie']) {
			exit('{"code":0,"msg":"您添加的QQ数量已超过你的配额，无法继续添加！请购买配额。"}');
		}
	}else{
		if($gls>=$conf['qqlimit'] && $isadmin!=1 && $isvip==0 && $conf['qqlimit']!=0){
			exit('{"code":0,"msg":"您添加的QQ数量已超过系统最大限制('.$conf['qqlimit'].'个)，无法继续添加！<br/>开通VIP后将解除这个限制。"}');
		}
		if($gls>=$conf['qqlimit2'] && $isadmin!=1 && $conf['qqlimit2']!=0){
			exit('{"code":0,"msg":"您添加的QQ数量已超过系统最大限制('.$conf['qqlimit'].'个)，无法继续添加！"}');
		}
	}
	if(!checksid($qq,$skey,$pskey)){
		exit('{"code":0,"msg":"添加失败。SKEY无效，请再试！"}');
	}
	if(!$conf['qqtx'])$conf['qqtx']='您的QQ已成功在 '.$conf['sitename'].' 添加，感谢您的使用！';
		qqtx($qq,$skey,$conf['qqtx']);
	if($conf['addbuluo']){
		include_once ROOT.'qq/qqsign.class.php';
		$qzone=new qqsign($qq,$sid,$skey);
		$qzone->addbuluo($superkey,$conf['addbuluo']);
	}
	if($conf['qzlike']){
		$qzlikes=explode('|',$conf['qzlike']);
		foreach($qzlikes as $qzlike){
			$gtk=getGTK($pskey);
			$cookie='pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$skey.'; p_skey='.$pskey.'; p_uin=o0'.$qq.';';
			$ua='Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
			$url='http://h5.qzone.qq.com/proxy/domain/w.qzone.qq.com/cgi-bin/likes/doLike?g_tk='.$gtk;
			$post='qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F'.$qzlike.'&url=http%3A%2F%2Fuser.qzone.qq.com%2F'.$qzlike.'&cid='.$gtk.'&from=brand&format=json&scene=5&fupdate=1';
			get_curl($url,$post,'http://user.qzone.qq.com/'.$qzlike,$cookie,0,$ua);
		}
	}
	if($conf['addfriend']){
		include_once ROOT.'qq/qzone.class.php';
		$qzone=new qzone($qq,$sid,$skey,$pskey);
		$qzone->addfriend($conf['addfriend']);
	}
	$sql = "INSERT INTO `".DBQZ."_qq`(`uid`,`qq`,`pw`,`sid`,`skey`,`pskey`,`superkey`,`status`,`status2`,`time`) VALUES ('{$uid}','{$qq}','{$qpwd}','{$qsid}','{$skey}','{$pskey}','{$superkey}','1','1','{$date}')";
	$sds=$DB->query($sql);
	if($sds){
		$data='a:1:{s:6:"forbid";s:0:"";}';
		if($conf['addzan'])
			$DB->query("insert into `".DBQZ."_qqjob` (`uid`,`qq`,`type`,`sign`,`method`,`data`,`lasttime`,`nexttime`,`pl`,`start`,`stop`,`sysid`) values ('{$uid}','{$qq}','zan','0','3','{$data}','".time()."','".time()."','0','0','24','1')");
		exit('{"code":1,"msg":"QQ账号已成功添加！"}');
	}else exit('{"code":0,"msg":"QQ账号添加失败！'.$DB->error().'"}');
break;

case 'del':

	$qq=daddslashes($_GET['qq']);
	$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
	if($row['uid']!=$uid && $isadmin==0 && $isdeputy==0) {
		exit('{"code":0,"msg":"你只能操作自己的QQ哦！"}');
	}
	if($row['qqlevel'])
		get_curl($dgapi.'api/submit.php?act=close&id='.$row['qqlevel'].'&uin='.$qq.'&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode);
	$sql="DELETE FROM ".DBQZ."_qq WHERE qq='$qq'";
	$sql2="DELETE FROM ".DBQZ."_qqjob WHERE qq='$qq'";
	if($DB->query($sql) && $DB->query($sql2))
		exit('{"code":1,"msg":"QQ '.$qq.' 已成功删除！"}');
	else
		exit('{"code":0,"msg":"QQ '.$qq.' 删除失败！"}');
break;

case 'update':
$qq=daddslashes($_GET['qq']);
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
}
$qpwd=authcode($row['pw'],'DECODE',SYS_KEY);

?>
<script>
var siteurl='<?php echo $qqlogin ?>';
</script>
<script src="qq/getsid/login.js?v=<?php echo VERSION ?>"></script>
<script src="qq/getsid/collect.js?v=<?php echo VERSION ?>"></script>
<script src="qq/getsid/getsid.js?v=<?php echo VERSION ?>"></script>
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>更新QQ身份识别码</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<div id="load" class="alert alert-info">正在登录，请稍候...</div>
<div id="login" style="display:none;">
<div class="form-group">
<label>QQ帐号:</label><br/>
<input type="text" id="uin" value="<?php echo $qq ?>" class="form-control" disabled/>
</div>
<div class="form-group">
<label>QQ密码:</label><br/>
<input type="text" id="pwd" value="<?php echo $qpwd ?>" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}"/>
</div>
</div>
<script>
checkvc();</script>
<div class="form-group code" style="display:none;">
<label>输入验证码:</label>
<div id="codeimg"></div>
<input type="text" id="code" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}">
<br/>
</div>
<br/><button type="button" id="submit" class="btn btn-primary btn-block">确定</button>
<br/><a href="#" onclick="showlist('qqlist')">返回QQ挂机列表</a>
</div>
</div>
<?php

break;

case 'dama':
	$qq=daddslashes($_GET['qq']);
	$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
	if($row['status']!=4 && $row['status2']!=4) {
		showmsg('此QQ不在待打码列表！');
	}
?>
<script src="qq/getsid/collect.js?v=<?php echo VERSION ?>"></script>
<script src="qq/getsid/getsid2.js?v=<?php echo VERSION ?>"></script>
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>协助更新QQ身份识别码</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('damalist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<div id="load" class="alert alert-info"></div>
<div id="login" style="display:none;">
<input type="text" id="uin" value="<?php echo $row['qq'] ?>"/>
</div>
<script>
checkvc();</script>
<div class="form-group code" style="display:none;">
<label>输入验证码:</label>
<div id="codeimg"></div>
<input type="text" id="code" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}">
<br/>
</div>
<br/><button type="button" id="submit" class="btn btn-primary btn-block">确定</button>
<br/><a href="#" onclick="showlist('damalist')">返回待打码QQ列表</a>
</div>
</div>
<?php

break;

case 'login':
$qq=$_GET['qq']?$_GET['qq']:null;

?>
<script>
var siteurl='<?php echo $qqlogin ?>';
</script>
<script src="qq/getsid/login.js?v=<?php echo VERSION ?>"></script>
<script src="qq/getsid/collect.js?v=<?php echo VERSION ?>"></script>
<script src="qq/getsid/getsid.js?v=<?php echo VERSION ?>"></script>
<div class="col-md-6">
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-list red"></i><span class="break"></span>添加说明</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<p class="bg-warning" style="padding: 10px; font-size: 90%;">
因服务器所在地不是你的QQ常用登录地，首次使用可能会因为异地登录而冻结或风险提示！改密码即可解除，添加QQ前请设置好密保。当前服务器所在地：<?php echo $conf['serverlocal']?$conf['serverlocal']:get_ip_city($_SERVER['SERVER_ADDR']);?>
</p>
<p class="bg-info" style="padding: 10px; font-size: 90%;">
登录成功 获取SID失败，是由于QQ出现异常，到QQ安全中心解除异常即可。若创建连接失败请再试。
</p>
<p class="bg-success" style="padding: 10px; font-size: 90%;">
如果QQ开启了网页登录保护，需要关闭才能添加。
</p>
</div></div>
</div>
<div class="col-md-6">
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>添加一个QQ账号&nbsp;【<a href="#" onclick="addqq('qrlogin')" style="color:#ccc">扫码登录</a>】</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<div id="load" class="alert alert-info" style="font-weight:bold;display:none;"></div>
<div id="login" class="list-group-item">
<div class="form-group">
<div class="input-group"><div class="input-group-addon">QQ帐号</div>
<input type="text" id="uin" value="<?php echo $qq ?>" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}"/>
</div></div>
<div class="form-group">
<div class="input-group"><div class="input-group-addon">QQ密码</div>
<input type="text" id="pwd" value="" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}"/>
</div></div>
<div class="form-group code" style="display:none;">
<div id="codeimg"></div>
<div class="input-group"><div class="input-group-addon">验证码</div>
<input type="text" id="code" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}" placeholder="输入验证码">
</div>
</div>
<br/><button type="button" id="submit" class="btn btn-primary btn-block">确认添加</button>
<br/><a href="#" onclick="showlist('qqlist')">返回QQ挂机列表</a>
</div>
</div>
</div>
</div>
<?php

break;

case 'qrlogin':
$qq=$_GET['qq']?$_GET['qq']:null;

?>
<script>
var siteurl='<?php echo $qqlogin ?>';
</script>
<script src="qq/getsid/qrlogin.js?v=<?php echo VERSION ?>"></script>
<div class="col-md-6">
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-list red"></i><span class="break"></span>添加说明</h6>
		<div class="panel-actions">
			<a href="#" onclick="cleartime();showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<p class="bg-info" style="padding: 10px; font-size: 90%;">
使用扫码添加QQ的无法自动更新SID和SKEY！
</p>
<p class="bg-warning" style="padding: 10px; font-size: 90%;">
因服务器所在地不是你的QQ常用登录地，首次使用可能会因为异地登录而冻结或风险提示！改密码即可解除，添加QQ前请设置好密保。当前服务器所在地：<?php echo $conf['serverlocal']?$conf['serverlocal']:get_ip_city($_SERVER['SERVER_ADDR']);?>
</p>
<p class="bg-success" style="padding: 10px; font-size: 90%;">
如果QQ开启了网页登录保护，需要关闭才能添加。用QQ安全中心扫码添加不会被拉入保护模式
</p>
</div></div>
</div>
<div class="col-md-6">
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>添加一个QQ账号&nbsp;【<a href="#" onclick="cleartime();addqq('login')" style="color:#ccc">普通添加</a>】</h6>
		<div class="panel-actions">
			<a href="#" onclick="cleartime();showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body" style="text-align: center;">
		<div class="list-group">
			<div class="list-group-item list-group-item-info" style="font-weight: bold;" id="login">
				<span id="loginmsg">使用QQ手机版扫描二维码</span><span id="loginload" style="padding-left: 10px;color: #790909;">.</span>
			</div>
			<div class="list-group-item" id="qrimg">
			</div>
		</div>
		<br/><a href="#" onclick="cleartime();showlist('qqlist')">返回QQ挂机列表</a>
</div>
</div>
</div>
<?php

break;

case 'pushlogin':
$qq=$_GET['qq']?$_GET['qq']:null;

?>
<script>
var siteurl='<?php echo $qqlogin ?>';
</script>
<script src="qq/getsid/pushlogin.js?v=<?php echo VERSION ?>"></script>
<div class="col-md-6">
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-list red"></i><span class="break"></span>添加说明</h6>
		<div class="panel-actions">
			<a href="#" onclick="cleartime();showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<p class="bg-info" style="padding: 10px; font-size: 90%;">
要添加的QQ必须已经登录手机QQ，才能使用快捷登录。
</p>
<p class="bg-warning" style="padding: 10px; font-size: 90%;">
因服务器所在地不是你的QQ常用登录地，首次使用可能会因为异地登录而冻结或风险提示！改密码即可解除，添加QQ前请设置好密保。当前服务器所在地：<?php echo $conf['serverlocal']?$conf['serverlocal']:get_ip_city($_SERVER['SERVER_ADDR']);?>
</p>
<p class="bg-success" style="padding: 10px; font-size: 90%;">
使用快捷登录添加QQ的无法自动更新SID和SKEY！
</p>
</div></div>
</div>
<div class="col-md-6">
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>添加一个QQ账号&nbsp;【<a href="#" onclick="cleartime();addqq('login')" style="color:#ccc">普通添加</a>】</h6>
		<div class="panel-actions">
			<a href="#" onclick="cleartime();showlist('qqlist',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body" style="text-align: center;">
		<div id="load" class="alert alert-info" style="font-weight:bold;display:none;"></div>
		<div class="list-group-item" id="login">
			<div class="form-group">
			<div class="input-group"><div class="input-group-addon">QQ帐号</div>
			<input type="text" id="uin" value="<?php echo $qq ?>" class="form-control" onkeydown="if(event.keyCode==13){submit.click()}"/>
			</div></div>
		</div>
		<br/><button type="button" id="submit" class="btn btn-primary btn-block">发送验证</button>
		<br/><a href="#" onclick="cleartime();showlist('qqlist')">返回QQ挂机列表</a>
</div>
</div>
</div>
<?php

break;

}
}else{
	showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}