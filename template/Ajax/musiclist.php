<?php
if(!defined('IN_CRONLITE'))exit();

if($conf['limhplayer']==2 && $conf['music_list']){
	$music=@unserialize($conf['music_list']);
	$song_name='';
	$song_id='';
	foreach($music as $val){
		$song_name.=$val['name'].'|';
		$song_id.=$val['id'].'|';
	}
	$song_name=substr($song_name,0,-1);
	$song_id=substr($song_id,0,-1);
	echo 'var wenkmList=[{song_album:"《精选专辑》",song_album1:"我喜欢的一些音乐",song_file:"/",song_name:"'.$song_name.'".split("|"),song_id:"'.$song_id.'".split("|")}];';
}else{
?>
var wenkmList=[{song_album:"《精选专辑》",song_album1:"我喜欢的一些音乐",song_file:"/",song_name:"秋殇别恋|Flower Dance|Faded|英雄联盟|我的天空|入戏太深|芊芊|害怕|帝都|放手了吗|唐人|冷暴力|一亿个伤心|许多年以后|光辉岁月".split("|"),song_id:"1772313955xm|1769834090xm|36990266wy|364757wy|28892408wy|29818815wy|102138wy|33941661wy|31721353wy|33785998wy|26524448wy|29786040wy|27901044wy|338870wy|346576wy".split("|")}];
<?php }?>