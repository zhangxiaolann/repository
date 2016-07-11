<?php
error_reporting(E_ALL & ~E_NOTICE);
@header('Content-Type: text/html; charset=UTF-8');
if(file_exists('install.lock')){
	exit('已经安装完成！如需重新安装，请删除install目录下的install.lock!');
}
$step=is_numeric($_GET['step'])?$_GET['step']:'1';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
	<title>微秒赞程序数据库配置</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="/Style/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Style/bootstrap/css/bootstrap-theme.min.css">
	<script src="/Style/bootstrap/js/jquery.min.js"></script>
	<script src="/Style/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="/Style/css/default.css?3">
</head>
<body>
<div class="mznav">
	<nav class="navbar">
	<div class="container-fluid">
		<div class="navbar-header">
			<p class="navbar-brand">
				<a href="/" class="navbar-logo">数据库配置</a>
			</p>
		</div>
	</div>
	</nav>
</div>
<div class="page-container" style="width: 100%;height: 100%;position: absolute;padding: 15px;background: -webkit-linear-gradient(top, #A481AB, #62708B);">
<div class="container">
	<div class="row"><br>
	<?php if($step==1){?>
		<div class="col-xs-12">
			<div class="list-group">
				<div class="list-group-item list-group-item-info">欢迎使用微秒赞程序</div>
				<div class="list-group-item">
					1、本程序不允许泄露。
				</div>
				<div class="list-group-item list-group-item-warning">
					2、本程序代码不允许再其他程序中使用。
				</div>
				<div class="list-group-item">
					3、如违反以上规定，无任何解释理由，拉黑名单。
				</div>
				<div class="list-group-item">
					4、本程序原作者快乐是福QQ815856515
				</div>
				<div class="list-group-item">
					5、破解：小浩  二开：小浩
				</div>
				<div class="list-group-item">
					6、小浩QQ：1126786437   
				</div>
				<div class="list-group-item">
					7、小浩技术博客  www.ixiaohao.cn
				</div>
				<div class="list-group-item">
					<a href="?step=2" class="btn btn-block btn-warning">同意以上协议，进行下一步</a>
				</div>
			</div>

		</div>
	<?php }elseif($step==2){?>
		<div class="col-xs-12">
			<div class="list-group">
				<div class="list-group-item list-group-item-success">数据库配置</div>
			<form action="?step=3" role="form" class="form-horizontal" method="post">
			<div class="list-group-item">
				<div class="input-group">
					<div class="input-group-addon">数据库地址</div>
					<input type="text" class="form-control" name="DB_HOST" value="localhost">
				</div>
			</div>
			<div class="list-group-item">
				<div class="input-group">
					<div class="input-group-addon">数据库端口</div>
					<input type="text" class="form-control" name="DB_PORT" value="3306">
				</div>
			</div>
			<div class="list-group-item">
				<div class="input-group">
					<div class="input-group-addon">数据库库名</div>
					<input type="text" class="form-control" name="DB_NAME" placeholder="输入数据库库名">
				</div>
			</div>
			<div class="list-group-item">
				<div class="input-group">
					<div class="input-group-addon">数据用户名</div>
					<input type="text" class="form-control" name="DB_USER" placeholder="输入数据库用户名">
				</div>
			</div>
			<div class="list-group-item">
				<div class="input-group">
					<div class="input-group-addon">数据库密码</div>
					<input type="text" class="form-control" name="DB_PWD" placeholder="输入数据库密码">
				</div>
			</div>
			<div class="list-group-item">
				<input type="submit" name="submit" value="保存配置" class="btn btn-primary btn-block">
			</div>
			</form>
				
			</div>
		</div>
	<?php }elseif($step==3){
		if(!$_POST['DB_HOST'] || !$_POST['DB_PORT'] || !$_POST['DB_NAME'] || !$_POST['DB_USER'] || !$_POST['DB_PWD']){
			exit('<script language=\'javascript\'>alert(\'所有项都不能为空\');history.go(-1);</script>');
		}
		if(!$con=mysql_connect($_POST['DB_HOST'].':'.$_POST['DB_PORT'],$_POST['DB_USER'],$_POST['DB_PWD'])){
			exit('<script language=\'javascript\'>alert("连接数据库失败，'.mysql_error().'");history.go(-1);</script>');
		}elseif(!mysql_select_db($_POST['DB_NAME'],$con)){
			exit('<script language=\'javascript\'>alert("选择的数据库不存在，'.mysql_error().'");history.go(-1);</script>');
		}
		$data="<?php
return array(
	'DB_HOST'               =>  '{$_POST['DB_HOST']}',
    'DB_NAME'               =>  '{$_POST['DB_NAME']}',
    'DB_USER'               =>  '{$_POST['DB_USER']}',
    'DB_PWD'                =>  '{$_POST['DB_PWD']}',
    'DB_PORT'               =>  '{$_POST['DB_PORT']}',
    'DB_PREFIX'             =>  'vmz_',
);";
		if(!file_put_contents('../Common/Conf/db.php',$data)){
			exit('<script language=\'javascript\'>alert(\'保存数据库配置文件失败，请检查网站是否有写入权限！\');history.go(-1);</script>');
		}
	?>
		<div class="col-xs-12">
			<div class="list-group">
				<div class="list-group-item list-group-item-info">数据库配置文件保存成功</div>
				<div class="list-group-item">
					<a href="?step=4" onclick="if(!confirm('创建数据表会清空已存在的，是否继续？')){return false;}" class="btn btn-block btn-warning">创建数据表</a>
				</div>
				<div class="list-group-item">
					<a href="?step=4&do=update" onclick="if(!confirm('确定要更新吗？')){return false;}" class="btn btn-block btn-warning">我要更新，更新数据表</a>
				</div>
				<div class="list-group-item">
					<a href="?step=5" class="btn btn-block btn-warning">我已有完整数据，跳过创建！</a>
				</div>
			</div>
		</div>
	<?php }elseif($step==4){
		$mysql=require("../Common/Conf/db.php");
		try{
			$db=new PDO("mysql:host=".$mysql['DB_HOST'].";dbname=".$mysql['DB_NAME'].";port=".$mysql['DB_PORT'],$mysql['DB_USER'],$mysql['DB_PWD']);
		}catch(Exception $e){
			exit('链接数据库失败:'.$e->getMessage());
		}
		$db->exec("set names utf8");
		if($_GET['do']=='update'){
			$sqls=@file_get_contents("updata.sql");
		}else{
			$sqls=@file_get_contents("install.sql");
		}
		$sqls=str_replace('{DB_PREFIX}', 'vmz', $sqls);
		$explode = explode("<fgf>",$sqls);
		$num = count($explode);
		foreach($explode as $sql){
			if($sql=trim($sql)){
				$db->exec($sql);
			}
		}
		if(mysql_error()){
			exit('<script language=\'javascript\'>alert("导入数据表时错误，'.mysql_error().'");history.go(-1);</script>');
		}
		exit("<script language='javascript'>alert('执行SQL成功，共导入{$num}条数据!');window.location.href='?step=5';</script>");

	}elseif($step==5){
		@file_put_contents('install.lock','安装锁定文件');	
	?>
		<div class="col-xs-12">
			<div class="list-group">
				<div class="list-group-item list-group-item-info">秒赞平台安装完成</div>
				<li class='list-group-item'>【牢记】网站后台地址:域名/admin.php</li>
				<li class='list-group-item'>后台默认密码:vmz 请尽快登录后台修改！</li>
				<div class="list-group-item">
					<a href="/" class="btn btn-block btn-success" target="_blank">进入网站首页</a>
				</div>
				<div class="list-group-item">
					<a href="/index.php/Mz/Admin" class="btn btn-block btn-warning" target="_blank">进入网站后台</a>
				</div>
			</div>
		</div>
	<?php }?>
	</div>
</div>	
</div>

</body>
</html>