<?php
 /*
　*秒赞检测与好友分组
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="秒赞检测";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">秒赞检测</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';

if($conf['dx_api']==1)$siteurl='http://mzbapi.odata.cc/';
if($islogin==1){
vipfunc_check('mzjc');
$qq=daddslashes($_GET['qq']);
if(!$qq) {
	showmsg('参数不能为空！');
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
}
if ($row['status']!=1) {
	showmsg('SKEY已过期！');
}
$sid=$row['sid'];
$skey=$row['skey'];
$pskey=$row['pskey'];

if($conf['mzjc_api']==0 || !$conf['mzjc_api']) {
$gtk = getGTK($pskey);
$cookie='pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$skey.'; p_uin=o0'.$qq.'; p_skey='.$pskey.';';
$url = 'http://mobile.qzone.qq.com/friend/mfriend_list?g_tk='.$gtk.'&res_uin='.$qq.'&res_type=normal&format=json&count_per_page=10&page_index=0&page_type=0&mayknowuin=&qqmailstat=';
//$url='http://rc.qzone.qq.com/p/r/cgi-bin/tfriend/friend_show_qqfriends.cgi?uin='.$qq.'&follow_flag=0&groupface_flag=0&fupdate=1&format=json&g_tk='.$gtk;
$json = get_curl($url,0,1,$cookie);
$json=mb_convert_encoding($json, "UTF-8", "UTF-8");
$arr = json_decode($json, true);
//print_r($arr);exit;
if (!$arr) {
	showmsg('好友列表获取失败！');
}elseif ($arr["code"] == -3000) {
	showmsg('SID已过期！');
}
$friend=$arr["data"]["list"];
$gpnames=$arr["data"]["gpnames"];

foreach($gpnames as $gprow){
	$gpid=$gprow['gpid'];
	$gpname[$gpid]=$gprow['gpname'];
}

$gtk = getGTK($skey);
$cookie='pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$skey.';';

$url='http://sh.taotao.qq.com/cgi-bin/emotion_cgi_feedlist_v6?hostUin='.$qq.'&ftype=0&sort=0&pos=0&num=5&replynum=0&code_version=1&format=json&need_private_comment=1&g_tk='.$gtk;
$data = get_curl($url,0,0,$cookie);
$arr=json_decode($data,true);
//print_r($arr);exit;
$qqrow=array();
$qquins=array();
if (@array_key_exists('code',$arr) && $arr['code']==0) {
	foreach ($arr['msglist'] as $k => $row ) {
		$url='http://users.qzone.qq.com/cgi-bin/likes/get_like_list_app?uin='.$qq.'&unikey='.urlencode($row['key1']).'&begin_uin=0&query_count=200&if_first_page=1&g_tk='.$gtk;
		$data2 = get_curl($url,0,'http://user.qzone.qq.com/',$cookie);
		if(!$data2){showmsg('SKEY已失效，请更新SKEY！');exit;}
		preg_match('/_Callback\((.*?)\)\;/is',$data2,$json);
		$arr2=json_decode($json[1],true);
		$data2=$arr2['data']['like_uin_info'];
		foreach ($data2 as $row2 ) {
			$fuin=$row2['fuin'];
			if(isset($qqrow[$fuin])){$qqrow[$fuin]++;}
			else {$qqrow[$fuin]=1;$qquins[]=$fuin;}
		}
	}
}else{
	showmsg('获取失败！');
}

$mzcount=count($qqrow);
$uins=base64_encode(json_encode($qquins));
foreach ($friend as $row3 ) {
	$fuin=$row3['uin'];
	if(isset($qqrow[$fuin]))$list['mz']=$qqrow[$fuin];
	else $list['mz']=0;
	$list['uin']=$row3['uin'];
	$list['name']=$row3['nick'];
	if($row3['remark'])$list['remark']=$row3['remark'];
	else $list['remark']=$row3['nick'];
	$list['groupid']=$row3['groupid'];
	$result['friend'][]=$list;
	unset($list);
}
rsort($result['friend']);
$friend=$result['friend'];
}else{
$data = get_curl($allapi.'api/mzjc.php?qq='.$qq.'&skey='.$skey.'&pskey='.$pskey.'&pskey2='.$row['pskey2'].'&authcode='.$authcode);
$arr=json_decode($data,true);
if(@array_key_exists('code',$arr) && $arr['code']==0) {
	$uins=base64_encode(json_encode($arr['uins']));
	$gpnames=$arr["gpnames"];
	$friend=$arr['friend'];
	$mzcount=$arr['mzcount'];
	$gpname=$arr["gpname"];
} elseif(@array_key_exists('code',$arr)) {
	showmsg($arr['msg']);
} else {
	showmsg('从官方API获取数据失败！');
}
}
$fcount=count($friend);
$array=array();
foreach($friend as $nrow){
	if($nrow['mz']) $array[$nrow['groupid']]['mzcount']=$array[$nrow['groupid']]['mzcount']+1;
	$array[$nrow['groupid']][]=$nrow;
}
$friend=$array;
?>
<script>
var qqnum = 0;
function SelectAll(chkAll,type) {
	var items = $('.uins');
	var mz;
	for (i = 0; i < items.length; i++) {
		mz=items[i].attributes["mz"].value;
		if((type==1 && mz>0) || type != 1){
			if (items[i].id.indexOf("uins") != -1) {
				if (items[i].type == "checkbox") {
					items[i].checked = chkAll.checked;
				}
			}
		}
	}
}
function SelectGpAll(gp,chkAll,type) {
	var items = $('.gpuins'+gp);
	var mz;
	for (i = 0; i < items.length; i++) {
		mz=items[i].attributes["mz"].value;
		if((type==1 && mz>0) || type != 1){
			if (items[i].id.indexOf("uins") != -1) {
				if (items[i].type == "checkbox") {
					items[i].checked = chkAll.checked;
				}
			}
		}
	}
}
function fenzuqq(){
	var url="<?php echo $siteurl ?>qq/api/fenzu.php";
	var fenzu=$("#gpname").val();
	$("input[name=uins]:checked:first").each(function(){
		var checkself=$(this);
		var touin=checkself.val();
		var statusself=$('.status[uin='+touin+']');
		statusself.html("<img src='images/load.gif' height=25>");
		xiha.postData(url,'uin=<?php echo $qq ?>&skey=<?php echo $skey ?>&pskey=<?php echo $pskey ?>&touin='+touin+'&gpid='+fenzu, function(d) {
			if(d.code==0){
				qqnum++;
				statusself.html('<font color="green">成功</font>');
				checkself.removeAttr('checked');
				$('.fenzu').html(touin+'移动完成');
				fenzuqq();
			}else if(d.code==-1){
				statusself.html('<font color="red">失败</font>');
				alert('SKEY已过期，请更新SKEY！');
			}else{
				statusself.html('<font color="red">失败</font>');
			}
		});
		return true;
	});
}

$(document).ready(function(){
	$('.fenzu').click(function(){
		var self=$(this);
		if (self.attr("data-lock") === "true") return;
			else self.attr("data-lock", "true");
		self.html('移动中<img src="images/load.gif" height=22>');
		qqnum = 0;
		fenzuqq();
		if(qqnum<1) self.html('没有待移动的QQ');
		else self.html('移动成功！');
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
				//alert('未检测到移动结果，请自己查看好友分组');
			}
		});
	}
}
</script>
<div class="panel panel-warning">
	<div class="panel-heading">
		<div class="panel-title">
			<div class="input-group" style="padding:8px 0;">
				<div class="input-group-addon">All<input type="checkbox" onclick="SelectAll(this)" />&nbsp;赞<input type="checkbox" onclick="SelectAll(this,1)" /></div>
				<div class="input-group-addon btn fenzu">移动勾选好友到</div>
				<select id="gpname" class="form-control">
				<?php
				foreach($gpnames as $row) {
				echo '<option value="'.$row['gpid'].'">'.$row['gpname'].'</option>';
				}
				?>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="panel-group" id="gpnames">
<div class="panel panel-info">
	<div class="panel-heading">
		<div class="panel-title">总共<font color="black"><?php echo $fcount?></font>个好友,其中<font color="red"><?php echo $mzcount?></font>个可能秒赞好友</div>
	</div>
</div>
<?php foreach($gpnames as $gprow){
$count=$friend[$gprow['gpid']]['mzcount']?$friend[$gprow['gpid']]['mzcount']:0;?>
<div class="panel panel-success">
	<div class="panel-heading">
		<span class="right" style="color:red">[<?php echo $count?>]</span>
		<div class="panel-title" data-toggle="collapse" data-parent="#gpnames" href="#gpnames<?php echo $gprow['gpid']?>">↓&nbsp;<?php echo $gprow['gpname']?>[<?php echo count($friend[$gprow['gpid']])?>]</div>
	</div>
	<div id="gpnames<?php echo $gprow['gpid']?>" class="panel-collapse collapse">
		<div class="panel-body">
			<table class="table table-bordered" style="table-layout: fixed;">
			<tbody>
			<tr><td style="width:150px;" align="center">All<input type="checkbox" onclick="SelectGpAll(<?php echo $gprow['gpid']?>,this)"/>&nbsp;赞<input type="checkbox" onclick="SelectGpAll(<?php echo $gprow['gpid']?>,this,1)"/>&nbsp;QQ</td><td class="mzwidthtd" align="center"">昵称</td><td class="mzwidthtd hidden-xs" align="center">备注</td><td align="center">结果</td></tr>
			<?php foreach($friend[$gprow['gpid']] as $row){ $bf=$row['mz']/5;$bfb=round(($row['mz']/5)*100); if(is_array($row)){ echo '<tr><td><label><input name="uins" type="checkbox" class="uins gpuins'.$row['groupid'].'" id="uins" value="'.$row['uin'].'" mz="'.$row['mz'].'">'.$row['uin'].'<label></td><td class="mztd hidden-xs" title="'.$row['name'].'">'.$row['name'].'</td><td class="mztd" title="'.$row['remark'].'">'.$row['remark'].'</td><td class="mztd status" uin="'.$row['uin'].'" align="center" style="background: rgba(205, 133, 0, '.$bf.');">'.$bfb.'%</td></tr>';}}?>
			</tbody>
			</table>
		</div>
	</div>
</div>
<?php }?>
</div>

<?php
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>