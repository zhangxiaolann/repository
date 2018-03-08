<?php
/*
 *签到领VIP
 *Original:零欧喵喵
*/
if(!defined('IN_CRONLITE'))exit();
$title='签到领VIP';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-check-square"></i>签到领VIP</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-lg-8 col-sm-10 col-xs-12 center-block" role="main">';

$jfjf = $conf['qd_jifen'] ;//这个是几虚拟币换一天vip

if($islogin==1){
if($conf['active']!=0)phone_check();
	$user = $row['user'];
	$uid = $row['userid'];
	//$sid = $row['sid'];
	$vipjf = $row['coin'];
	$rmb = $row['coin'];
	$vipsigntime = $row['vipsigntime'];
	$vipend = $row['vipdate'];
	$vipsj = floor($vipjf/$jfjf);
	if($isvip==1 || $isvip==2){
		$vip = '<font color="#FF0000">是</font>';
	}else{
		$vip = '<font color="#0000C6">否</font>';
	}
	if($row['daili']==1){
		$daili = '<font color="#FF0000">是</font>';
	}else{
		$daili = '<font color="#0000C6">否</font>';
	}
	$time = time();
	$viptime = (strtotime($row['vipdate'])-strtotime(date("y-m-d",$time)))/3600/24;
	if($viptime < 0){
		$viptime = 0;
	}
	if($isvip==2)$viptime='永久';
	$datatime = date("y-m-d",$time);
	if(strtotime($datatime)==strtotime($vipsigntime)){
		$qdzt = '已签到';
	}else{
		$qdzt = '未签到';
	}
	if(@$_GET['qd']==1){
		$vipjf = $vipjf+$conf['qd_coin'];
		$time = time();
		$datatime = date("y-m-d",$time);
		$verifycode=daddslashes(strip_tags($_POST['verify']));

		if(strtotime($datatime)==strtotime($vipsigntime)){
			echo"<script> alert('今天你已经签到了');history.go(-1); </script>";
		}else{
			if(!$verifycode || strtolower($verifycode)!=$_SESSION['verifycode']){
				exit("<script language='javascript'>alert('验证码不正确！');history.go(-1);</script>");
			}
			if(!empty($conf['qd_ss'])){
				include_once(ROOT."qq/qzone.class.php");
				$qq=daddslashes($_POST['qq']);
				$qqs = $DB->get_row("select * from ".DBQZ."_qq where qq='{$qq}' and uid='{$uid}' limit 1");
				if(!$qqs['qq'])
					exit("<script> alert('签到失败，此QQ不存在');history.go(-1); </script>");
				$qdqq = new qzone($qqs['qq'],$qqs['sid'],$qqs['skey'],$qqs['pskey']);
				$qdqq->shuo(0,$conf['qd_ss'],$conf['qd_pturl']);
				/*foreach($qdqq->msg as $result){
				var_dump($result.'<br/>');
				}*/
			}
			$DB->query("update ".DBQZ."_user set coin='".$vipjf."',vipsigntime='".$datatime."' where userid='".$uid."'");
			$mark = $DB->affected();
			if($mark>0){
				if(!$vipsigntime && $conf['qd_vipts']>0 && $isvip==0){
					$vipdate = date("Y-m-d", strtotime("+ {$conf['qd_vipts']} days"));
					$DB->query("update ".DBQZ."_user set vip='1',vipdate='".$vipdate."' where userid='".$uid."'");
					if($conf['vipmode']==1)$DB->query("update ".DBQZ."_qq set vip='1',vipdate='".$vipdate."' where qq='".$qq."'");
					$addstr='，同时获得了'.$conf['qd_vipts'].'天体验VIP';
				}
				echo"<script> alert('签到成功，你获得了 {$conf['qd_coin']}{$conf['coin_name']}{$addstr}！');window.location = 'index.php?mod=qd'; </script>";
			}else{
				echo"<script> alert('签到失败');history.go(-1); </script>";
			}
		}
	}
	if(isset($_POST['data'])){
		if($jfjf==0)showmsg("本站未开启签到兑换VIP功能，请管理员到后台【VIP规则设定】中开启相关配置！",2);
		$xg_vipjf = $vipjf - @$_POST['data'] * $jfjf;
		if($_POST['data'] < 0){
			echo"<script> alert('兑换天数不能为负数！'); </script>";
		}elseif($row['vip']==2){
			echo"<script> alert('你已经是永久VIP，无法兑换！'); </script>";
		}else{
			if(floor($_POST['data']) < 1){
				echo"<script> alert('兑换天数不能为小数！'); </script>";
				}else{
					if($xg_vipjf < 0){
						echo"<script> alert('剩余{$conf['coin_name']}不够！'); </script>";
					}else{
						if(empty($_POST['data'])){
							echo"<script> alert('兑换天数不能为空！'); </script>";
						}else{
							$xg_vipend = $row['vipdate'];
							$time = date("y-m-d",time());
							if(strtotime($row['vipdate']) < time()){
								$xg_vipend = date("y-m-d",time());
							}
							$xg_vipend = strtotime($xg_vipend) + 3600 * 24 * floor(@$_POST['data']);
							$xg_vipend = date('y-m-d',$xg_vipend);
							$viplq_sql="update ".DBQZ."_user set coin='".$xg_vipjf."',vipdate='".$xg_vipend."',vip='1' where userid='".$uid."'";
							$DB->query($viplq_sql);
							$mark = $DB->affected();
							if($mark>0){
								echo"<script> alert('成功兑换{$_POST['data']}天VIP，请刷新查看最新信息！');window.location = 'index.php?mod=qd'; </script>";
							}else{
								echo"<script> alert('兑换失败！');history.go(-1); </script>";
							}
						}
					}
			}
		}
	}

echo '<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">
			签到公告
		</h3>
	</div>
<div class="list-group-item reed"><span>签到可以获得'.$conf['coin_name'].'，同时能获取体验VIP资格！</span></div>';
if($conf['qd_vipts'])echo '<div class="list-group-item reed"><span>签到即送VIP，每人仅限获取一次VIP，重复签到没有VIP奖励！</span></div>';
if(!empty($conf['qd_ss']))echo '<div class="list-group-item reed"><span>签到会利用添加的QQ发送一条带广告说说，不同意请勿签到！</span></div>';
echo '</div>';
	$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE uid='{$uid}' and status2=1 order by id desc");
	$contents='';
	while($myrow = $DB->fetch($rs)) $contents.='<option value="'.$myrow['qq'].'">'.$myrow['qq'].'</option>';
echo '<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">
			当前的登陆信息
		</h3>
	</div>
	<table class="table table-bordered">
		<tbody align="center">
			<tr>
				<td>
					UID
				</td>
				<td>
					用户名
				</td>
				<td>
					是否VIP
				</td>
				<td>
					VIP剩余天数
				</td>
				<td>
					是否代理
				</td>
				<td>
					'.$conf['coin_name'].'
				</td>
			</tr>
			<tr>
				<td>
					'. @$uid . '
				</td>
				<td>
					'. @$user . '
				</td>
				<td>
					'. @$vip . '
				</td>
				<td>
					' . @$viptime . '
				</td>
				<td>
					' . @$daili . '
				</td>
				<td>
					' . @$rmb . '
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">
			签到信息（每日获得 '.$conf['qd_coin'].''.$conf['coin_name'].'）
		</h3>
	</div>
	<form method="post" action="index.php?mod=qd&qd=1">
	<table class="table table-bordered">
		<tbody align="center">
			<tr>
				<td colspan="1">
					签到状态
				</td>
				<td colspan="1">
					<img title="点击刷新" src="verifycode.php" onclick="this.src=\'verifycode.php?\'+Math.random();" style="max-height:38px;vertical-align:middle;" class="img-rounded">
				</td>
				<td colspan="2">
					选择ＱＱ
				</td>
				<td colspan="2">
					点击签到
				</td>
			</tr>
			<tr>
				<td colspan="1">
					' . @$qdzt . '
				</td>
				<td colspan="1" style="max-width:100px;">
					<input type="text" name="verify" class="form-control" style="display:inline-block;vertical-align:middle;" placeholder="输入验证码" required>
				</td>
				<td colspan="2">
				<select class="form-control" name="qq">
				'.$contents.'
				</select>
				</td>
				<td colspan="2">
				<input type="submit" name="submit" value="签到" class="btn btn-info"/>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
</div>';
if($jfjf)echo '<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">
			领取VIP( ' . $jfjf . ' '.$conf['coin_name'].'兑换 1 天VIP)
		</h3>
	</div>
	<form method="post" action="index.php?mod=qd">
	<table class="table table-bordered">
		<tbody align="center">
			<tr>
				<td colspan="2">
					签到积分
				</td>
				<td colspan="1">
					可领天数
				</td>
				<td colspan="1">
					领取天数
				</td>
				<td colspan="2">
					点击领取
				</td>
			</tr>
			<tr>
				<td colspan="2">
					' . @$vipjf . '
				</td>
				<td colspan="1">
					' . @$vipsj . '
				</td>
				<td colspan="1">
					<input type="text" style="width:100px;" name="data" class="form-control" required>
				</td>
				<td colspan="2">
					<input type="submit" name="submit" value="兑换" class="btn btn-success"/>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
</div>
';

}else
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);

include TEMPLATE_ROOT."foot.php";
?>
