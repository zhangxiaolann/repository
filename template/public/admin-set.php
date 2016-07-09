<?php
 /*
　*　后台管理文件
*/
if(!defined('IN_CRONLITE'))exit();
$title="后台管理";
include_once(TEMPLATE_ROOT."head.php");

$my=isset($_POST['my'])?$_POST['my']:$_GET['my'];
if($theme=='default')echo '<div class="col-md-9" role="main">';

if ($isadmin==1)
{
if($my=='set_config')
{
echo '<div class="w h"><h3>网站信息配置</h3></div><div class="box">
<form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_config">
<div class="form-group">
<label>*站点名称:</label><br>
<input type="text" class="form-control" name="sitename" value="'.$conf['sitename'].'">
</div>
<div class="form-group">
<label>*网站标题栏后缀:</label><br>
<input type="text" class="form-control" name="sitetitle" value="'.$conf['sitetitle'].'">
</div>
<div class="form-group">
<label>*网站客服QQ:</label><br>
<input type="text" class="form-control" name="kfqq" value="'.$conf['kfqq'].'">
</div>
<div class="form-group">
<label>*开启系统个数(>=1&<=8):</label><br>
<input type="text" class="form-control" name="sysnum" value="'.$conf['sysnum'].'" maxlength="10">
</div>
<div class="form-group">
<label>*单系统任务数上限:</label><br>
<input type="text" class="form-control" name="max" value="'.$conf['max'].'" maxlength="10">
</div>
<div class="form-group">
<label>*列表每页显示个数:</label><br>
<input type="text" class="form-control" name="pagesize" value="'.$conf['pagesize'].'" maxlength="10">
</div>
<div class="form-group">
<label>*批量添加任务上限:</label><br>
<input type="text" class="form-control" name="bulk" value="'.$conf['bulk'].'" maxlength="10">
</div>
<div class="form-group">
<label>注册开关:</label><br><select class="form-control" name="zc""><option value="'.$conf['zc'].'">'.$conf['zc'].'</option><option value="2">2_开放注册(防刷模式)</option><option value="1">1_开放注册</option><option value="0">0_关闭注册</option></select>
<font color="green">建议开启防刷模式，以免被恶意刷注册。主机屋空间请不要开启防刷模式。</font>
</div>
<div class="form-group">
<label>底部随机语录:</label><br><select class="form-control" name="sjyl""><option value="'.$conf['sjyl'].'">'.$conf['sjyl'].'</option><option value="1">1_显示</option><option value="0">0_隐藏</option></select>
<font color="green">开启随机语录可能会影响页面加载效率</font>
</div>
<div class="form-group">
<label>*系统执行频率显示设定:</label><br/><input type="text" class="form-control" name="frequency" value="'.$conf['show'].'"><font color="green">分别对应1～8系统，中间用“|”隔开。此处可以修改用户中心所显示的每个系统的运行频率，实际运行频率由监控频率决定。</font>
</div>
<div class="form-group">
<label>网址关键词屏蔽设定:</label><br/><input type="text" class="form-control" name="block" value="'.$conf['block'].'"><font color="green">每个关键词中间用“|”隔开</font>
</div>
<div class="form-group">
<label>添加QQ屏蔽设定:</label><br/><input type="text" class="form-control" name="qqblock" value="'.$conf['qqblock'].'"><font color="green">每个QQ号中间用“|”隔开</font>
</div>
<div class="form-group">
<label>IP地址屏蔽设定:</label><br/><input type="text" class="form-control" name="banned" value="'.$conf['banned'].'"><font color="green">每个IP地址中间用“|”隔开</font><br/>
<label>反腾讯网址安全监测系统:</label><br><select class="form-control" name="txprotect""><option value="'.$conf['txprotect'].'">'.$conf['txprotect'].'</option><option value="0">0_关闭</option><option value="1">1_开启</option><option value="2">2_开启特定域名</option></select>
<font color="green">此功能可以屏蔽腾讯管家网址安全检测系统访问、屏蔽QQ浏览器访问、屏蔽深圳&上海IP访问。开启此系统可以防止腾讯把秒赞网域名拉入黑名单，但是已拉黑的域名开启此功能就没有用了。</font>
</div>
<div id="frame_set" style="display:none;">
<div class="form-group">
<label>开启反腾讯网址安全监测的域名:</label><br><input type="text" class="form-control" name="txprotect_domain" value="'.$conf['txprotect_domain'].'" placeholder="cron.sgwap.net,cron.aliapp.com">
<font color="green">多个域名请用英文逗号 , 隔开</font>
</div>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form></div>
<script>
          $("select[name=\'txprotect\']").change(function(){
              if($(this).val() == 2){
                $("#frame_set").css("display","inherit");
              }else{
                $("#frame_set").css("display","none");
              }
          });
</script>';
}
elseif($my=='ad_config'){
$sitename=$_POST['sitename'];
$sitetitle=$_POST['sitetitle'];
$sysnum=$_POST['sysnum'];
$max=$_POST['max'];
$pagesize=$_POST['pagesize'];
$zc=$_POST['zc'];
$sjyl=$_POST['sjyl'];
$bulk=$_POST['bulk'];
$show=$_POST['frequency'];
$block=$_POST['block'];
$banned=$_POST['banned'];
$kfqq=$_POST['kfqq'];
$qqblock=$_POST['qqblock'];
$txprotect=$_POST['txprotect'];
$txprotect_domain=$_POST['txprotect_domain'];
if($sitename==NULL or $sysnum==NULL or $max==NULL or $pagesize==NULL or $show==NULL or $bulk==NULL){
showmsg('保存错误,请确保加*项都不为空!',3);
} else {
$ad=$DB->query("update `wjob_config` set `zc` ='$zc',`max` ='$max',`sjyl` ='$sjyl',`pagesize` ='$pagesize',`sitename` ='$sitename',`sitetitle` ='$sitetitle',`sysnum` ='$sysnum',`show` ='$show',`block` ='$block',`bulk` ='$bulk',`banned` ='$banned',`kfqq` ='$kfqq',`qqblock` ='$qqblock',`txprotect` ='$txprotect',`txprotect_domain` ='$txprotect_domain' where `id`='1'");
if($ad){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}}

elseif($my=='set_rw')
{
echo '<div class="w h"><h3>任务运行配置</h3></div>
<div class="box">
<form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_rw">
<div class="form-group">
<label>监控文件运行密钥:</label><br><input type="text" class="form-control" name="cronkey" value="'.$conf['cronkey'].'">
<font color="green">默认为空。设置密钥后，你需要在所有监控文件后面加上 <u>?key=你的密钥</u> ，例如系统一的监控地址就是 <u>'.$siteurl.'cron/job1.php?key=你的密钥</u> 。这样可以防止监控文件被恶意执行</font>
</div>
<div class="form-group">
<label>多线程运行开关:</label><br><input type="text" class="form-control" name="multi" value="'.$conf['multi'].'">
<font color="green">多线程运行开关。分别对应1～8系统，中间用“-”相连。1为开启，0为关闭。多线程在任务总数过多的情况下建议开启，开启多线程可能会加重服务器负担。</font>
</div>
<div class="form-group">
<label>每次/每线程运行个数:</label><br/><input type="text" class="form-control" name="interval" value="'.$conf['interval'].'" maxlength="10">
<font color="green">每次运行任务数是指在单个系统内，每运行一次监控文件(jobx.php)所能够执行的任务数。如果开启多线程后则为每个线程的任务数。为0则默认全部执行。可以根据自己空间的负载情况进行设置。</font>
</div>
<div class="form-group">
<label>使用备用获取说说列表接口:</label><br><select class="form-control" name="getss""><option value="'.$conf['getss'].'">'.$conf['getss'].'</option><option value="0">0_否</option><option value="1">1_是</option></select>
<font color="green">如果本站IP被腾讯屏蔽导致大部分QQ获取说说列表失败，请尝试使用备用获取说说列表接口。</font>
</div>
<div class="form-group">
<label><font color="red">[优先级1]</font>有限循环秒刷配置:</label><br/><input type="text" class="form-control" name="seconds" value="'.$conf['seconds'].'">
<font color="green">分别对应1～8系统，中间用“-”相连。0为关闭该系统秒刷功能，大于0的数则为每运行一次监控文件(jobx.php)所连续循环执行的次数。使用秒刷功能可能会导致空间超负载。</font>
</div>
<div class="form-group">
<label><font color="red">[优先级2]</font>无限循环秒刷配置:</label><br/><input type="text" class="form-control" name="loop" value="'.$conf['loop'].'">
<font color="green">分别对应1～8系统，中间用“-”相连。0为关闭该系统秒刷功能，1为开启。开启后，每运行一次监控文件(jobx.php)可不间断地自动循环运行，最大循环次数因空间而异。</font><br/>
<font color="red">优先级说明：开启优先级1的秒刷配置后优先级2的配置将失效</font>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form></div>';
}
elseif($my=='ad_rw'){
$cronkey=$_POST['cronkey'];
$multi=$_POST['multi'];
$interval=$_POST['interval'];
$seconds=$_POST['seconds'];
$loop=$_POST['loop'];
$getss=$_POST['getss'];
$sql2="ALTER TABLE `wjob_config`
ADD  `cronkey` VARCHAR(150) DEFAULT NULL";
$DB->query($sql2);
if($interval==NULL or $seconds==NULL or $loop==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$ad=$DB->query("update `wjob_config` set `cronkey` ='$cronkey',`multi` ='$multi',`loop` ='$loop',`interval` ='$interval',`seconds` ='$seconds',`getss` ='$getss' where `id`='1'");
if($ad){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}}

elseif($my=='set_mail')
{
echo '<div class="w h"><h3>发信邮箱配置</h3></div>
<div class="box">
<form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_mail">
<div class="form-group">
<label>邮箱账号:</label><br><input type="text" class="form-control" name="mail_name" value="'.$conf['mail_name'].'">
</div>
<div class="form-group">
<label>邮箱密码:</label><br><input type="text" class="form-control" name="mail_pwd" value="'.$conf['mail_pwd'].'">
</div>
<div class="form-group">
<label>邮箱STMP服务器:</label><br><input type="text" class="form-control" name="mail_stmp" value="'.$conf['mail_stmp'].'">
</div>
<div class="form-group">
<label>邮箱STMP端口:</label><br><input type="text" class="form-control" name="mail_port" value="'.$conf['mail_port'].'">
</div>
<font color="green">如果为QQ邮箱需先开通STMP，且要填写QQ邮箱独立密码。邮箱STMP服务器可以百度一下，例如163邮箱的即为 smtp.163.com。邮箱STMP端口默认为25</font><br/><br/>

<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form></div>';
}
elseif($my=='ad_mail'){
$mail_name=$_POST['mail_name'];
$mail_pwd=$_POST['mail_pwd'];
$mail_stmp=$_POST['mail_stmp'];
$mail_port=$_POST['mail_port'];
if($mail_name==NULL or $mail_pwd==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$ad=$DB->query("update `wjob_config` set `mail_name` ='$mail_name',`mail_pwd` ='$mail_pwd',`mail_stmp` ='$mail_stmp',`mail_port` ='$mail_port' where `id`='1'");
if($ad){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}}

elseif($my=='set_api')
{
echo '<div class="w h"><h3>签到/QQ挂机模块API配置</h3></div>
<div class="box">
<form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_api">
<div class="form-group">
<label>签到API服务器:</label><br/><select class="form-control" name="apiserver""><option value="'.$conf['apiserver'].'">'.$conf['apiserver'].'</option><option value="1">1_彩虹官方API一号</option><option value="2">2_彩虹官方API二号</option><option value="3">3_彩虹官方API三号</option><!--option value="4">4_彩虹官方API四号</option--><option value="0">0_本地API</option></select>
<font color="green">彩虹官方API由国内各大应用引擎搭建，速度快，稳定性好。如果你当前使用的签到API无法访问可以在此更换。如果你的空间性能足够好也可以选择使用本地API。</font></div>
<div class="form-group">
<label>QQ挂机类是否使用官方API:</label><br/><select class="form-control" name="qqapiid""><option value="'.$conf['qqapiid'].'">'.$conf['qqapiid'].'</option><option value="0">0_否</option><option value="1">1_是(需授权)</option></select>
<font color="green">建议选否，因为QQ的数量和空间稳定性是呈负相关的。如果你的空间实在无法运行QQ挂机类任务可以尝试使用官方API。</font></div>
<div class="form-group">
<label>QQ登录API服务器:</label><br/><select class="form-control" name="qqloginid""><option value="'.$conf['qqloginid'].'">'.$conf['qqloginid'].'</option><option value="1">1_官方API一号(ECS)</option><option value="2">2_官方API二号(SAE)</option><option value="3">3_官方API三号(ACE)</option><option value="0">0_本地API</option></select>
<font color="green">QQ登录即为添加QQ与更新sid。如果在添加QQ时出现登录成功但获取sid失败，请在此处更换QQ登录API</font></div>
<div class="form-group">
<label>发信API服务器:</label><br/><select class="form-control" name="mail_api""><option value="'.$conf['mail_api'].'">'.$conf['mail_api'].'</option><option value="0">0_本地发信</option><option value="1">1_官方API一号</option><option value="2">2_官方API二号</option></select>
<font color="green">使用此API后，网站将通过官方发信API发送邮件。建议在当前空间不支持发送邮件时使用。</font></div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form></div>';
}
elseif($my=='ad_api'){
$apiserver=$_POST['apiserver'];
$qqapiid=$_POST['qqapiid'];
$qqloginid=$_POST['qqloginid'];
$mail_api=$_POST['mail_api'];
if($apiserver==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$sql2="ALTER TABLE `wjob_config`
ADD  `qqapiid` INT(4) NOT NULL DEFAULT 0";
$DB->query($sql2);
$ad=$DB->query("update `wjob_config` set `apiserver` ='$apiserver',`qqapiid` ='$qqapiid',`qqloginid` ='$qqloginid',`mail_api` ='$mail_api' where `id`='1'");
if($ad){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}}

elseif($my=='set_mm')
{
$user=$DB->get_row("select * from wjob_user where userid='{$conf['adminid']}' limit 1");
echo '<div class="w h"><h3>修改后台密码</h3></div><div class="box"><form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_mm"><input type="hidden" name="password" value="'.$conf['adminpass'].'">管理员账号:<br><input type="text" class="form-control" name="account" value="'.$user['user'].'"><br>管理员密码:<br><input type="text" class="form-control" name="pass" value="'.$user['pass'].'"><br/><input type="submit"
class="btn btn-primary btn-block" value="确定修改"></form></div>';
}

elseif($my=='ad_mm')
{
$account=$_POST['account'];
$pass=$_POST['pass'];
if($account=='' || $pass==''){
showmsg('用户名密码不能为空!',3);
} else {
$ad=$DB->query("update `wjob_user` set `pass`='$pass', where `user`='$account'");
if($ad){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}
}

elseif($my=='set_gg'){
echo '<div class="w h"><h3>广告与公告管理</h3></div><div class="box">
<form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_gg">
<div class="form-group">
<label>首页公告栏:</label><br><textarea class="form-control" name="gg" rows="4">'.$conf['gg'].'</textarea>
</div>
<div class="form-group">
<label>首页强力推荐:</label><br><textarea class="form-control" name="guang" rows="4">'.$conf['guang'].'</textarea>
</div>
<div class="form-group">
<label>首页底部:</label><br><textarea class="form-control" name="bottom" rows="4">'.$conf['bottom'].'</textarea>
</div>
<div class="form-group">
<label>全局底部排版:</label><br><textarea class="form-control" name="footer" rows="4">'.$conf['footer'].'</textarea>
</div>
<input type="submit" class="btn btn-primary btn-block"
value="确定修改"></form></div>';
}

elseif($my=='ad_gg'){
$gg=$_POST['gg'];
$guang=$_POST['guang'];
$bottom=$_POST['bottom'];
$footer=$_POST['footer'];
$ad2=$DB->query("update `wjob_config` set `gg` ='$gg',`guang` ='$guang',`bottom` ='$bottom',`footer` ='$footer' where `id`='1'");
if($ad2){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}

elseif($my=='set_css'){
echo '<div class="w h"><h3>更改系统皮肤样式</h3></div><div class="box">
<form action="index.php?mod=admin-set" method="POST"><input type="hidden" name="my" value="ad_css">
<div class="form-group">
<label>触屏/电脑版皮肤样式:</label><br>
<select class="form-control" name="css2"><option value="'.$conf['css2'].'">'.$conf['css2'].'</option><option value="1">1_Bootstrap原版</option><option value="2">2_Skeumorphism UI</option><option value="3">3_Metro风格Flat UI</option><option value="4">4_高仿谷歌扁平样式</option><option value="5">5_Windows8 Metro UI</option><option value="0">0_禁用触屏版</option></select></div>
<div class="form-group">
<label>手机炫彩版皮肤样式:</label><br>
<select class="form-control" name="css"><option value="'.$conf['css'].'">'.$conf['css'].'</option><option value="1">1_立体炫彩</option><option value="2">2_简洁经典</option><option value="3">3_碧海蓝天</option><option value="4">4_金色年华</option><option value="5">5_高仿chen4.6</option><option value="6">6_七彩阳光</option></select></div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form></div>';
}

elseif($my=='ad_css'){
$css=$_POST['css'];
$css2=$_POST['css2'];
$ad2=$DB->query("update `wjob_config` set `css` ='$css',`css2` ='$css2' where `id`='1'");
if($ad2){showmsg('修改成功!',1);
}else{showmsg('修改失败!'.$DB->error(),4);}
}

elseif($my=='logo'){
echo '<div class="w h"><h3>更改系统LOGO</h3></div><div class="box">';
if($_POST['s']==1){
copy($_FILES['file']['tmp_name'], ROOT.'images/logo.png');
echo "成功上传文件!<br>（可能需要清空浏览器缓存才能看到效果）";
}
echo '<br><form action="index.php?mod=admin-set&my=logo" method="POST" enctype="multipart/form-data"><label for="file"></label><input type="file" name="file" id="file" /><input type="hidden" name="s" value="1" /><br><input type="submit" class="btn btn-primary btn-block" value="确认更改LOGO" /></form><br>现在的LOGO：<br><img src="images/logo.png">';
echo '</div>';
}

elseif($my == 'bj'){
echo '<div class="w h"><h3>更改背景图片</h3></div><div class="box">';
if($_POST['s']==1){
copy($_FILES['file']['tmp_name'], ROOT.'images/b.gif');
echo "成功上传文件!<br>（可能需要清空浏览器缓存才能看到效果）";
}
echo '<br><form action="index.php?mod=admin-set&my=bj" method="POST" enctype="multipart/form-data"><label for="file"></label><input type="file" name="file" id="file" /><input type="hidden" name="s" value="1" /><br><input type="submit" class="btn btn-primary btn-block" value="确认更改背景" /></form><br>现在的背景：<br><img src="images/b.gif"><br>';
echo '</div>';
}

elseif($my == 'bj2'){
echo '<div class="w h"><h3>更改触屏版背景图片</h3></div><div class="box">';
if($_POST['s']==1){
copy($_FILES['file']['tmp_name'], ROOT.'images/fzbeijing.png');
echo "成功上传文件!<br>（可能需要清空浏览器缓存才能看到效果）";
}
echo '<br><form action="index.php?mod=admin-set&my=bj2" method="POST" enctype="multipart/form-data"><label for="file"></label><input type="file" name="file" id="file" /><input type="hidden" name="s" value="1" /><br><input type="submit" class="btn btn-primary btn-block" value="确认更改背景" /></form><br>现在的背景：<br><img src="images/fzbeijing.png"><br>';
echo '</div>';
}

elseif($my == 'info'){
echo'<div class="w h"><h3>程序信息</h3></div>';
echo'<div class="box">';
echo'版权所有：消失的彩虹海<br/>ＱＱ：1277180438<br/>当前版本：V5.10 (Build '.VERSION.')<br/>官方网站：<a href="http://blog.cccyun.cn">blog.cccyun.cn</a><br/><a href="http://cron.sgwap.net">cron.aliapp.com</a>
';
echo'</div>';
}

elseif($my == 'help'){
echo'<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">任务监控说明</h3></div>';
echo'<div class="panel-body box">';
if(!empty($conf['cronkey']))$ext='&key='.$conf['cronkey'];
echo '你可以根据需要监控以下文件：<br/><font color=brown>';
for($i=1;$i<=$conf['sysnum'];$i++) {
	echo "<li class=\"list-group-item\">{$siteurl}cron/job.php?sys={$i}{$ext}</li>";
}
echo "<li class=\"list-group-item\">{$siteurl}cron/newsid.php?{$ext}</font></li>";
echo'
监控完监控网址后即可执行任务！<br/>
<font color="red">如果你的空间开启了防CC攻击，或者使用了加速乐等CDN，请将本站服务器IP:'.$_SERVER["SERVER_ADDR"].' 加入到白名单中，否则任务将无法执行。</font><br/>
推荐监控系统:<br/>
<a target="_blank" href="http://cron.sgwap.net/">http://cron.sgwap.net/</a>（需要彩虹币）<br/>
<a target="_blank" href="http://console.aliyun.com/jiankong/">http://console.aliyun.com/jiankong/</a>（需实名认证）<br/>
<a target="_blank" href="http://jk.cloud.360.cn/">http://jk.cloud.360.cn/</a><br/>
<a target="_blank" href="http://bce.baidu.com/product/bcm.html">http://bce.baidu.com/product/bcm.html</a><br/>
';
echo'</div></div>';
}

}
else
{
showmsg('后台管理登录失败。请以管理员身份 <a href="index.php?mod=login">重新登录</a>！',3);
}
echo'<div class="copy">';
echo date("Y年m月d日 H:i:s");
echo'<br>';
echo'<a href="index.php?mod=admin">返回后台管理</a>-<a href="index.php">返回首页</a>';
include(ROOT.'includes/foot.php');
echo'</div></div></div></div></body></html>';
?>