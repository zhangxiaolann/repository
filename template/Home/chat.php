<?php 
/*
　*　聊天室文件
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="聊天室";

/****发言限制设定****/
$timelimit = 600; //时间周期(秒)
$iplimit = 3; //相同IP在1个时间周期内限制发言的次数
if($islogin==1)$verifyswich=0; //验证码开关
else $verifyswich=1;

function ubb_parse($content)
{
$n = htmlspecialchars($content, ENT_QUOTES); 
if(strpos($n,'[img]')!==false)
{
//$n = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src='\\1'>",$n); 
}
if(strpos($n,'[cy]')!==false)
{
$n = str_replace("[cy]","<img src='./images/face/ciya.gif'>",$n);
} 
if(strpos($n,'[fn]')!==false)
{
$n = str_replace("[fn]","<img src='./images/face/fennu.gif'>",$n);
}
if(strpos($n,'[gg]')!==false)
{
$n = str_replace("[gg]","<img src='./images/face/ganga.gif'>",$n);
} 
if(strpos($n,'[hx]')!==false)
{
$n = str_replace("[hx]","<img src='./images/face/huaixiao.gif'>",$n);
} 
if(strpos($n,'[ka]')!==false)
{
$n = str_replace("[ka]","<img src='./images/face/keai.gif'>",$n);
}
if(strpos($n,'[kl]')!==false)
{
$n = str_replace("[kl]","<img src='./images/face/kelian.gif'>",$n);
}
if(strpos($n,'[ll]')!==false)
{
$n = str_replace("[ll]","<img src='./images/face/liulei.gif'>",$n);
}
if(strpos($n,'[se]')!==false)
{
$n = str_replace("[se]","<img src='./images/face/se.gif'>",$n);
} 
if(strpos($n,'[wq]')!==false)
{
$n = str_replace("[wq]","<img src='./images/face/weiqu.gif'>",$n);
}
if(strpos($n,'[wx]')!==false)
{
$n = str_replace("[wx]","<img src='./images/face/weixiao.gif'>",$n);
}
if(strpos($n,'[xia]')!==false)
{
$n = str_replace("[xia]","<img src='./images/face/xia.gif'>",$n);
}
if(strpos($n,'[yun]')!==false)
{
$n = str_replace("[yun]","<img src='./images/face/yun.gif'>",$n);
}
if(strpos($n,'[br]')!==false)
{
$n = str_replace("[br]","<br />",$n);
}
if(strpos($n,'[hr]')!==false)
{
//$n = str_replace("[hr]","<hr />",$n);
}
if(strpos($n,'[color')!==false)
{
$n = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\"\\1\">\\2</font>",$n);
}
if(strpos($n,'[url]')!==false)
{
$n=preg_replace("/\[url=(http:\/\/.+?)\](.+?)\[\/url\]/is","<u><a href='\\1' target='_blank'>\\2</a></u>",$n);
$n=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<u><a href='\\1' target='_blank'>\\1</a></u>",$n); 
}
if(strpos($n,'[move]')!==false)
{
$n=preg_replace("/\[move\](.+?)\[\/move\]/is","<marquee width=\"98%\" scrollamount=\"3\">\\1</marquee>",$n);
}
return $n;
}

