<?php global $zym_decrypt;$zym_decrypt['�֮�����î����֯�þ��Į���֥����']=base64_decode('ZGVmaW5lZA==');$zym_decrypt['������������î��î���þ������å�']=base64_decode('c3RyX3JlcGxhY2U=');$zym_decrypt['���ċ�ľ��־��������Ô�î֔���Ď']=base64_decode('ZXhwbG9kZQ==');$zym_decrypt['���������ċ����֮�������������È']=base64_decode('ZGFkZHNsYXNoZXM=');$zym_decrypt['����֯È���Ë���å��֋��þ������']=base64_decode('c2hvd21zZw=='); ?>
<?php
if(!$GLOBALS['zym_decrypt']['�֮�����î����֯�þ��Į���֥����'](base64_decode('SU5fQ1JPTkxJVEU=')))exit();$title="批量导入QQ";$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist"><i class="icon fa fa-qq"></i>ＱＱ管理</a></li>
<li class="active"><a href="#"><i class="icon fa fa-list-alt"></i>批量导入QQ</a></li>';include TEMPLATE_ROOT.base64_decode('aGVhZC5waHA=');$my=isset($_POST['my'])?$_POST['my']:$_GET['my'];echo base64_decode('PGRpdiBjbGFzcz0iY29sLWxnLTggY29sLXNtLTEwIGNvbC14cy0xMiBjZW50ZXItYmxvY2siIHJvbGU9Im1haW4iPg==');if ($isadmin==1){if($_POST['type']=="edit"){$list =$_POST['list'];$list =$GLOBALS['zym_decrypt']['������������î��î���þ������å�'](array("\r\n", "\r", "\n"), "[br]", $list);$match=$GLOBALS['zym_decrypt']['���ċ�ľ��־��������Ô�î֔���Ď']("[br]",$list);$success=0;$error=0;foreach($match as $val){if($val=='')continue;$array=$GLOBALS['zym_decrypt']['���ċ�ľ��־��������Ô�î֔���Ď']('----',$val);$qq=$GLOBALS['zym_decrypt']['���������ċ����֮�������������È']($array[0]);$qpwd=$GLOBALS['zym_decrypt']['���������ċ����֮�������������È']($array[1]);if($qq==''||$qpwd=='')continue;$qpwd=authcode($qpwd,'ENCODE',SYS_KEY);$rowm1=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");if(!$rowm1['qq']){$sql ="INSERT INTO `".DBQZ."_qq`(`uid`,`qq`,`pw`,`status`,`status2`,`time`) VALUES ('{$uid}','{$qq}','{$qpwd}','0','0','{$date}')";$sds=$DB->query($sql);if($sds){$data='a:1:{s:6:"forbid";s:0:"";}';if($_POST['addzan']==1)$DB->query("insert into `".DBQZ."_qqjob` (`uid`,`qq`,`type`,`sign`,`method`,`data`,`lasttime`,`nexttime`,`pl`,`start`,`stop`,`sysid`) values ('{$uid}','{$qq}','zan','0','3','{$data}','".time()."','".time()."','0','0','24','1')");$success++;}else{$error++;}}else{$sql="update `".DBQZ."_qq` set `pw` ='$qpwd',`status` ='0',`status2` ='0',`time`='$date' where `qq`='$qq'";$sds=$DB->query($sql);}unset($sds);}exit("<script language=\"javascript\">alert('已成功导入{$success}个QQ，失败{$error}个');history.go(-1);</script>");}?>
<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">批量导入QQ</h3></div><div class="panel-body box">
<form action="index.php?mod=import" method="post">
<input type="hidden" name="type" value="edit" />
<div class="form-group">
<label>QQ列表:</label><br>
<textarea class="form-control" name="list" rows="8" placeholder="一行一个，格式：QQ----密码" required></textarea>
</div>
<div class="form-group">
<input type="checkbox" name="addzan" id="addzan" value="1">
<label for="addzan">同时添加秒赞任务</label>
</div>
<div class="form-group text-right">
<button type="submit" class="btn btn-primary btn-block" id="save">提交</button>
</div>
</form>
<h5>说明:</h5>
使用之前请开启自动打码，可以增加newsid.php的刷新频率来快速更新QQ状态
</div></div>
<?php
}else {$GLOBALS['zym_decrypt']['����֯È���Ë���å��֋��þ������'](base64_decode('5ZCO5Y+w566h55CG55m75b2V5aSx6LSl44CC6K+35Lul566h55CG5ZGY6Lqr5Lu9IDxhIGhyZWY9ImluZGV4LnBocD9tb2Q9bG9naW4iPumHjeaWsOeZu+W9lTwvYT7vvIE='),3);}include TEMPLATE_ROOT.base64_decode('Zm9vdC5waHA=');?>