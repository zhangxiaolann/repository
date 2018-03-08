<?php
 /*
　*欢乐球吃球刷星贝
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="欢乐球吃球刷星贝";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">欢乐球吃球刷星贝</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">';


if($islogin==1){
if(OPEN_SHUA==0) {
	showmsg('当前站点未开启此功能。',2);
}
vipfunc_check('rq');
$qq=daddslashes($_GET['qq']);
$link=daddslashes($_GET['link']);
if(!$qq) {
	showmsg('参数不能为空！');
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
}
if(!isset($_SESSION['rqcount']))$_SESSION['rqcount']=0;

$result=$DB->query("SELECT * from ".DBQZ."_qq WHERE status='1' order by rand() limit 50");
$arr=array();
while($row=$DB->fetch($result)){
	$arr[]=$row;
}

$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1");

if(empty($link)){
?>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">欢乐球吃球刷星贝</h3>
	</div>
	<div class="panel-body box" align="left">
		<form action="index.php" method="GET">
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">分享链接</div>
		<input type="hidden" name="mod" value="qiuqiu">
		<input type="hidden" name="qq" value="<?php echo $qq ?>">
		<input type="text" class="form-control" name="link" value="" placeholder="请输入欢乐球吃球分享链接" required>
		</div></div>
		<div class="form-group">
		<input type="submit" class="btn btn-primary btn-block" value="领取星贝">
		</div>
		</form>
	</div>
</div>
<?php }else{
if(strpos($link,'/url.cn/')){
	$data = get_curl($link,0,0,0,1);
	preg_match("/Location: (.*?)\r\n/iU", $data, $match);
	$link = $match[1];
}
if(strpos($link,'sToken=') && preg_match("/sToken=([0-9a-zA-Z]+)&/", $link, $match)){
	if(strpos($link,'logtype=wx')){
		$logtype='wx';
	}else{
		$logtype='qq';
	}
	$openid = $match[1];
}else{
	showmsg('分享链接输入错误！',3);
}
?>
<script>
$(document).ready(function() {
	$('#startcheck').click(function(){
		$('#load').html('检测中');
		var self=$(this);
		if (self.attr("data-lock") === "true") return;
			else self.attr("data-lock", "true");
		var touin,num=0;
		$(".nostart").each(function(){
			var checkself=$(this),
				qid=checkself.attr('qid');
			checkself.html("<img src='images/load.gif' height=25>")
			var url="<?php echo $siteurl ?>qq/api/qiuqiu.php";
			xiha.postData(url,'uin=<?php echo $qq ?>&openid=<?php echo $openid ?>&logtype=<?php echo $logtype ?>&qid='+qid, function(d) {
				checkself.removeClass('nostart');
				if(d.code ==0){
					checkself.html("<font color='green'>已点链接</font>");
					$('#load').html(d.msg);
					num = $('#liked').text();
					num=parseInt(num);
					num++;
					$('#liked').text(num);
					return false;
				}else if(d.code ==-1){
					$('#load').html(d.msg);
					checkself.html("<font color='blue'>资格用尽</font>");
				}else if(d.code ==-3){
					$('#load').html(d.msg);
					checkself.html("<font color='red'>SID已失效</font>");
				}else{
					$('#load').html(d.msg);
					checkself.html("<font color='red'>失败</font>");
				}
			});
			num++;
			//return false;
		});
		if(num<1) $('#load').html('没有待点链接的QQ');
		self.attr("data-lock", "false");
	});
	
});
var xiha={
	postData: function(url, parameter, callback, dataType, ajaxType) {
		if(!dataType) dataType='json';
		$.ajax({
			type: "POST",
			url: url,
			async: true,
			dataType: dataType,
			json: "callback",
			data: parameter,
			success: function(data) {
				if (callback == null) {
					return;
				} 
				callback(data);
			},
			error: function(error) {
//				alert('创建连接失败');
			}
		});
	}
}
</script>

<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">使用说明</h3>
	</div>
	<div class="panel-body box" align="left">
		<p style="color:red">利用平台内QQ号点击你的欢乐球吃球分享链接！<br>每次随机取出30个QQ，刷新本页面可以更换一批QQ。</p>
	</div>
</div>
	<div class="panel panel-primary">
		<div class="panel-heading w h">
			<h3 class="panel-title" align="center"><span class="btn btn-block" id="startcheck" style="color:white;">点此开始点击分享链接</span></h3>
		</div>
		<div class="panel-body box" align="left">
			<ul class="list-group" style="list-style:none;">
			<li class='list-group-item'>你的Openid：<?php echo $openid ?></li>
			<li class='list-group-item'>平台总共<span id="hyall"><?php echo $gls;?><span>个QQ,有<span id="liked"></span>个已成功点击分享链接！</li>
			<li class='list-group-item' style="color:red;text-align: center;font-weight: bold;" id="load">等待开启</li>
			<?php
			$liked=0;
				foreach($arr as $k=>$row){
					$uin=$row['qq'];
					if(isset($_SESSION["q_".$openid][$uin])){
						if($_SESSION["q_".$openid][$uin]==1){
							$liked=$liked+1;
							echo '<li class="list-group-item">'.$uin.'<span style="float:right;"><font color="green">已点链接</font></span></li>';
						}else{
							echo '<li class="list-group-item">'.$uin.'<span style="float:right;" qid="'.$row['id'].'" class="nozan"><font color="red">失败</font></span></li>';
						}
					}else{
						echo '<li class="list-group-item">'.$uin.'<span style="float:right;" qid="'.$row['id'].'" class="nostart">未开启</span></li>';
					}
				}
			echo "<script>$('#liked').html('{$liked}');</script>";
			?>
			</ul>
		</div>
	</div>

<?php
			}
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>