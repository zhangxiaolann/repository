<?php
if(!defined('IN_CRONLITE'))exit();
$cell=$_GET['shuoid'];
$content=$_GET['content'];
$qq=$_GET['qq'];

$result=$DB->query("SELECT * from ".DBQZ."_qq WHERE status='1' order by rand() limit 10");
$arr=array();
while($row=$DB->fetch($result)){
	$arr[]=$row;
}
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE status='1'");
?>
<ul class="list-group" style="list-style:none;">

<li class='list-group-item alert-info'>平台总共<span id="hyall"><?php echo $gls;?><span>个QQ,有<span id="liked"></span>个已成功刷队形！</li>
<li class='list-group-item' style="color:red;text-align: center;font-weight: bold;" id="load">等待开启</li>
<?php
$liked=0;
foreach($arr as $k=>$row){
	$uin=$row['qq'];
	if(isset($_SESSION["r_".$cell][$uin])){
		if($_SESSION["r_".$cell][$uin]==1){
			$liked=$liked+1;
			echo '<li class="list-group-item">'.$uin.'<span style="float:right;"><font color="green">已评论</font></span></li>';
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
<script>
$(document).ready(function() {
	$('#load').html('检测中');
	var shuoid='<?php echo $cell?>';
	var touin,num=0;
	$(".nostart").each(function(){
		var checkself=$(this),
			qid=checkself.attr('qid');
		checkself.html("<img src='images/load.gif' height=25>")
		var url="<?php echo $siteurl ?>qq/api/reply.php";
		xiha.postData(url,'uin=<?php echo $qq ?>&content=<?php echo $content ?>&cell='+shuoid+'&qid='+qid, function(d) {
			if(d.code ==0){
				checkself.removeClass('nostart');
				checkself.html("<font color='green'>已评论</font>");
				$('#load').html(d.msg);
				num = $('#liked').text();
				num=parseInt(num);
				num++;
				$('#liked').text(num);
			}else if(d.code ==-2){
				checkself.html("<font color='yellow'>频繁</font>");
				$('#load').html(d.msg);
			}else if(d.code ==-3){
				checkself.removeClass('nostart');
				checkself.html("<font color='red'>SID过期</font>");
				$('#load').html(d.msg);
			}else{
				checkself.html("<font color='red'>失败</font>");
				alert(d.msg);
				return false;
			}
		});
		num++;
		//return false;
	});
	if(num<1) $('#load').html('没有待可评论的QQ');
});
</script>