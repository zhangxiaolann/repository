<?php
if(!defined('IN_CRONLITE'))exit();
$title="试用卡获取";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-check-square"></i>试用卡获取</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-lg-8 col-sm-10 col-xs-12 center-block" role="main">';

$result = $DB->query("SELECT * FROM ".DBQZ."_kms WHERE kind='3' and isuse='0' LIMIT 30");

if($conf['active']!=0)phone_check();
echo '<table class="table table-bordered">
<tbody align="center">
<tr>
<td>卡密</td>
<td>时长</td>
<td>是否使用</td>
<td>使用ID</td>
<td>使用时间</td>
<td>使用</td>
<!--<td>生成时间</td>-->
</tr>';
while($rows = $DB->fetch($result))
  {
if($rows['isuse']==1){
	$sfsy='<font color="#FF0000">已使用</font>';
}else{
	$sfsy='<font color="#0000C6">未使用</font>';
}
	  echo '<tr>
<td>'. $rows['km'] . '</td>
<td>'. $rows['value'] . '天</td>
<td>' . $sfsy . '</td>
<td>' . $rows['user'] . '</td>
<td>' . $rows['usetime'] . '</td>
<td><form action="index.php?mod=shop&kind=3" method="POST">
<input name="km" type="hidden" value="'. $rows['km'] . '">
<button type="submit" name="submit" class="btn btn-info">使用</button>
</form>
</td>
<!--<td>' . $rows['addtime'] . '</td>-->
</tr>';
	}

echo '</tbody></table>';
include TEMPLATE_ROOT."foot.php";
?>