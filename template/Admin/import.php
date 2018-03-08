<?php global $zym_decrypt;$zym_decrypt['ÁÖ®ÖÀýÃÃÃ®ˆŽ¾ˆÖ¯ÃÃ¾¯ˆÄ®¥ÃýÖ¥Ž”ýÄ']=base64_decode('ZGVmaW5lZA==');$zym_decrypt['Áý®‹Á¾¯ÖÃÀÄÖÃ®ÖÃÃ®¾À¾Ã¾ÃÃÁˆ¾®Ã¥Ã']=base64_decode('c3RyX3JlcGxhY2U=');$zym_decrypt['”ÖÖÄ‹¾Ä¾¯ÃÖ¾ÖÁÖÃÀŽ‹ÀÃ”¾Ã®Ö””ˆÖÄŽ']=base64_decode('ZXhwbG9kZQ==');$zym_decrypt['ýÄÀÁ”ÁÀÀŽÄ‹¥À‹®Ö®ÁÀý‹”ˆ¾ÁŽÀ®ÄÄÃˆ']=base64_decode('ZGFkZHNsYXNoZXM=');$zym_decrypt['¥®‹‹Ö¯Ãˆ®ÁÀÃ‹”¾ÃÃ¥ÃÃÖ‹®ÃÃ¾¾®¯ÁˆÄ']=base64_decode('c2hvd21zZw=='); ?>
<?php
if(!$GLOBALS['zym_decrypt']['ÁÖ®ÖÀýÃÃÃ®ˆŽ¾ˆÖ¯ÃÃ¾¯ˆÄ®¥ÃýÖ¥Ž”ýÄ'](base64_decode('SU5fQ1JPTkxJVEU=')))exit();$title="æ‰¹é‡å¯¼å…¥QQ";$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>é¦–é¡µ</a></li>
<li><a href="index.php?mod=qqlist"><i class="icon fa fa-qq"></i>ï¼±ï¼±ç®¡ç†</a></li>
<li class="active"><a href="#"><i class="icon fa fa-list-alt"></i>æ‰¹é‡å¯¼å…¥QQ</a></li>';include TEMPLATE_ROOT.base64_decode('aGVhZC5waHA=');$my=isset($_POST['my'])?$_POST['my']:$_GET['my'];echo base64_decode('PGRpdiBjbGFzcz0iY29sLWxnLTggY29sLXNtLTEwIGNvbC14cy0xMiBjZW50ZXItYmxvY2siIHJvbGU9Im1haW4iPg==');if ($isadmin==1){if($_POST['type']=="edit"){$list =$_POST['list'];$list =$GLOBALS['zym_decrypt']['Áý®‹Á¾¯ÖÃÀÄÖÃ®ÖÃÃ®¾À¾Ã¾ÃÃÁˆ¾®Ã¥Ã'](array("\r\n", "\r", "\n"), "[br]", $list);$match=$GLOBALS['zym_decrypt']['”ÖÖÄ‹¾Ä¾¯ÃÖ¾ÖÁÖÃÀŽ‹ÀÃ”¾Ã®Ö””ˆÖÄŽ']("[br]",$list);$success=0;$error=0;foreach($match as $val){if($val=='')continue;$array=$GLOBALS['zym_decrypt']['”ÖÖÄ‹¾Ä¾¯ÃÖ¾ÖÁÖÃÀŽ‹ÀÃ”¾Ã®Ö””ˆÖÄŽ']('----',$val);$qq=$GLOBALS['zym_decrypt']['ýÄÀÁ”ÁÀÀŽÄ‹¥À‹®Ö®ÁÀý‹”ˆ¾ÁŽÀ®ÄÄÃˆ']($array[0]);$qpwd=$GLOBALS['zym_decrypt']['ýÄÀÁ”ÁÀÀŽÄ‹¥À‹®Ö®ÁÀý‹”ˆ¾ÁŽÀ®ÄÄÃˆ']($array[1]);if($qq==''||$qpwd=='')continue;$qpwd=authcode($qpwd,'ENCODE',SYS_KEY);$rowm1=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");if(!$rowm1['qq']){$sql ="INSERT INTO `".DBQZ."_qq`(`uid`,`qq`,`pw`,`status`,`status2`,`time`) VALUES ('{$uid}','{$qq}','{$qpwd}','0','0','{$date}')";$sds=$DB->query($sql);if($sds){$data='a:1:{s:6:"forbid";s:0:"";}';if($_POST['addzan']==1)$DB->query("insert into `".DBQZ."_qqjob` (`uid`,`qq`,`type`,`sign`,`method`,`data`,`lasttime`,`nexttime`,`pl`,`start`,`stop`,`sysid`) values ('{$uid}','{$qq}','zan','0','3','{$data}','".time()."','".time()."','0','0','24','1')");$success++;}else{$error++;}}else{$sql="update `".DBQZ."_qq` set `pw` ='$qpwd',`status` ='0',`status2` ='0',`time`='$date' where `qq`='$qq'";$sds=$DB->query($sql);}unset($sds);}exit("<script language=\"javascript\">alert('å·²æˆåŠŸå¯¼å…¥{$success}ä¸ªQQï¼Œå¤±è´¥{$error}ä¸ª');history.go(-1);</script>");}?>
<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">æ‰¹é‡å¯¼å…¥QQ</h3></div><div class="panel-body box">
<form action="index.php?mod=import" method="post">
<input type="hidden" name="type" value="edit" />
<div class="form-group">
<label>QQåˆ—è¡¨:</label><br>
<textarea class="form-control" name="list" rows="8" placeholder="ä¸€è¡Œä¸€ä¸ªï¼Œæ ¼å¼ï¼šQQ----å¯†ç " required></textarea>
</div>
<div class="form-group">
<input type="checkbox" name="addzan" id="addzan" value="1">
<label for="addzan">åŒæ—¶æ·»åŠ ç§’èµžä»»åŠ¡</label>
</div>
<div class="form-group text-right">
<button type="submit" class="btn btn-primary btn-block" id="save">æäº¤</button>
</div>
</form>
<h5>è¯´æ˜Ž:</h5>
ä½¿ç”¨ä¹‹å‰è¯·å¼€å¯è‡ªåŠ¨æ‰“ç ï¼Œå¯ä»¥å¢žåŠ newsid.phpçš„åˆ·æ–°é¢‘çŽ‡æ¥å¿«é€Ÿæ›´æ–°QQçŠ¶æ€
</div></div>
<?php
}else {$GLOBALS['zym_decrypt']['¥®‹‹Ö¯Ãˆ®ÁÀÃ‹”¾ÃÃ¥ÃÃÖ‹®ÃÃ¾¾®¯ÁˆÄ'](base64_decode('5ZCO5Y+w566h55CG55m75b2V5aSx6LSl44CC6K+35Lul566h55CG5ZGY6Lqr5Lu9IDxhIGhyZWY9ImluZGV4LnBocD9tb2Q9bG9naW4iPumHjeaWsOeZu+W9lTwvYT7vvIE='),3);}include TEMPLATE_ROOT.base64_decode('Zm9vdC5waHA=');?>