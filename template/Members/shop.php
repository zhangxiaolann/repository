<?php
if(!defined('IN_CRONLITE'))exit();
$title='自助购买';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-shopping-cart"></i>自助购买</a></li>';
include TEMPLATE_ROOT."head.php";

if($islogin==1){

$act=isset($_GET['act'])?$_GET['act']:1;

echo '<div class="col-md-12 center-block" role="main">';


if(OPEN_DAMA==1)$addstr1='<li><a href="index.php?mod=dama">协助打码</a></li>';
if($act==1) {
	echo '<ul class="nav nav-tabs">
	  <li class="active"><a href="#">自助购买</a></li>
	  <li><a href="index.php?mod=shop&act=2">自助兑换</a></li>
	  '.$addstr1.'
</ul>';
} elseif($act==2) {
	echo '<ul class="nav nav-tabs">
	  <li><a href="index.php?mod=shop&act=1">自助购买</a></li>
	  <li class="active"><a href="#">自助兑换</a></li>
	  '.$addstr1.'
</ul>';
} elseif($act==3) {
	echo '<ul class="nav nav-tabs">
	  <li><a href="index.php?mod=shop&act=1">自助购买</a></li>
	  <li><a href="index.php?mod=shop&act=2">自助兑换</a></li>
	  '.$addstr1.'
</ul>';
}
echo '</div>';

if(isset($_POST['km'])){
$km=daddslashes($_POST['km']);
$qq=daddslashes($_POST['qq']);
$myrow=$DB->get_row("select * from ".DBQZ."_kms where km='$km' limit 1");
$kid=$myrow['id'];
$kind=$myrow['kind'];
$kmname=array('','充值卡','VIP卡','试用卡','配额卡','代理余额卡');

if(!$myrow)
{
showmsg('此'.$kmname[$kind].'密不存在！',3);
exit;
}
if($myrow['isuse']==1){
showmsg('此'.$kmname[$kind].'密已被使用！',3);
exit;
}

if($kind==1) {
	$sql=$DB->query("update ".DBQZ."_user set coin=coin+{$myrow['value']} where user='".$gl."'");
	if($sql){
		$DB->query("update `".DBQZ."_kms` set `isuse` ='1',`user` ='$gl',`usetime` ='$date' where `id`='$kid'");
		showmsg('<font color="red">'.$myrow['value'].'</font> '.$conf['coin_name'].'充值成功！<br/>你当前拥有：<font color="red">'.($row['coin']+$myrow['value']).'</font> '.$conf['coin_name'].'',1);
	}else{
		showmsg('充值失败！'.$DB->error(),4);
	}
} elseif($kind==2) {
	if($myrow['value']==0) {
		$sql=$DB->query("update ".DBQZ."_user set vip='2',active='1' where userid='".$uid."'");
		if($conf['vipmode']==1)
			$sql=$DB->query("update ".DBQZ."_qq set vip='2' where qq='".$qq."'");
		$myrow['value']='无限';
	} else {
		if($isvip==1) $vipdate = date("Y-m-d", strtotime("+ {$myrow['value']} months", strtotime($row['vipdate'])));
		else $vipdate = date("Y-m-d", strtotime("+ {$myrow['value']} months"));
		$sql=$DB->query("update ".DBQZ."_user set vip='1',vipdate='$vipdate',active='1' where userid='".$uid."'");
		if($conf['vipmode']==1){
			if(empty($qq))showmsg('没有可续费的QQ，请先添加QQ之后再使用卡密续费！',3);
			$qqrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
			if(!$qqrow)showmsg('系统上没有此QQ，请先添加QQ之后再使用卡密续费！',4);
			if($qqrow['vip']==1 && $qqrow['vipdate']>date("Y-m-d")) $vipdate = date("Y-m-d", strtotime("+ {$myrow['value']} months", strtotime($qqrow['vipdate'])));
			else $vipdate = date("Y-m-d", strtotime("+ {$myrow['value']} months"));
			$sql=$DB->query("update ".DBQZ."_qq set vip='1',vipdate='$vipdate' where qq='".$qq."'");
		}
	}
	if($sql){
		$DB->query("update `".DBQZ."_kms` set `isuse` ='1',`user` ='$gl',`usetime` ='$date' where `id`='$kid'");
		showmsg('VIP开通/续期成功！<br/>成功开通/续期 <font color="red">'.$myrow['value'].'</font> 个月VIP，你的VIP到期日期:'.$vipdate,1);
	}else{
		showmsg('VIP开通/续期失败！'.$DB->error(),4);
	}
} elseif($kind==3) {
	if($isvip==0) {
		if($DB->get_row("SELECT * FROM ".DBQZ."_kms WHERE kind='3' and user='$gl' LIMIT 1")){
			showmsg('VIP试用开通失败！您已使用过试用卡！',4);
			exit;
		}
		$vipdate = date("Y-m-d", strtotime("+ {$myrow['value']} days"));
		$sql=$DB->query("update ".DBQZ."_user set vip='1',vipdate='$vipdate' where userid='".$uid."'");
		if($sql){
			$DB->query("update `".DBQZ."_kms` set `isuse` ='1',`user` ='$gl',`usetime` ='$date' where `id`='$kid'");
			showmsg('VIP试用开通成功！<br/>成功开通 <font color="red">'.$myrow['value'].'</font> 天VIP，你的VIP到期日期:'.$vipdate,1);
		}else{
			showmsg('VIP试用开通失败！'.$DB->error(),4);
		}
	}else{
		showmsg('你已是VIP，不能使用试用卡！',3);
	}
} elseif($kind==4) {
	$sql=$DB->query("update ".DBQZ."_user set peie=peie+{$myrow['value']} where userid='".$uid."'");
	if($sql){
		$DB->query("update `".DBQZ."_kms` set `isuse` ='1',`user` ='$gl',`usetime` ='$date' where `id`='$kid'");
		showmsg('<font color="red">'.$myrow['value'].'</font> 个配额增加成功！<br/>你当前拥有：<font color="red">'.($row['peie']+$myrow['value']).'</font> 个配额。',1);
	}else{
		showmsg('增加配额失败！'.$DB->error(),4);
	}
} elseif($kind==5) {
	$sql=$DB->query("update ".DBQZ."_user set daili=1,daili_rmb=daili_rmb+{$myrow['value']} where userid='".$uid."'");
	if($sql){
		$DB->query("update `".DBQZ."_kms` set `isuse` ='1',`user` ='$gl',`usetime` ='$date' where `id`='$kid'");
		showmsg('<font color="red">'.$myrow['value'].'</font> RMB代理余额增加成功！<br/>你当前拥有：<font color="red">'.($row['daili_rmb']+$myrow['value']).'</font> RMB代理余额。',1);
	}else{
		showmsg('增加配额失败！'.$DB->error(),4);
	}
}

}
elseif(isset($_POST['value'])){
if($_POST['to']=='vip') {
	$value=intval($_POST['value']);
	$qq=daddslashes($_POST['qq']);
	if($conf['coin_tovip']==0)
	{
		showmsg('当前站点未开启虚拟币兑换VIP功能！',3);
	}
	if(!is_numeric($value) || $value<=0 || $value>12)
	{
		showmsg('月数只能为数字，不能超过12个月！',3);
	}
	if($isvip==2&&$conf['vipmode']==0)
	{
		showmsg('你已经是永久VIP，不能兑换！',3);
	}
	$need=$value*$conf['coin_tovip'];
	if($need>$row['coin'])
	{
		showmsg('兑换'.$value.'个月VIP需要'.$need.$conf['coin_name'].'，你只有'.$row['coin'].$conf['coin_name'].'！',3);
	}
	if($isvip==1) $vipdate = date("Y-m-d", strtotime("+ {$value} months", strtotime($row['vipdate'])));
	else $vipdate = date("Y-m-d", strtotime("+ {$value} months"));
	$sql=$DB->query("update ".DBQZ."_user set vip='1',vipdate='$vipdate' where user='".$gl."'");
	if($conf['vipmode']==1){
		if(empty($qq))showmsg('没有可续费的QQ，请先添加QQ之后再使用卡密续费！',3);
		$qqrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
		if(!$qqrow)showmsg('系统上没有此QQ，请先添加QQ之后再使用卡密续费！',4);
		if($qqrow['vip']==1 && $qqrow['vipdate']>date("Y-m-d")) $vipdate = date("Y-m-d", strtotime("+ {$value} months", strtotime($qqrow['vipdate'])));
		else $vipdate = date("Y-m-d", strtotime("+ {$value} months"));
		$sql=$DB->query("update ".DBQZ."_qq set vip='1',vipdate='$vipdate' where qq='".$qq."'");
	}
	if($sql){
		$DB->query("update ".DBQZ."_user set coin=coin-{$need} where user='".$gl."'");
		showmsg('VIP开通/续期成功！<br/>成功开通/续期 <font color="red">'.$value.'</font> 个月VIP，你的VIP到期日期:'.$vipdate,1);
	}else{
		showmsg('VIP开通/续期失败！'.$DB->error(),4);
	}
} elseif($_POST['to']=='vip2') {
	$value=intval($_POST['value']);
	$qq=daddslashes($_POST['qq']);
	if($conf['coin_tovip2']==0)
	{
		showmsg('当前站点未开启虚拟币兑换VIP功能！',3);
	}
	if(!is_numeric($value) || $value<=0 || $value>30)
	{
		showmsg('天数只能为数字，不能超过30天！',3);
	}
	if($isvip==2&&$conf['vipmode']==0)
	{
		showmsg('你已经是永久VIP，不能兑换！',3);
	}
	$need=$value*$conf['coin_tovip2'];
	if($need>$row['coin'])
	{
		showmsg('兑换'.$value.'天VIP需要'.$need.$conf['coin_name'].'，你只有'.$row['coin'].$conf['coin_name'].'！',3);
	}
	if($isvip==1) $vipdate = date("Y-m-d", strtotime("+ {$value} days", strtotime($row['vipdate'])));
	else $vipdate = date("Y-m-d", strtotime("+ {$value} days"));
	$sql=$DB->query("update ".DBQZ."_user set vip='1',vipdate='$vipdate' where user='".$gl."'");
	if($conf['vipmode']==1){
		$qqrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
		if($qqrow['vip']==1 && $qqrow['vipdate']>date("Y-m-d")) $vipdate = date("Y-m-d", strtotime("+ {$value} days", strtotime($qqrow['vipdate'])));
		else $vipdate = date("Y-m-d", strtotime("+ {$value} days"));
		$sql=$DB->query("update ".DBQZ."_qq set vip='1',vipdate='$vipdate' where qq='".$qq."'");
	}
	if($sql){
		$DB->query("update ".DBQZ."_user set coin=coin-{$need} where user='".$gl."'");
		showmsg('VIP开通/续期成功！<br/>成功开通/续期 <font color="red">'.$value.'</font> 天VIP，你的VIP到期日期:'.$vipdate,1);
	}else{
		showmsg('VIP开通/续期失败！'.$DB->error(),4);
	}
} elseif($_POST['to']=='peie') {
	$value=intval($_POST['value']);
	if($conf['coin_topeie']==0)
	{
		showmsg('当前站点未开启虚拟币兑换配额功能！',3);
	}
	if(!is_numeric($value) || $value<=0 || $value>10)
	{
		showmsg('配额只能为数字，且一次性只能增加10个配额！',3);
	}
	$need=$value*$conf['coin_topeie'];
	if($need>$row['coin'])
	{
		showmsg('兑换'.$value.'个配额需要'.$need.$conf['coin_name'].'，你只有'.$row['coin'].$conf['coin_name'].'！',3);
	}
	$sql=$DB->query("update ".DBQZ."_user set peie=peie+{$value} where user='".$gl."'");
	if($sql){
		$DB->query("update ".DBQZ."_user set coin=coin-{$need} where user='".$gl."'");
		showmsg('增加配额成功！<br/>成功增加 <font color="red">'.$value.'</font> 个配额。',1);
	}else{
		showmsg('增加配额失败！'.$DB->error(),4);
	}
}
}
else
{
if($act==1)
{
echo '<div class="col-md-6 col-xs-12">';
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">购买公告</h3></div><div class="panel-body box">';
echo $conf['shop'].'
</div></div>';

if($conf['vipmode']!=1){
echo '<div class="panel panel-primary"><div class="panel-body box">';
echo '<li class="list-group-item"><b>你当前拥有：</b><font color="red">'.$row['coin'].'</font> '.$conf['coin_name'].'</li>';
if($isvip==1)$vipstatus='到期时间:<font color="green">'.$row['vipdate'].'</font>';
elseif($isvip==2)$vipstatus='<font color="green">永久 VIP</font>';
else $vipstatus='<font color="red">非 VIP</font>';
echo '<li class="list-group-item"><b>VIP状态：</b>'.$vipstatus.'</li>';
if($conf['peie_open'])
	echo '<li class="list-group-item"><b>你当前拥有：</b><font color="red">'.$row['peie'].'</font> 个QQ配额</li>';
echo '</div></div>';
}
echo '</div>';

echo '<div class="col-md-6 col-xs-12">';
if($conf['alipay_api']||$conf['tenpay_api']||$conf['wxpay_api']||$conf['qqpay_api']){
echo '<div class="panel panel-success">
<div class="panel-heading w h"><h3 class="panel-title">在线购买</h3></div><div class="panel-body box">';
if($conf['vipmode']==1) {
	$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE uid='{$uid}' order by id desc");
	$contents='';
	while($myrow = $DB->fetch($rs)) $contents.='<option value="'.$myrow['qq'].'">'.$myrow['qq'].'</option>';
	echo '
<div class="form-group">
<label>选择商品：</label><br>
<select class="form-control" name="shopid">';
if($buy_rules[0])echo '<option value="0">1天试用VIP会员('.$buy_rules[0].'元)</option>';
if($buy_rules[1])echo '<option value="1">1个月VIP会员('.$buy_rules[1].'元)</option>';
if($buy_rules[2])echo '<option value="2">3个月VIP会员('.$buy_rules[2].'元)</option>';
if($buy_rules[3])echo '<option value="3">半年VIP会员('.$buy_rules[3].'元)</option>';
if($buy_rules[4])echo '<option value="4">一年VIP会员('.$buy_rules[4].'元)</option>';
if($buy_rules[5])echo '<option value="5">永久VIP会员('.$buy_rules[5].'元)</option>';
if($daili_rules[10])echo '<option value="12">开通代理商并赠送'.$daili_rules[11].'余额('.$daili_rules[10].'元)</option>';
echo '</select></div>
<div class="form-group">
<label>选择要续期的QQ：</label><br>
<select class="form-control" name="qq" '.(isset($_GET['qq'])?'default="'.$_GET['qq'].'"':null).'>
'.$contents.'
</select></div><div class="form-group text-center">';
if($conf['alipay_api'])echo '<button type="submit" class="btn btn-default" id="buy_alipay"><img src="images/icon/alipay.ico" class="logo">支付宝</button>&nbsp;';
if($conf['qqpay_api'])echo '<button type="submit" class="btn btn-default" id="buy_qqpay"><img src="images/icon/qqpay.ico" class="logo">QQ钱包</button>&nbsp;';
if($conf['wxpay_api'])echo '<button type="submit" class="btn btn-default" id="buy_wxpay"><img src="images/icon/wechat.ico" class="logo">微信支付</button>&nbsp;';
if($conf['tenpay_api'])echo '<button type="submit" class="btn btn-default" id="buy_tenpay"><img src="images/icon/tenpay.ico" class="logo">财付通</button>&nbsp;';
echo '</div>';
}else{
echo '
<div class="form-group">
<label>选择商品：</label><br>
<select class="form-control" name="shopid">';
if($buy_rules[0])echo '<option value="0">1天试用VIP会员('.$buy_rules[0].'元)</option>';
if($buy_rules[1])echo '<option value="1">1个月VIP会员('.$buy_rules[1].'元)</option>';
if($buy_rules[2])echo '<option value="2">3个月VIP会员('.$buy_rules[2].'元)</option>';
if($buy_rules[3])echo '<option value="3">半年VIP会员('.$buy_rules[3].'元)</option>';
if($buy_rules[4])echo '<option value="4">一年VIP会员('.$buy_rules[4].'元)</option>';
if($buy_rules[5])echo '<option value="5">永久VIP会员('.$buy_rules[5].'元)</option>';
if($buy_rules[6])echo '<option value="6">1个QQ配额('.$buy_rules[6].'元)</option>';
if($buy_rules[7])echo '<option value="7">3个QQ配额('.$buy_rules[7].'元)</option>';
if($buy_rules[8])echo '<option value="8">5个QQ配额('.$buy_rules[8].'元)</option>';
if($buy_rules[9])echo '<option value="9">10个QQ配额('.$buy_rules[9].'元)</option>';
if($rules[0])echo '<option value="10">充值'.$conf['coin_name'].'</option>';
if($daili_rules[10])echo '<option value="12">开通代理商并赠送'.$daili_rules[11].'余额('.$daili_rules[10].'元)</option>';
echo '</select></div>
<div class="form-group" style="display:none;" id="display_coin">
<label>要充值的金额：（1元='.$rules[0].$conf['coin_name'].'）</label><br>
<input type="text" class="form-control" name="qq" autocomplete="off" placeholder="输入要充值的金额"></div><div class="form-group text-center">';
if($conf['alipay_api'])echo '<button type="submit" class="btn btn-default" id="buy_alipay"><img src="images/icon/alipay.ico" class="logo">支付宝</button>&nbsp;';
if($conf['qqpay_api'])echo '<button type="submit" class="btn btn-default" id="buy_qqpay"><img src="images/icon/qqpay.ico" class="logo">QQ钱包</button>&nbsp;';
if($conf['wxpay_api'])echo '<button type="submit" class="btn btn-default" id="buy_wxpay"><img src="images/icon/wechat.ico" class="logo">微信支付</button>&nbsp;';
if($conf['tenpay_api'])echo '<button type="submit" class="btn btn-default" id="buy_tenpay"><img src="images/icon/tenpay.ico" class="logo">财付通</button>&nbsp;';
echo '</div>';
}
?>
<script>
$(document).ready(function(){
$("#buy_alipay").click(function(){
	var shopid=$("select[name='shopid']").val();
	var qq=$("select[name='qq']").val();
	if(qq==undefined)qq=$("input[name='qq']").val();
	ajax.get("ajax.php?mod=shop&act=pay&type=alipay&shopid="+shopid+"&qq="+qq, "html", function(data) {
		$('#myDiv').html(data);
		$('#shop').modal('show');
	});
});
$("#buy_qqpay").click(function(){
	var shopid=$("select[name='shopid']").val();
	var qq=$("select[name='qq']").val();
	if(qq==undefined)qq=$("input[name='qq']").val();
	ajax.get("ajax.php?mod=shop&act=pay&type=qqpay&shopid="+shopid+"&qq="+qq, "html", function(data) {
		$('#myDiv').html(data);
		$('#shop').modal('show');
	});
});
$("#buy_wxpay").click(function(){
	var shopid=$("select[name='shopid']").val();
	var qq=$("select[name='qq']").val();
	if(qq==undefined)qq=$("input[name='qq']").val();
	ajax.get("ajax.php?mod=shop&act=pay&type=wxpay&shopid="+shopid+"&qq="+qq, "html", function(data) {
		$('#myDiv').html(data);
		$('#shop').modal('show');
	});
});
$("#buy_tenpay").click(function(){
	var shopid=$("select[name='shopid']").val();
	var qq=$("select[name='qq']").val();
	if(qq==undefined)qq=$("input[name='qq']").val();
	ajax.get("ajax.php?mod=shop&act=pay&type=tenpay&shopid="+shopid+"&qq="+qq, "html", function(data) {
		$('#myDiv').html(data);
		$('#shop').modal('show');
	});
});
$("select[name='shopid']").change(function(){
	if($(this).val() == 10){
		$("#display_coin").css("display","inherit");
	}else{
		$("#display_coin").css("display","none");
	}
});
});
</script>
</div></div>
<?php
}

if($conf['open_km']){
echo '<div class="panel panel-success">
<div class="panel-heading w h"><h3 class="panel-title">卡密激活</h3></div><div class="panel-body box">';
if($conf['vipmode']==1) {
	$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE uid='{$uid}' order by id desc");
	$contents='';
	while($myrow = $DB->fetch($rs)) $contents.='<option value="'.$myrow['qq'].'">'.$myrow['qq'].'</option>';
	if(empty($contents))$contents='没有可续费的QQ';
	echo '<form action="index.php?mod=shop&act='.$act.'" method="POST">
<div class="form-group">
<label>卡密激活：</label><br>
<input type="text" class="form-control" name="km" value="" autocomplete="off" placeholder="输入卡密"></div>
<div class="form-group">
<label>选择要续期的QQ：</label><br>
<select class="form-control" name="qq" '.(isset($_GET['qq'])?'default="'.$_GET['qq'].'"':null).'>
'.$contents.'
</select></div>
<input type="submit" class="btn btn-success btn-block" value="确认使用"></form><br/>
<font color=green>输入卡密，点击确定使用即可为对应QQ续期！</font>';
}else{
echo '<form action="index.php?mod=shop&act='.$act.'" method="POST">
<div class="form-group">
<label>卡密激活：</label><br>
<input type="text" class="form-control" name="km" value="" autocomplete="off" placeholder="输入卡密"></div>
<input type="submit" class="btn btn-success btn-block" value="确认使用"></form><br/>
<font color=green>所有卡密都是在这个页面激活！输入卡密，点击确定使用即可！</font>';
}
echo '</div></div>';
}
echo '<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default"));
}
</script>';
echo '</div>';
}
elseif($act==2)
{
echo '<div class="col-md-6 col-xs-12"">';
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">自助兑换</h3></div><div class="panel-body box">';
echo '<blockquote><p>
<font color="green">此页面可以使用 '.$conf['coin_name'].' 兑换VIP和配额。</font>
</p></blockquote>
</div></div>
';

if($conf['vipmode']!=1){
echo '<div class="panel panel-primary"><div class="panel-body box">';
echo '<li class="list-group-item"><b>你当前拥有：</b><font color="red">'.$row['coin'].'</font> '.$conf['coin_name'].'</li>';
if($isvip==1)$vipstatus='到期时间:<font color="green">'.$row['vipdate'].'</font>';
elseif($isvip==2)$vipstatus='<font color="green">永久 VIP</font>';
else $vipstatus='<font color="red">非 VIP</font>';
echo '<li class="list-group-item"><b>VIP状态：</b>'.$vipstatus.'</li>';
if($conf['peie_open'])
	echo '<li class="list-group-item"><b>你当前拥有：</b><font color="red">'.$row['peie'].'</font> 个QQ配额</li>';
echo '</div></div>';
}
echo '</div>';

echo '<div class="col-md-6 col-xs-12">';
if($conf['vipmode']==1){
if($conf['coin_tovip']){
	$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE uid='{$uid}' order by id desc");
	$contents='';
	while($myrow = $DB->fetch($rs)) $contents.='<option value="'.$myrow['qq'].'">'.$myrow['qq'].'</option>';
	if(empty($contents))$contents='没有可续费的QQ';
	echo '<div class="panel panel-primary">
	<div class="panel-body box">
	<form action="index.php?mod=shop&act='.$act.'" method="POST"><input type="hidden" name="to" value="vip">
	<div class="form-group">
	<label>VIP兑换：</label><br>
	兑换价格：<font color="red">'.$conf['coin_tovip'].'</font> '.$conf['coin_name'].'＝1个月VIP会员<br>
	<input type="text" class="form-control" name="value" value="" autocomplete="off" placeholder="要兑换的VIP月数"></div>
	<div class="form-group">
	<label>选择要续期的QQ：</label><br>
	<select class="form-control" name="qq" '.(isset($_GET['qq'])?'default="'.$_GET['qq'].'"':null).'>
	'.$contents.'
	</select></div>
	<input type="submit" class="btn btn-success btn-block" value="确认兑换"></form>
	</div>
	</div>';
echo '<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default"));
}
</script>';
}
}else{
if($conf['coin_tovip']){
	echo '<div class="panel panel-primary">
	<div class="panel-body box">
	<form action="index.php?mod=shop&act='.$act.'" method="POST"><input type="hidden" name="to" value="vip">
	<div class="form-group">
	<label>VIP兑换：</label><br>
	兑换价格：<font color="red">'.$conf['coin_tovip'].'</font> '.$conf['coin_name'].'＝1个月VIP会员<br>
	<input type="text" class="form-control" name="value" value="" autocomplete="off" placeholder="要兑换的VIP月数"></div>
	<input type="submit" class="btn btn-success btn-block" value="确认兑换"></form>
	</div>
	</div>';
}
if($conf['coin_tovip2']){
	echo '<div class="panel panel-primary">
	<div class="panel-body box">
	<form action="index.php?mod=shop&act='.$act.'" method="POST"><input type="hidden" name="to" value="vip2">
	<div class="form-group">
	<label>试用VIP兑换：</label><br>
	兑换价格：<font color="red">'.$conf['coin_tovip2'].'</font> '.$conf['coin_name'].'＝1天VIP会员<br>
	<input type="text" class="form-control" name="value" value="" autocomplete="off" placeholder="要兑换的VIP天数"></div>
	<input type="submit" class="btn btn-success btn-block" value="确认兑换"></form>
	</div>
	</div>';
}
if($conf['coin_topeie']){
	echo '<div class="panel panel-primary">
	<div class="panel-body box">
	<form action="index.php?mod=shop&act='.$act.'" method="POST"><input type="hidden" name="to" value="peie">
	<div class="form-group">
	<label>配额兑换：</label><br>
	兑换价格：<font color="red">'.$conf['coin_topeie'].'</font> '.$conf['coin_name'].'＝1个QQ配额<br>
	<input type="text" class="form-control" name="value" value="" autocomplete="off" placeholder="要兑换的配额个数"></div>
	<input type="submit" class="btn btn-success btn-block" value="确认兑换"></form>
	</div>
	</div>';
}
}
echo '</div>';
}
elseif($act==3)
{
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">代挂购买</h3></div><div class="panel-body box">';
echo $conf['qqlevel'].'
</div></div>
';
if($isadmin==1){
	$data=get_curl($dgapi.'api/submit.php?act=buy&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode);
	$arr=json_decode($data,true);
	echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">QQ代挂配额激活码购买方式</h3></div><div class="panel-body box">';
	echo '<p>{此处内容仅站长可见}<br/>'.($arr['msg']?$arr['msg']:$data).'</p>';
	echo '</div></div>
';
}
}
elseif($act==4)
{
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">网站赚币</h3></div><div class="panel-body box">';
echo $conf['shop'].'
</div></div>
<div class="panel panel-primary">
<div class="panel-body box">';

?>

<?php

}

}
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";

?>