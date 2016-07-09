<?php
 /*
　*　功能简介文件
*/
if(!defined('IN_CRONLITE'))exit();
$title="功能简介";
include_once(TEMPLATE_ROOT."head.php");

navi();

echo'<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">功能简介</h3></div>';
echo'<div class="panel-body box">';
echo'<b>V5新版特性：</b><br/>●全新界面：基于Bootstrap设计，响应式布局，电脑与智能手机两用。<br/>
●强大的任务运行机制：分布式任务调度、秒刷模式、多线程模式，提升运行效率，并力求将服务器负载降到最低。自定义运行时间,自定义使用代理,自定义代理ip及端口号,自定义POST模拟,自定义POST数据,自定义来源地址,自定义模拟浏览器,暂停网络任务<br/>
●完善的QQ管理系统：增加QQ账号管理，添加QQ任务更加快捷，一键更新失效的sid。<br/>
●丰富的QQ挂机功能：拥有说说秒赞、秒评、自动图片说说、3G挂Q、QQ机器人等挂机功能。<br/>
●自动签到：包含柯林、DZ、360、115、新浪微盘、虾米音乐、文网、刀云等自动签到插件，并支持扩展<br/>
●强大的任务管理：支持批量添加、文件导入导出，支持暂停任务，任务运行状况一目了然<br/>
●更多的界面风格：可在新版界面和旧版界面自由切换，同时针对两种界面分别预置了多款不同的皮肤供你选择<br/>
●全平台支持：支持ACE、SAE等应用引擎，支持SQLite和MySQL两种数据库<br/>
●安全性保障：360网站卫士全局防SQL注入、IP禁访配置、网址屏蔽配置<br/>
</div>';
echo'<div class="panel-heading w h"><h3 class="panel-title">ＱＱ功能一览</h3></div>';
echo'<div class="panel-body box"><ul class="list-group" style="list-style:none;"><font color="#2200DD">';
echo'<li class="list-group-item">1、3GQQ、JAVA双协议挂Q</li>';
echo'<li class="list-group-item">2、触屏、PC双协议秒赞</li>';
echo'<li class="list-group-item">3、触屏、PC双协议秒评</li>';
echo'<li class="list-group-item">4、双协议自动空间签到</li>';
echo'<li class="list-group-item">5、双协议自动发表说说</li>';
echo'<li class="list-group-item">6、双协议自动删除说说</li>';
echo'<li class="list-group-item">7、双协议自动转发说说</li>';
echo'<li class="list-group-item">8、图书签到,VIP签到,花藤服务</li>';
echo'<li class="list-group-item">9、单向好友检测工具</li>';
echo'</font></ul>
</div>';
echo'<div class="panel-heading w h"><h3 class="panel-title">签到功能一览</h3></div>';
echo'<div class="panel-body box"><ul class="list-group" style="list-style:none;"><font color="#2200DD">';
echo'<li class="list-group-item">1、柯林自动签到</li>';
echo'<li class="list-group-item">2、Discuz自动签到</li>';
echo'<li class="list-group-item">3、Discuz自动打卡</li>';
echo'<li class="list-group-item">4、115网盘签到</li>';
echo'<li class="list-group-item">5、360云盘签到</li>';
echo'<li class="list-group-item">6、新浪微盘签到</li>';
echo'<li class="list-group-item">7、虾米音乐签到</li>';
echo'<li class="list-group-item">8、福利论坛签到</li>';
echo'<li class="list-group-item">9、文网自动签到</li>';
echo'</font></ul>
</div>';
echo'<div class="panel-heading w h"><h3 class="panel-title">新手帮助</h3></div>';
echo'<div class="panel-body box">';
echo'●<b>如何添加秒赞任务？</b><br/>1.注册登录系统后进入QQ管理，点击上方的“添加QQ账号”。<br/>2.添加完成后，会自动返回到QQ列表，点击你刚才添加的QQ号，即进入任务列表。<br/>3.在任务列表上方点击“添加QQ挂机任务”，然后点击“添加空间说说秒赞任务”。<br/>4.设置好运行时间段和任务系统之后点击“提交”，即可成功添加任务到本系统。<br/>5.在任务列表中可以看到任务的运行情况。<br/>6.QQ管理中可查看SID是否失效，如果提示失效请手动更新sid。<br/>';
echo'●<b>如何添加签到任务？</b><br/>1.注册登录系统后进入任务管理。<br/>2.选择一个任务系统，注意每个系统的执行频率，签到任务建议选择6～12小时的。<br/>3.在任务列表上方点击“添加任务”，然后点击“添加网站签到任务”。<br/>4.进入相应的签到模块根据提示完成任务添加。';
echo'</div>';
echo'<div class="panel-heading w h"><h3 class="panel-title">关于网络任务</h3></div>';
echo'<div class="panel-body box">';
echo'什么是网络任务?网络任务是可以一天24小时不间断执行某一特定动作的特殊程序.利用网络任务可以轻易完成很多重复的动作,例如不间断访问某网页,或者定时执行某些程序等等.
';
echo'</div></div>';

echo'<div class="panel panel-primary"><div class="panel-body box" style="text-align: center;">';
echo date("Y年m月d日 H:i:s");
include(ROOT.'includes/foot.php');
echo'</div>';
if($conf['sjyl']==1)
{$txt=file(ROOT.'includes/content/content.db');
shuffle($txt);
echo"$txt[0]";}
echo'</div>
</div>
</div></body></html>';
?>