if($_POST['do']=='look'){
	@header('Content-Type: text/html; charset=UTF-8');
	$id=is_numeric($_POST['id'])?$_POST['id']:'1';
	if($id==1){
		exit('{"code":-1,"msg":"没有更多聊天内容了！"}');
	}
	$rows=$DB->query("select * from ".DBQZ."_chat where id<$id order by id desc limit 10");
	while($myrow=$DB->fetch($rows)){
		if($isadmin==1)$addstr='&nbsp;[<a href="#" onclick="deleteid(\''.$myrow['id'].'\')">删</a>]';
		$myrow['nr']=ubb_parse($myrow['nr']).$addstr;
		$list[]=$myrow;
	}
	$array['code']=0;
	$array['data']=$list;
	exit(json_encode($array));
}elseif($_POST['do']=='new'){
	@header('Content-Type: text/html; charset=UTF-8');
	$id=is_numeric($_POST['id'])?$_POST['id']:'0';
	$rows=$DB->query("select * from ".DBQZ."_chat where id>$id order by id desc limit 10");
	while($myrow=$DB->fetch($rows)){
		if($isadmin==1)$addstr='&nbsp;[<a href="#" onclick="deleteid(\''.$myrow['id'].'\')">删</a>]';
		$myrow['nr']=ubb_parse($myrow['nr']).$addstr;
		$list[]=$myrow;
	}
	$array['code']=0;
	$array['data']=$list;
	exit(json_encode($array));
}elseif($_POST['do']=='send'){
	@header('Content-Type: text/html; charset=UTF-8');
	$id=is_numeric($_POST['id'])?$_POST['id']:'0';
	$con=daddslashes(strip_tags($_POST['content']));
	if(!$con){
		exit('{"code":-2,"msg":"聊天内容不能为空！"}');
	}
	if($islogin==0){
		exit('{"code":-3,"msg":"登录后才能发言！"}');
	}
	if($isvip==0 && $isadmin==0 && in_array('chat',$vip_func)){
		exit('{"code":-3,"msg":"对不起，仅VIP能发送聊天信息！"}');
	}
	$timelimits=date("Y-m-d H:i:s",TIMESTAMP-$timelimit);
	$ipcount=$DB->count("SELECT count(*) FROM ".DBQZ."_chat WHERE `sj`>'$timelimits' and `ip`='$clientip'");
	if($ipcount>=$iplimit && $isadmin==0) {
		exit('{"code":-3,"msg":"你的发言速度太快了，请休息一下稍后重试。"}');
	}
	$sql="insert into `".DBQZ."_chat` (`user`,`nr`,`sj`,`to`,`ip`) values ('".$gl."','".$con."','".$date."','".$to."','".$clientip."')";
	$DB->query($sql);
	$rows=$DB->query("select * from ".DBQZ."_chat where id>$id order by id asc limit 1");
	while($myrow=$DB->fetch($rows)){
		if($isadmin==1)$addstr='&nbsp;[<a href="#" onclick="deleteid(\''.$myrow['id'].'\')">删</a>]';
		$myrow['nr']=ubb_parse($myrow['nr']).$addstr;
		$list[]=$myrow;
	}
	$array['code']=0;
	$array['data']=$list;
	exit(json_encode($array));
}elseif($_POST['do']=='delete'){
	@header('Content-Type: text/html; charset=UTF-8');
	if($isadmin==1){
		$id=is_numeric($_POST['id'])?$_POST['id']:'0';
		$sql=$DB->query("delete from ".DBQZ."_chat where id='$id' limit 1");
		if($sql){
			$array['code']=0;
			$array['msg']='删除成功！';
		}else{
			$array['code']=-1;
			$array['msg']='删除失败！'.$DB->error();
		}
	}else{
		$array['code']=-2;
		$array['msg']='你没有权限！';
	}
	exit(json_encode($array));
}

$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-comments"></i>聊天社区</a></li>';
include TEMPLATE_ROOT."head.php";
?>
<script type="text/javascript">
function Addstr(str) {
	$("#chatval").val($("#chatval").val()+str);
}
function deleteid(id) {
	var url="index.php?mod=chat";
	ajax.post(url,'do=delete&id='+id, 'json', function(d) {
		alert(d.msg);
	});
}
$(document).ready(function(){
	$('#clear').click(function(){
		$("#chatpage").html('<div class="chat-ts">系统提示：你已成功清除聊天内容！</div>');
	});
	$('#look').click(function(){
		self=$(this);
		var id=self.attr('startchat');
		if(id == 1){
			alert('没有更早的聊天内容了！');
			return;
		}
		if (self.attr("data-lock") === "true") return;
			else self.attr("data-lock", "true");
		self.html('加载中……');
		var url="index.php?mod=chat";
		ajax.post(url,'do=look&id='+id, 'json', function(d) {
			if(d.code ==0){
				if(d.data){
					$.each(d.data, function(i, chat){
						if(chat.user == '<?php echo $gl?>'){
							$("#chatpage").prepend('<div class="chat-user chat-me">['+chat.sj+'] <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'+chat.user+' \');return false">'+chat.user+'</a></span> '+chat.id+'#</div><div class="chat-div"><div class="chat-content right">'+chat.nr+'</div></div>'); 
						}else{
							$("#chatpage").prepend('<div class="chat-user">#'+chat.id+' <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'+chat.user+' \');return false">'+chat.user+'</a></span> ['+chat.sj+']</div><div class="chat-content">：'+chat.nr+'</div>'); 
						}
						self.attr('startchat',chat.id);
					}); 
				}
			}else{
				alert(d.msg);
			}
			self.attr("data-lock", "false");
			self.html('查看稍早内容^');
		});
	});
	$('#send').click(function(){
		self=$(this);
		var content=$("#chatval").val();
		if(content == ''){
			alert('请输入聊天内容！');
			return;
		}
		if (self.attr("data-lock") === "true") return;
			else self.attr("data-lock", "true");
		self.html('发送中……');
		var id=self.attr('lastchat');
		var url="index.php?mod=chat";
		ajax.post(url,'do=send&id='+id+'&content='+content, 'json', function(d) {
			if(d.code ==0){
				if(d.data){
					$.each(d.data, function(i, chat){
						if(chat.user == '<?php echo $gl?>'){
							$("#chatpage").append('<div class="chat-user chat-me">['+chat.sj+'] <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'+chat.user+' \');return false">'+chat.user+'</a></span> '+chat.id+'#</div><div class="chat-div"><div class="chat-content right">'+chat.nr+'</div></div>'); 
						}else{
							$("#chatpage").append('<div class="chat-user">#'+chat.id+' <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'+chat.user+' \');return false">'+chat.user+'</a></span> ['+chat.sj+']</div><div class="chat-content">：'+chat.nr+'</div>'); 
						}
						self.attr('lastchat',chat.id);
					}); 
					$('#body').scrollTop($('body')[0].scrollHeight);
					$("#chatval").val('');
				}
			}else{
				alert(d.msg);
			}
			self.attr("data-lock", "false");
			self.html('发送');
		});
	});
});
function Loadmsg(){
	var self=$('#send');
	var id=self.attr('lastchat');
	var url="index.php?mod=chat";
	ajax.post(url,'do=new&id='+id, 'json', function(d) {
		if(d.code ==0){
			if(d.data){
				$.each(d.data, function(i, chat){
					if(chat.user == '<?php echo $gl?>'){
						$("#chatpage").append('<div class="chat-user chat-me">['+chat.sj+'] <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'+chat.user+' \');return false">'+chat.user+'</a></span> '+chat.id+'#</div><div class="chat-div"><div class="chat-content right">'+chat.nr+'</div></div>'); 
					}else{
						$("#chatpage").append('<div class="chat-user">#'+chat.id+' <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'+chat.user+' \');return false">'+chat.user+'</a></span> ['+chat.sj+']</div><div class="chat-content">：'+chat.nr+'</div>'); 
					}
					self.attr('lastchat',chat.id);
				}); 
			}
		}else{
			alert(d.msg);
		}
	});
}
window.setInterval(Loadmsg, 30000);
</script>
<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">UBB使用说明</h4>
      </div>
      <div class="modal-body">
