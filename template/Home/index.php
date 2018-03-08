<?php
if(!defined('IN_CRONLITE'))exit();
@header('Content-Type: text/html; charset=UTF-8');
$qqs=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1"); //获取QQ数量
$users=$DB->count("SELECT count(*) from ".DBQZ."_user WHERE 1"); //获取用户数量
/*
$qqjobs=$DB->count("SELECT count(*) from ".DBQZ."_qqjob WHERE 1");
$signjobs=$DB->count("SELECT count(*) from ".DBQZ."_signjob WHERE 1");
$wzjobs=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE 1");
$zongs=$qqjobs+$signjobs+$wzjobs; //获取总任务数量
$info['times'] //系统累计运行的次数
$yxts=ceil((time()-strtotime($conf['build']))/86400); //本站已运行多少天
*/
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $conf['sitename'].$conf['sitetitle']?></title>		
        <meta name="description" content="<?php echo $conf['description']?>">
        <meta name="keywords" content="<?php echo $conf['keywords']?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link rel="shortcut icon" href="images/favicon.ico">
        <link rel="stylesheet" href="<?php echo $cdnserver?>assets/vendor/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/jquery.fancybox.css">
        <link rel="stylesheet" href="<?php echo $cdnserver?>assets/vendor/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/owl.carousel.css">
        <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/slit-slider.css">
        <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/basic.css">
    </head>
    <body id="body">

        <header id="navigation" class="navbar-inverse navbar-fixed-top animated-header">
            <div class="container">
                <div class="navbar-header">
				<?php if($is_fenzhan==1) $logoname = DBQZ;else $logoname = ''; 
					if(!file_exists(ROOT.'images/'.$logoname.'logo.png')) $logoname='';
				?>
					<h1 class="navbar-brand" style="padding:0;">
						<a href="#body"><img src="images/<?php echo $logoname?>logo.png" height="48" width="180" alt="<?php echo $conf['sitename']?>"/></a>
					</h1>
                </div>

                <nav class="collapse navbar-collapse navbar-right" role="navigation">
                
                    <ul id="nav" class="nav navbar-nav">
                        <li><a href="#body">主页</a></li>
                        <li><a href="#service">特点</a></li>
                        <li><a href="#portfolio">功能</a></li>
                        <li><a href="#testimonials">微语</a></li>
                        <li><a href="#price">购买</a></li>
                        <li><a href="#social">展示</a></li>
                        <li><a href="#contact">关于</a></li>
                    </ul>
                </nav>
				
            </div>
        </header>
		<main class="site-content" role="main">
		<section id="home-slider">
            <div id="slider" class="sl-slider-wrapper">
				<div class="sl-slider">
					<div class="sl-slide" data-orientation="horizontal" data-slice1-rotation="3" data-slice2-rotation="3" data-slice1-scale="2" data-slice2-scale="1">
						<div class="bg-img bg-img-1"></div>
						<div class="slide-caption">
                            <div class="caption-content">
                            <h1><?php echo $conf['sitename']?></h1>
                                <h2>免费提供24H离线秒赞秒评</h2>
                                <span>本站功能一键开启,无需安装软件,电脑,平板,手机全部一站式通用!</span>
								<?php if($islogin==1){?>
								<a href="index.php?mod=user" class="btn btn-blue btn-effect" style="margin-top:15px;">用户中心</a>
								<?php }else{?>
								<a href="index.php?mod=login" class="btn btn-blue btn-effect" style="margin-top:15px;" pjax="no">登录</a>
								<a href="index.php?mod=reg" class="btn btn-blue btn-effect" style="margin-top:15px;" pjax="no">注册</a>
								<?php }?>
								<span style="margin-top:40px;">目前我们正在为 <font color=red><?php echo $users?></font> 用户的 <font color=red><?php echo $qqs?></font> 个QQ提供服务,欢迎您的加入。</span>
                            </div>
                        </div>
					</div>
				</div>
                </div>
			</div>
		</section>
			
		<section id="service">
			<div class="container">
				<div class="row">

					<div class="col-md-3 col-sm-6 col-xs-12 text-center wow animated zoomIn">
						<div class="service-item">
							<div class="service-icon">
								<i class="fa fa-home fa-3x"></i>
							</div>
							<h3>一站式管理</h3>
							<p>使用<?php echo $conf['sitename']?>进行挂机，无需安装任何额外软件，注册登陆后添加QQ即可正常运行，您可以随时在电脑/手机/平板登陆本网站进行功能设置 </p>
						</div>
					</div>
				
					<div class="col-md-3 col-sm-6 col-xs-12 text-center wow animated zoomIn" data-wow-delay="0.3s">
						<div class="service-item">
							<div class="service-icon">
								<i class="fa fa-tasks fa-3x"></i>
							</div>
							<h3>分布式架构</h3>
							<p>高配置服务器采用分布式监控系统的运行，24小时不间断稳定不宕机，服务器秒级切换更改随心，离线托管完美使用体验 </p>
						</div>
					</div>
				
					<div class="col-md-3 col-sm-6 col-xs-12 text-center wow animated zoomIn" data-wow-delay="0.6s">
						<div class="service-item">
							<div class="service-icon">
								<i class="fa fa-clock-o fa-3x"></i>
							</div>
							<h3>智能提醒服务</h3>
							<p>SID/KEY过期后邮件自动推送，<?php echo $conf['sitename']?>使用付费邮局进行发信，保证邮件的送达率，让您秒赞24小时正常运行一切由我们来操作 </p>
						</div>
					</div>
				
					<div class="col-md-3 col-sm-6 col-xs-12 text-center wow animated zoomIn" data-wow-delay="0.9s">
						<div class="service-item">
							<div class="service-icon">
								<i class="fa fa-heart fa-3x"></i>
							</div>
							
							<h3>各种实用小工具</h3>
							<p>秒赞检测，单向好友检测，帮你一键找出单向好友，说说刷队形，说说刷赞，一键圈圈赞，让你爱不释手。</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="portfolio">
			<div class="container">
				<div class="row">
				
					<div class="sec-title text-center wow animated fadeInDown">
						<h2>功能介绍</h2>
					</div>
                    
					<div class="col-md-6 contact-form wow animated fadeInLeft">
						<address class="contact-details">
							<h3>如何使用？</h3>						
							<div class="vertical-timeline-block">
                              <p>在用户界面中添加需要托管的QQ号，之后点击开启秒赞功能即可离线运行，无需一直开着网页，任务会24小时自动运行。</p><br>
                              <p>更有全网独家的配套图文说说系统，多种语录可以选择，文字与图片配套，全天24小时自动发表，让你的小伙伴羡慕嫉妒去吧！</p><br>
                             <p>全新的QQ等级代挂功能只需提交QQ即可每天满加速，快速升级QQ等级到皇冠不再是梦。</p>
						</address>
					</div>
					
					<div class="col-md-6 wow animated fadeInRight">
						<address class="contact-details">
							<h3>拥有的功能</h3>						
							<div class="aini">
							<a title="秒赞">秒赞</a>
							<a title="秒评">秒评</a>
							<a title="离线挂Q">离线挂Q</a>
							<a title="包月转发说说">转发说说</a>
							<a title="平台互刷留言">互刷留言</a>
							<a title="刷访问量">刷访问量</a>
							<a title="刷主页赞">刷主页赞</a>
							<a title="自动空间签到">空间签到</a>
							<a title="每日自动浇花">自动浇花</a>
							<a title="快速清理留言">快速清理留言</a>
							<a title="定时删除说说">定时删除说说</a>
							<a title="图书签到">图书签到</a>
							<a title="QQ会员多项签到">QQ会员多项签到</a>
							<a title="QQ钱包签到">QQ钱包签到</a>
                            <a title="QQ群签到">QQ群签到</a>
							<a title="绿钻签到">绿钻签到</a>
							<a title="说说圈图">说说圈图</a>
							<a title="单向好友检测">单向好友检测</a>
							<a title="秒赞好友检测">秒赞好友检测</a>
							<a title="刷圈圈赞99">刷圈圈赞99+</a>
							<a title="说说刷赞">说说刷赞</a>
                            <a title="全套QQ等级代挂">全套QQ等级代挂</a>
							<a href="index.php?mod=help" title="查看更多功能">>>查看更多功能</a>
                            </div>
                            
						</address>
					</div>
				</div>
			</div>
		</section>

		<section id="testimonials" class="parallax">
			<div class="overlay">
				<div class="container">
					<div class="row">
					
						<div class="sec-title text-center white wow animated fadeInDown">
							<h2>站点微语</h2>
						</div>
						
						<div id="testimonial" class=" wow animated fadeInUp">
							<div class="testimonial-item text-center">
								
								<div class="clearfix">
									<span>秒赞</span>
									<p>24H秒赞、挂Q功能本平台免费使用，操作简单无需安装软件，安卓/苹果IOS通用，VIP会员服务全网最具性价比，功能全面为您秒赞好友说说，不漏掉每一条动态，为您秒评论好友说说，让Ta时时刻刻感受到你的存在，增加您和好友的亲密度</p>
								</div>
							</div>
							<div class="testimonial-item text-center">
								
								<div class="clearfix">
									<span>功能</span>
									<p>多功能、双协议，24小时稳定运行，无需安装软件，操作简单快速上手，已研发QS点赞协议保证不漏赞，采用高配置独立服务器，极速秒赞，全天24小时监控执行，完美离线使用。</p>
								</div>
							</div>
							<div class="testimonial-item text-center">
								
								<div class="clearfix">
									<span>追求</span>
									<p>我们始终坚持以用户需求为导向，为追求用户体验设计，提供最完善的秒赞服务，我们将不断地超越自我，挑战自我！</p>
								</div>
							</div>
						</div>
					
					</div>
				</div>
			</div>
		</section>

		<section id="price">
			<div class="container">
				<div class="row">
				
					<div class="sec-title text-center wow animated fadeInDown">
						<h2>VIP会员</h2>
						<p>VIP用户可获得更好的使用体验和更多的特权功能</p>
					</div>
					
					<div class="col-md-4 wow animated fadeInUp">
						<div class="price-table text-center">
							<span>月付VIP</span>
							<div class="value">
								<span>$</span>
								<span>5元</span><br>
								<span>30天</span>
							</div>
							<ul>
								<li>享用VIP专属服务器</li>
								<li>频率可设置最低1分钟</li>
								<li>开启基础VIP功能</li>
								<li>可使用单向好友检测</li>
								<li><a href="index.php?mod=shop">购买</a></li>
							</ul>
						</div>
					</div>
					
					<div class="col-md-4 wow animated fadeInUp" data-wow-delay="0.4s">
						<div class="price-table featured text-center">
							<span>季度VIP</span>
							<div class="value">
								<span>$</span>
								<span>12元</span><br>
								<span>90天</span>
							</div>
							<ul>
								<li>享用VIP专属服务器</li>
								<li>享受秒赞10秒频率</li>
								<li>开启全部VIP功能</li>
								<li>可使用单向好友检测</li>
								<li><a href="index.php?mod=shop">购买</a></li>
							</ul>
						</div>
					</div>
					
					<div class="col-md-4 wow animated fadeInUp" data-wow-delay="0.8s">
						<div class="price-table text-center">
							<span>年费VIP</span>
							<div class="value">
								<span>$</span>
								<span>36元</span><br>
								<span>365天</span>
							</div>
							<ul>
								<li>享用VIP专属服务器</li>
								<li>享受秒赞10秒频率</li>
								<li>开启全部VIP功能</li>
								<li>最新功能优先使用权</li>
								<li><a href="index.php?mod=shop">购买</a></li>
							</ul>
						</div>
					</div>
	
				</div>
			</div>
		</section>

		<section id="social" class="parallax">
			<div class="overlay">
				<div class="container">
					<div class="row" style="margin:0 auto;">
					
						<div class="sec-title text-center white wow animated fadeInDown">
							<h2>最新添加的QQ</h2>
						</div>
