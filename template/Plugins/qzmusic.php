<?php
/*
 *空间背景音乐查询
*/
if(!defined('IN_CRONLITE'))exit();
$title="空间背景音乐查询";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">空间背景音乐</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';

if($islogin==1){

?>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">使用说明</h3>
	</div>
	<div class="panel-body box" align="left">
		<p style="color:red">使用此功能可以获取任意QQ空间的背景音乐，同时可以将下载地址做为音乐外链使用。</p>
	</div>
</div>
<div class="panel panel-primary">
	<div class="panel-heading w h" style="background: #56892E;">
		<h3 class="panel-title" align="center">空间背景音乐查询</h3>
	</div>
	<ul align="center" class="list-group box" style="list-style:none;">
		<li class="list-group-item">
    <form action="index.php" method="get"><input type="hidden" name="mod" value="qzmusic"><input type="hidden" name="qq" value="<?php echo $_GET['qq']?>">
    请输入要查询的QQ:<input type="text" class="form-control" name="cxqq" size="20"><br><input type="submit" class="btn btn-primary btn-block" value="查询">
    </form>
<br>
	</ul>
</div>
<div class="panel panel-primary box">
<?php
if(isset($_GET['qq'])){
	if($_GET['qq'] == ''){
?>
<table class="table table-bordered table-striped">
<thead>
    <tr>
      <td colspan="2">查询结果：</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="2"><div class="alerte alert-error">请输入您要查询的QQ</div></td>
    </tr>
  </tbody>
</table>
<?php
	}elseif(!is_numeric($_GET['qq'])){
?>
<table class="table table-bordered table-striped">
<thead>
    <tr>
      <td colspan="2">查询结果：</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="2"><div class="alerte alert-error">QQ必须为数字</div></td>
    </tr>
  </tbody>
</table>
<?php
	}else{
		$qq=daddslashes($_GET['qq']);
		$cxqq=isset($_GET['cxqq'])?daddslashes($_GET['cxqq']):$qq;
		$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
		if(!$row['qq']) {
			showmsg('QQ不存在于本站！');
		}
		$gtk = getGTK($row['skey']);
		$cookie='pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$row['skey'].';';
		$url = 'http://c.y.qq.com/qzone/fcg-bin/cgi_playlist_xml_new.fcg?json=1&outCharset=utf-8&utf8=1&uin='.$cxqq.'&g_tk='.$gtk;
		$data = get_curl($url,0,'http://user.qzone.qq.com/',$cookie);
		if(preg_match_all('/{"id":(\d+),"type":(\d+),.*?"mid":"(.*?)","name":"(.*?)".*?"url":"(.*?)".*?"singer":\[(.*?)\]/i',$data,$match)){
		//print_r($match);exit;
			$musiclist = $match[0];
		}elseif(preg_match('!"musicnum":0,!',$data)){
			showmsg('该QQ未设置背景音乐',2);
		}else{
			showmsg('获取音乐列表失败');
		}
	?>
<table class="table table-bordered table-striped">
<thead>
    <tr>
      <td colspan="2"><?php echo $cxqq;?> 的查询结果：</td>
    </tr>
       </thead>
<?php
	if(count($musiclist) == 0){
?>
  <tbody>
    <tr>
      <td colspan="2"><div class="alerte alert-error">该QQ未设置背景音乐</div></td>
    </tr>
 </tbody>
<?php
	}else{
?>
  <tbody>
    <tr>
      <td>歌曲名称：</td>
      <td>下载地址：</td>
    </tr>
  </tbody>
<?php
	$Token=get_curl("http://base.music.qq.com/fcgi-bin/fcg_musicexpress.fcg?json=3&guid=".$cxqq."&g_tk=".$gtk,0,'http://user.qzone.qq.com/',$cookie);
	preg_match('!jsonCallback\((.*?)\)!',$Token,$Token);
	$Token=json_decode($Token[1], true);
	$Token=$Token["key"];
	for($i=0;$i<count($musiclist);$i++){
	if($match[2][$i]==1)
		$songurl=$match[5][$i];
	else
		$songurl='http://dl.stream.qqmusic.qq.com/C200'.$match[3][$i].'.m4a?fromtag=6&vkey='.$Token.'&guid='.$cxqq;
	$songname = $match[4][$i];
	$encode = mb_detect_encoding($songname, array('UTF-8','GBK'));
	if($encode != 'UTF-8'){
		$songname = mb_convert_encoding($songname, 'UTF-8', $encode);
	}
	if(preg_match('!"name":"(.*?)"!',$match[6][$i],$singername)){
		$singername = $singername[1];
		$encode = mb_detect_encoding($singername, array('UTF-8','GBK'));
		if($encode != 'UTF-8'){
			$singername = mb_convert_encoding($singername, 'UTF-8', $encode);
		}
	}
?>
  <thead>
    <tr>
      <td><?php echo $songname .' - '. $singername?></td>
      <td><div class="btn-group"><a href="<?php echo $songurl?>" target="_blank" rel="noreferrer">下载地址</a></div></td>
    </tr>
  </thead>
<?php
			}
		}
?>
</table>
<?php
	}
}
?>
</div>

<?php
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>