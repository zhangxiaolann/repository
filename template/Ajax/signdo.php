<?php
error_reporting(0);
$type=$_GET['type'];
$jobid=$_GET['jobid'];
$page=$_GET['page'];
$data=serialize($_POST);
$row=signjob_decode($type,$data);
$url=$row['url'];
?>
<div class="panel panel-primary">
	<div class="panel-heading bk-bg-primary">
		<h6><i class="fa fa-indent red"></i><span class="break"></span>签到结果</h6>
		<div class="panel-actions">
			<a href="#" onclick="showlist('signtask',1)" class="btn-close"><i class="fa fa-times black"></i></a>
		</div>
	</div>
	<div class="panel-body">
<div id="load"><img src="images/load.gif" height="25">正在加载...</div>
<iframe src="<?php echo $url;?>" frameborder="0" scrolling="auto" seamless="seamless" width="100%"  onload="$('#load').hide();" name="signresult"></iframe>
<p><button type="button" class="btn btn-primary btn-block" id="signjob_add">添加到任务列表</button>
<button type="button" class="btn btn-info btn-block" onclick="window.open(document.all.signresult.src,'signresult','')">点此重试</button>
<button type="button" class="btn btn-default btn-block" id="back" onclick="signjob_edit('<?php echo $type?>')">返回重填</button></p>
</div>
</div>
<script>
$(document).ready(function(){
$('#signjob_add').click(function()
{
	$("#signjob_add").val('loading');
	ajax.post("ajax.php?mod=signjob&act=add&type=<?php echo $type?>&jobid=<?php echo $jobid?>",
	{
		data:'<?php echo $data?>'
	},"json",function(arr,status){
		if(arr.code==1){
			alert(arr.msg);
			showlist('signtask',<?php echo $page?>);
		}else{
			alert(arr.msg);
		}
	});
});
});
</script>