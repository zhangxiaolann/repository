<?php
if(!defined('IN_CRONLITE'))exit();
@header('Content-Type: text/html; charset=UTF-8');

$sitename=$conf['sitename'];

if($title=='首页' || empty($title))
$titlename=$sitename.' '.$conf['sitetitle'];
else
$titlename=$title.'|'.$sitename.' '.$conf['sitetitle'];

if($conf['ui_background']==0)
$repeat='background-repeat:repeat;';
elseif($conf['ui_background']==1)
$repeat='background-repeat:repeat-x;
background-size:auto 100%;';
elseif($conf['ui_background']==2)
$repeat='background-repeat:repeat-y;
background-size:100% auto;';
elseif($conf['ui_background']==3)
$repeat='background-repeat:no-repeat;
background-size:100% 100%;';
if($conf['ui_background2']==0)
$fixed=null;
elseif($conf['ui_background2']==1)
$fixed='fixed';

if($is_fenzhan) $bjname = DBQZ;else $bjname = '';
if(!file_exists(ROOT.'images/'.$bjname.'bj.png')) $bjname='';

if($conf['ui_bing']==1){
	if(date("Ymd")==$conf['ui_bing_date']){
		$backgroundimage=$conf['ui_backgroundurl'];
		$repeat='background-repeat:no-repeat;
		background-size:100% 100%;';
		$fixed='fixed';
	}else{
		$bing_data=get_curl('http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
		$bing_arr=json_decode($bing_data,true);
		if (!empty($bing_arr['images'][0]['url'])) {
			saveSetting('ui_backgroundurl', 'http://cn.bing.com'.$bing_arr['images'][0]['url']);
			saveSetting('ui_bing_date', date("Ymd"));
			$CACHE->clear();
			$backgroundimage=$bing_arr['images'][0]['url'];
			$repeat='background-repeat:no-repeat;
			background-size:100% 100%;';
			$fixed='fixed';
		}else{
			$backgroundimage='images/'.$bjname.'bj.png';
		}
	}
}else{
	$backgroundimage='images/'.$bjname.'bj.png';
}

if($ismobile==true && $conf['ui_style2'])
	$ui_style=$conf['ui_style2'];
else
	$ui_style=$conf['ui_style'];
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="charset" content="utf-8">
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $titlename?></title>
<meta name="keywords" content="<?php echo $conf['keywords']?>" />
<meta name="description" content="<?php echo $conf['description']?>" />
<link rel="shortcut icon" href="images/favicon.ico">
	<!-- start: CSS file-->

	<!-- Vendor CSS-->
	<link href="<?php echo $cdnserver?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo $cdnserver?>assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" />

	<link href="<?php echo $cdnserver?>assets/plugins/pjax/pjax.css" rel="stylesheet" />
<?php if($ui_style==1 || $ui_style==7){?>
	<!-- Plugins CSS-->
	<link href="<?php echo $cdnserver?>assets/plugins/bootkit/css/bootkit.css" rel="stylesheet" />
	<link href="<?php echo $cdnserver?>assets/plugins/magnific-popup/css/magnific-popup.css" rel="stylesheet" />
	<link href="<?php echo $cdnserver?>assets/plugins/jquery-ui/css/jquery-ui-1.10.4.min.css" rel="stylesheet" />

	<!-- Theme CSS -->
	<link href="<?php echo $cdnserver?>assets/css/jquery.mmenu.css" rel="stylesheet" />
	
	<!-- Page CSS -->		
	<link href="<?php echo $cdnserver?>assets/css/style<?php echo $ui_style==7?'3':null?>.css" rel="stylesheet" />
	<link href="<?php echo $cdnserver?>assets/css/add-ons.min.css" rel="stylesheet" />
	
	<?php if($conf['ui_flat']){
		echo '<link href="'.$cdnserver.'assets/vendor/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />';
	}
}elseif($ui_style==2)
	echo '<link href="'.$cdnserver.'assets/css/style2.css" rel="stylesheet" />';
elseif($ui_style==3)
	echo '<link href="'.$cdnserver.'assets/vendor/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />
	<link href="'.$cdnserver.'assets/css/style2.css" rel="stylesheet" />';
elseif($ui_style==4)
	echo '<link rel="stylesheet" href="'.$cdnserver.'assets/vendor/flat/flat-ui.min.css">
	<link href="'.$cdnserver.'assets/css/style2.css" rel="stylesheet" />';
elseif($ui_style==5)
	echo '<link rel="stylesheet" href="'.$cdnserver.'assets/vendor/todc/todc-bootstrap.min.css">
	<link href="'.$cdnserver.'assets/css/style2.css" rel="stylesheet" />';
elseif($ui_style==6)
	echo '<link rel="stylesheet" href="'.$cdnserver.'assets/vendor/metro/metro-bootstrap.min.css"><link rel="stylesheet" href="'.$cdnserver.'assets/vendor/metro/metro-bootstrap-responsive.min.css">
	<link href="'.$cdnserver.'assets/css/style2.css" rel="stylesheet" />';
?>

	<!-- end: CSS file-->

	<!-- Head Libs -->
	<script src="<?php echo $cdnserver?>assets/plugins/modernizr/js/modernizr.js"></script>
	<script src="<?php echo $cdnserver?>assets/vendor/js/jquery-2.1.4.min.js"></script>
	<!--[if lt IE 9]>
	<script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
<STYLE type="text/css">
	body{cursor:url('images/XSSB-1.cur'), auto;}
	a{cursor:url('images/XSSB-2.cur'), auto;}
body,.main{
background:#ecedf0 url("<?php echo $backgroundimage?>") <?php echo $fixed?>;
<?php echo $repeat?>
}
<?php if($conf['ui_opacity']){?>
.table-responsive,.pagination,.alert{
filter:alpha(opacity=85);
-moz-opacity:0.85;
-khtml-opacity: 0.85;
opacity: 0.85;
}
<?php }?>
</STYLE>
</head>
<body>

<?php
if($conf['switch']=='0')
exit('系统正在升级中，请稍后访问。');

if(!$no_nav){
	if($ui_style==1 || $ui_style==7)include TEMPLATE_ROOT."nav.php";
	else include TEMPLATE_ROOT."nav2.php";
}

?>