<?php //获取最新QQ列表
$liukay=$DB->query("select qq,time from ".DBQZ."_qq order by id desc limit 24");
while ($lingku = $DB->fetch($liukay))
{
echo '<div class="col-xs-3 col-md-2 col-lg-1"><a href="./index.php?mod=search&q='.$lingku['qq'].'" target="_blank"><img class="qqlogo"  src="//q1.qlogo.cn/g?b=qq&nk='.$lingku['qq'].'&s=100" width="80px" height="80px" alt="'.$lingku['qq'].'" title="'.$lingku['qq'].'|添加时间:'.$lingku['time'].'"></a></div>';
}
?>
                    
						
					</div>
				</div>
			</div>
		</section>

		<section id="contact" >
			<div class="container">
				<div class="row">
					
					<div class="sec-title text-center wow animated fadeInDown">
						<h2>关于平台</h2>
					</div>
					
					<div class="col-md-6 contact-form wow animated fadeInLeft">
						<address class="contact-details">
							<h3>秒赞介绍</h3>						
							<p>本站采用阿里云集群高配置服务器24H不间断处理秒赞服务，保证了秒赞服务的稳定和可靠性，到目前已经有<?php echo $users?>个用户选择了<?php echo $conf['sitename']?>，这是为什么？因为我们提供最全面的功能体验，优质稳定的服务，并且我们的信誉有保障，保证你的账号安全不泄露！选择<?php echo $conf['sitename']?>，才是你最好的选择，欢迎免费进行体验！
                            </p>
						</address>
					</div>
					
					<div class="col-md-6 wow animated fadeInRight">
						<address class="contact-details">
							<h3>联系我们</h3>						
							<p><i class="fa fa-pencil"></i><?php echo $conf['sitename']?><span><?php echo $_SERVER['HTTP_HOST']?></span>
                            </p><br>
							<p><i class="fa fa-phone"></i>站长QQ：<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq']?>&site=qq&menu=yes" target="_blank"><?php echo $conf['kfqq']?></a></p>
							<p><i class="fa fa-envelope"></i><?php echo $conf['kfqq']?>@qq.com</p>
						</address>
					</div>
		
				</div>
			</div>
		</section>
		
		</main>
		<footer id="footer">
			<div class="container">
				<div class="row text-center">
					<div class="footer-content">
						<div class="wow animated fadeInDown">
							<p><?php echo $conf['sitename']?></p>
							<p>免费24H离线秒赞秒评系统</p>
						</div>
						<p>Copyright &copy; 2015-2016 <a href="<?php echo $siteurl?>" title="<?php echo $conf['sitename']?>"><?php echo $conf['sitename']?></a></p>
                        <!--p><a  href="http://www.miitbeian.gov.cn/"  target="_blank">备案号</a></p-->
					</div>
				</div>
			</div>
		</footer>
        <script src="<?php echo $cdnserver?>assets/js/modernizr-2.6.2.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/vendor/js/jquery-2.1.4.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/jquery.singlePageNav.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/jquery.fancybox.pack.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/owl.carousel.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/jquery.easing.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/jquery.slitslider.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/wow.min.js"></script>
        <script src="<?php echo $cdnserver?>assets/js/main.js"></script>
<?php if(!empty($conf['ui_backmusic'])){?>
<section class="u-audio hidden" data-src="<?php echo $conf['ui_backmusic']?>"></section>
<div class="btnAudio" id="btnAudio"></div>
 
<script>
var bg_audio_val = true;
var bg_audio = new Audio();
function audio_init(){
        var options_audio = {
                loop: true,
                preload: "auto",
                src: $('.u-audio').attr('data-src')
        }
        for (var key in options_audio) {
                bg_audio[key] = options_audio[key];
        }
        bg_audio.load();
        audio_addEvent();
        bg_audio.play();
}
function audio_addEvent(){
        $("#btnAudio").on('click', audio_control);
        $(bg_audio).on('play',function(){
                bg_audio_val = false;
                $('#btnAudio').addClass('rotate1circle');
        })
        $(bg_audio).on('pause',function(){
                $('#btnAudio').removeClass('rotate1circle');
        })
}
function audio_control(){
        if(!bg_audio_val){
                bg_audio.pause();
                bg_audio_val = true;
        }else{
                bg_audio.play();
        }
}
audio_init();
</script>
<?php }?>
    </body>
</html>