<br>换行:[br]
<br>呲牙:[cy]  愤怒:[fn]  尴尬:[gg]  坏笑:[hx]
<br>可爱:[ka]  可怜:[kl]  流泪:[ll]  色:[se]
<br>委屈:[wq] 微笑:[wx]  吓:[xia]  晕:[yun]
<br>链接:[url=http://链接地址]名称[/url]
<br>移动文字:[move]内容[/move]
<br>彩色文字:[color=颜色名]文字[/color]
<br><hr>颜色代码如:<br><font color=green>green</font>,<font color=red>red</font>,<font color=brown> brown</font>,<font color=#CCC00> #CCC00</font>,<font color=#66CCCC>#66CCCC</font> <a href="http://tool.c7sky.com/webcolor" target="_blank" rel="nofollow">更多</a>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php
echo '<div class="col-lg-8 col-sm-10 col-xs-12 center-block" role="main">';

if(OPEN_CHAT==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}

$rows=$DB->query("select * from ".DBQZ."_chat order by id desc limit 10");
$list=array();
while($myrow=$DB->fetch($rows)){
	$list[]=$myrow;
}
if(!$lastchat=$list[0]['id']) $lastchat=0;
$list=array_reverse($list);
if(!$startchat=$list[0]['id']) $startchat=1;

?>
<div class="panel panel-success">
	<div class="panel-heading"><i class="fa fa-comments"></i>聊天社区<span class="right" id="look" startchat="<?php echo $startchat?>">查看稍早内容^</span></div>
	<div class="panel-body" id="chatpage">
	<?php
		foreach($list as $row){
			if($isadmin==1)$addstr='&nbsp;[<a href="#" onclick="deleteid(\''.$row['id'].'\')">删</a>]';
			if($row['user']==$gl){
				echo '<div class="chat-user chat-me">['.$row['sj'].'] <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'.$row['user'].' \');return false">'.$row['user'].'</a></span> '.$row['id'].'#</div><div class="chat-div"><div class="chat-content right">'.ubb_parse($row['nr']).$addstr.'</div></div>';
			}else{
				echo '<div class="chat-user">#'.$row['id'].' <span class="chatuser"><a class="chatuser" href="#" onclick="Addstr(\'@'.$row['user'].' \');return false">'.$row['user'].'</a></span> ['.$row['sj'].']</div><div class="chat-content">：'.ubb_parse($row['nr']).$addstr.'</div>';
			}
		}
	?>
		<div class="chat-ts">系统提示：请文明聊天，勿刷屏，违者后果自负！</div>
	</div>
</div>
<div class="panel panel-info">
	<div class="input-group">
		<div class="input-group-addon" id="clear">清屏</div>
		<input class="form-control" id="chatval" placeholder="请输入文明聊天内容" onkeydown="if(event.keyCode==13){$('#send').click()}">
		<div class="input-group-addon" lastchat="<?php echo $lastchat?>" id="send">发送</div>
	</div>
</div>
<div class="panel panel-info">
<a data-toggle="modal" data-target="#myModal" class="btn btn-info btn-block">查看UBB使用说明</a>
</div>
<?php
include TEMPLATE_ROOT."foot.php";
?>