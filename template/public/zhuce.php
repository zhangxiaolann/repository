<?php
//注册文件
if(!defined('IN_CRONLITE'))exit();
if($verifyswich==1)
$displyver='<div class="form-group"><label>验证码: </label><img title="点击刷新" src="verifycode.php" onclick="this.src=\'verifycode.php?\'+Math.random();"><br>
<input type="text" class="form-control" name="verify" value="" autocomplete="off"></div>';
else $displyver='';
echo'<div class="panel-body box"><form action="index.php?mod=reg" method="post"><input type="hidden" name="my" value="reg">
<div class="form-group">
<label>用户名:</label><br><input type="text" class="form-control" name="user" value=""></div>
<div class="form-group">
<label>密码:</label><br><input type="password" class="form-control" name="pass" value=""></div>
<div class="form-group">
<label>确认密码:</label><br><input type="password" class="form-control" name="pass2" value=""></div>
'.$displyver.'
<input type="submit" class="btn btn-primary btn-block" value="确认注册"></form></div>';
echo'<div class="panel-heading w h"><h3 class="panel-title">最新注册用户:</h3></div>';
echo "<div class='panel-body box'>";
$rsz = $DB->query("select * from wjob_user order by userid desc limit 0,5");
while ($rowz = $DB->fetch($rsz)) {
$len = strlen($rowz["user"])/2;
$len = ceil($len);
$str=substr_replace($rowz["user"],str_repeat('*',$len),$len);
echo $str . "<br>";
}
echo'</div></div>';
?>