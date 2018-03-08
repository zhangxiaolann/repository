<?php
if(!defined('IN_CRONLITE'))exit();
$title="用户注册";
include TEMPLATE_ROOT."head.php";

if($is_fenzhan==1) $logoname = DBQZ;else $logoname = ''; 
if(!file_exists(ROOT.'images/'.$logoname.'logo.png')) $logoname='';
?>
<div class="col-md-12" style="margin: 0 auto;max-width:580px;">
<div class="panel panel-primary">
	<div class="panel-body" style="text-align: center;">
		<img src="images/<?php echo $logoname?>logo.png">
	</div>
</div>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">免费注册</h3></div>

<?php
$displyver='<div class="form-group"><label>验证码: </label><img title="点击刷新" src="verifycode.php" onclick="this.src=\'verifycode.php?\'+Math.random();"><br>
<input type="text" class="form-control" name="verify" value="" autocomplete="off" required></div>';
?>
<div class="panel-body"><form action="index.php?mod=reg" method="post"><input type="hidden" name="my" value="reg">
<div class="form-group">
<label>用户名:</label><br><input type="text" class="form-control" name="user" value="" placeholder="中文、英文或数字" required required></div>
<div class="form-group">
<label>密码:</label><br><input type="password" class="form-control" name="pass" value="" required></div>
<div class="form-group">
<label>重复密码:</label><br><input type="password" class="form-control" name="pass2" value="" required></div>
<?php echo $displyver?>
<div class="form-group">
<label>ＱＱ:</label><br><input type="text" class="form-control" name="qq" value="" placeholder="用于显示头像及找回密码" required></div>
<?php if($conf['zc_mail']==1){?>
<div class="form-group">
<label>邮箱:</label><br><input type="email" class="form-control" name="email" value="" placeholder="用于找回密码及SID失效提醒" required></div>
<?php }?>
<input type="submit" class="btn btn-primary btn-block" value="确认注册"></form></div>
<?php
echo'<div class="panel-heading"><h3 class="panel-title">最新注册用户:</h3></div>';
echo "<div class='panel-body'>";
$rsz = $DB->query("select * from ".DBQZ."_user order by userid desc limit 0,5");
while ($rowz = $DB->fetch($rsz)) {
$len = strlen($rowz["user"])/2;
$len = ceil($len);
$str=substr_replace($rowz["user"],str_repeat('*',$len),$len);
echo $str . "<br>";
}
echo'</div></div>';

include TEMPLATE_ROOT."foot.php";
?>