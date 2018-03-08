<?php
 /*
　*空间刷人气
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="空间刷人气";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">空间刷人气</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">';


if($islogin==1){
if(OPEN_SHUA==0) {
	showmsg('当前站点未开启此功能。',2);
}
vipfunc_check('rq');
$qq=daddslashes($_GET['qq']);
if(!$qq) {
	showmsg('参数不能为空！');
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
}
if(!isset($_SESSION['rqcount']))$_SESSION['rqcount']=0;
if($_SESSION['rqcount']>100 && $isadmin==0) {
	showmsg('你的刷人气次数已超配额，请明天再来！');
}
$result=$DB->query("SELECT * from ".DBQZ."_qq WHERE status='1' order by rand() limit 50");
$arr=array();
while($row=$DB->fetch($result)){
	$arr[]=$row;
}

$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1");
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
			var url="<?php echo $siteurl ?>qq/api/rq.php";
			xiha.postData(url,'uin=<?php echo $qq ?>&cell=<?php echo $cell ?>&qid='+qid, function(d) {
				if(d.code ==0){
					checkself.removeClass('nostart');
					checkself.html("<font color='green'>已刷人气</font>");
					$('#load').html(d.msg);
					num = $('#liked').text();
					num=parseInt(num);
					num++;
					$('#liked').text(num);
				}else{
					checkself.html("<font color='red'>失败</font>");
				}
			});
			num++;
			//return false;
		});
		if(num<1) $('#load').html('没有待刷人气的QQ');
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
		<p style="color:red">利用平台内QQ号刷自己空间人气！<br>每次随机取出50个QQ，刷新本页面可以更换一批QQ。<br>刷人气前请先将自己的QQ空间权限设为所有人可访问！</p>
	</div>
</div>
	<div class="panel panel-primary">
		<div class="panel-heading w h">
			<h3 class="panel-title" align="center"><span class="btn btn-block" id="startcheck" style="color:white;">点此开始刷人气</span></h3>
		</div>
		<div class="panel-body box" align="left">
			<ul class="list-group" style="list-style:none;">
			
			<li class='list-group-item'>平台总共<span id="hyall"><?php echo $gls;?><span>个QQ,有<span id="liked"></span>个已成功刷人气！</li>
			<li class='list-group-item' style="color:red;text-align: center;font-weight: bold;" id="load">等待开启</li>
			<?php
			$liked=0;
				foreach($arr as $k=>$row){
					$uin=$row['qq'];
					if(isset($_SESSION["r_".$cell][$uin])){
						if($_SESSION["r_".$cell][$uin]==1){
							$liked=$liked+1;
							echo '<li class="list-group-item">'.$uin.'<span style="float:right;"><font color="green">已刷人气</font></span></li>';
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
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>