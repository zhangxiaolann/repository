<?php
//HTML5浮动播放器音乐解析模块

error_reporting(0);
define('SYSTEM_ROOT', dirname(dirname(__FILE__)).'/includes/');
$do=isset($_GET['do'])?$_GET['do']:exit;

switch($do){
	case 'parse':
		$type=isset($_GET['type'])?$_GET['type']:exit;
		$id=isset($_GET['id'])?$_GET['id']:null;
		$callback=isset($_GET['callback'])?$_GET['callback']:null;
		if($type=='wy'){
			if(is_file(SYSTEM_ROOT."cache/".md5($id))){
				$data=json_decode(file_get_contents(SYSTEM_ROOT."cache/".md5($id)),true);
				$type="cache";
			}else{
				$post=encode_netease_data(array(
					'method' => 'GET',
					'url'    => 'http://music.163.com/api/song/detail',
					'params' => array(
					  'id'  => $id,
					  'ids' => '['.$id.']'
					)
				));
				$data=get_curl('http://music.163.com/api/linux/forward',http_build_query($post),'http://music.163.com/');
				$arr=json_decode($data, true);
				if(array_key_exists('songs',$arr)){
				$music_url = $arr['songs'][0]['mp3Url'];
				if (!$music_url) {
					$post=encode_netease_data(array(
                              'method' => 'POST',
                              'url' => 'http://music.163.com/api/song/enhance/player/url',
                              'params' => array(
                                  'ids' => array($id),
                                  'br'  => 320000,
                              )
                          ));
					$data=get_curl('http://music.163.com/api/linux/forward',http_build_query($post),'http://music.163.com/');
					$arr2=json_decode($data, true);
					if (!empty($arr2)) {
						$music_url = $arr2['data'][0]['url'];
					}
				}
				$arr=array(
					"SongName"=>$arr['songs'][0]['name'],
					"Artist"=>$arr['songs'][0]['artists'][0]['name'],
					"Album"=>$arr['songs'][0]['album']['name'],
					"ListenUrl"=>$music_url,
					"PicUrl"=>$arr['songs'][0]['album']['picUrl'],
				);
				}else{
					$data=get_curl('http://music.api.cccyun.cc/163music.php?id='.$id);
					$arr=json_decode($data,true);
				}
				//file_put_contents(SYSTEM_ROOT."cache/".md5($id),json_encode($arr));
				$data=$arr;
				$type="new";
			}
			//$data["ListenUrl"]=get_curl('http://music.api.cccyun.cc/163music.php?id='.$id);
			$SongName=$data["SongName"];
			$Artist=$data["Artist"];
			$Album=$data["Album"];
			$ListenUrl=$data["ListenUrl"];
			$PicUrl=$data["PicUrl"];
		}elseif($type=='xm'){
			$data=get_curl('http://www.xiami.com/song/playlist/id/'.$id.'/type/0/cat/json');
			$arr=json_decode($data, true);
			$SongName=$arr['data']['trackList'][0]['title'];
			$Artist=$arr['data']['trackList'][0]['artist'];
			$Album=$arr['data']['trackList'][0]['album_name'];
			$ListenUrl=ipcxiami($arr['data']['trackList'][0]['location']);
			$LrcUrl=$arr['data']['trackList'][0]['lyric'];
			$PicUrl=$arr['data']['trackList'][0]['album_pic'];
		}elseif($type=='bd'){
			$data=get_curl('http://music.baidu.com/data/music/fmlink?songIds='.$id.'&type=mp3&rate=320');
			$arr=json_decode($data,true);
			//print_r($arr);exit;
			preg_match('!music/(\d+)/!',$arr['data']['songList'][0]['songLink'],$json);
			$songid=$json[1];
			$SongName=$arr['data']['songList'][0]['songName'];
			$Artist=$arr['data']['songList'][0]['artistName'];
			$Album=$arr['data']['songList'][0]['albumName'];
			$ListenUrl='http://musicdata.baidu.com/data2/music/'.$songid.'/'.$songid.'.mp3';
			$LrcUrl=$arr['data']['songList'][0]['lrcLink'];
			$PicUrl=$arr['data']['songList'][0]['songPicRadio'];
		}elseif($type=='qq'){
			if(is_file(SYSTEM_ROOT."cache/".md5($id))){
				$data=json_decode(file_get_contents(SYSTEM_ROOT."cache/".md5($id)),true);
				$type="cache";
			}else{
				$data=get_curl('http://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg?songmid='.$id.'&format=json',0,'http://y.qq.com/n/yqq/song/'.$id.'.html');
				$arr=json_decode($data,true);
				$data=array(
					"SongName"=>$arr['data'][0]['name'],
					"Artist"=>$arr['data'][0]['singer'][0]['name'],
					"Album"=>$arr['data'][0]['album']['name'],
					"ListenUrl"=>'http://'.$arr['url'][$arr['data'][0]['id']],
					"PicUrl"=>'http://y.gtimg.cn/music/photo_new/T002R300x300M000'.$arr['data'][0]['album']['mid'].'.jpg',
				);
				$type="new";
			}

			$SongName=$data["SongName"];
			$Artist=$data["Artist"];
			$Album=$data["Album"];
			$ListenUrl=$data["ListenUrl"];
			$PicUrl=$data["PicUrl"];
		}
		echo $callback.'({"location":"'.$ListenUrl.'","lyric":"'.$LrcUrl.'","album_cover":"'.$PicUrl.'","album_name":"'.$Album.'","artist_name":"'.$Artist.'","song_name":"'.$SongName.'","song_id":"'.$id.'"}) ';
	break;

	case 'lyric':
		if($_GET['type']=='wy'){
			$id=isset($_GET['id'])?$_GET['id']:null;
			$data=get_curl('http://music.163.com/api/song/lyric?os=pc&id='.$id.'&lv=-1&kv=-1&tv=-1',0,'http://music.163.com/');
			$arr=json_decode($data, true);
			$data=$arr['lrc']['lyric'];
		}elseif($_GET['type']=='qq'){
			$id=isset($_GET['id'])?$_GET['id']:null;
			$data=get_curl("http://i.y.qq.com/lyric/fcgi-bin/fcg_query_lyric.fcg?pcachetime=".time()."&songmid=".$id,0,"http://y.qq.com/",0);
			preg_match('!MusicJsonCallback\((.*?)\)!',$data,$json);
			$arr=json_decode($json[1], true);
			$data=str_replace(array('&#58;','&#40;','&#41;','&#32;','&#10;','&#45;','&#46;',';',"\r"),array(';','(',')',' ','','-','.',':',''),base64_decode($arr['lyric']));
		}else{
			$url=isset($_GET['url'])?$_GET['url']:null;
			$data=get_curl($url);
		}
		echo "var cont = '".str_replace("\n","",$data)."';";
	break;

	case 'color':
		if (!$_GET["url"]) {
			exit("BAD ARGS!");
		}
		if (is_file(SYSTEM_ROOT."cache/" . md5($_GET["url"]))) {
			$data = json_decode(file_get_contents(SYSTEM_ROOT."cache/" . md5($_GET["url"])) , true);
		} else {
			$color = imgColor($_GET["url"]);
			$img_color = "{$color['r']},{$color['g']},{$color['b']}";
			$data = array(
				"img_color" => $img_color
			);
			file_put_contents(SYSTEM_ROOT."cache/" . md5($_GET["url"]) , json_encode($data));
		}
		echo "var cont = '" . $data["img_color"] . "';";
	break;
}
function imgColor($url) {
    $imageInfo = getimagesize($url);
    //图片类型
    $imgType = strtolower(substr(image_type_to_extension($imageInfo[2]) , 1));
    //对应函数
    $imageFun = 'imagecreatefrom' . ($imgType == 'jpg' ? 'jpeg' : $imgType);
    $i = $imageFun($url);
    //循环色值
    $rColorNum = $gColorNum = $bColorNum = $total = 0;
    for ($x = 0; $x < imagesx($i); $x++) {
        for ($y = 0; $y < imagesy($i); $y++) {
            $rgb = imagecolorat($i, $x, $y);
            //三通道
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $rColorNum+= $r;
            $gColorNum+= $g;
            $bColorNum+= $b;
            $total++;
        }
    }
    $rgb = array();
    $rgb['r'] = round($rColorNum / $total);
    $rgb['g'] = round($gColorNum / $total);
    $rgb['b'] = round($bColorNum / $total);
    return $rgb;
}
function get_curl($url, $post=0, $referer=0, $cookie=0, $header=0, $ua=0, $nobaody=0)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept:application/json";
	$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
	$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
	$httpheader[] = "Connection:close";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if($referer){
		if($referer==1){
			curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
		}else{
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
	}
	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	}
	else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (MSIE 9.0; Windows NT 6.1; Trident/5.0)");
	}
	if ($nobaody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
function ipcxiami($location){
$count = (int)substr($location, 0, 1);
$url = substr($location, 1);
$line = floor(strlen($url) / $count);
$loc_5 = strlen($url) % $count;
$loc_6 = array();
$loc_7 = 0;
$loc_8 = '';
$loc_9 = '';
$loc_10 = '';
while ($loc_7 < $loc_5){
$loc_6[$loc_7] = substr($url, ($line+1)*$loc_7, $line+1);
$loc_7++;
}
$loc_7 = $loc_5;
while($loc_7 < $count){
$loc_6[$loc_7] = substr($url, $line * ($loc_7 - $loc_5) + ($line + 1) * $loc_5, $line);
$loc_7++;
}
$loc_7 = 0;
while ($loc_7 < strlen($loc_6[0])){
$loc_10 = 0;
while ($loc_10 < count($loc_6)){
$loc_8 .= @$loc_6[$loc_10][$loc_7];
$loc_10++;
}
$loc_7++;
}
$loc_9 = str_replace('^', 0, urldecode($loc_8));
return $loc_9;
}

// 加密网易云音乐 api 参数
function encode_netease_data($data)
{
    $_key = '7246674226682325323F5E6544673A51';
    $data = json_encode($data);
    if (function_exists('openssl_encrypt')) {
        $data = openssl_encrypt($data, 'aes-128-ecb', pack('H*', $_key));
    } else {
        $_pad = 16 - (strlen($data) % 16);
        $data = base64_encode(mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            hex2bin($_key),
            $data.str_repeat(chr($_pad), $_pad),
            MCRYPT_MODE_ECB
        ));
    }
    $data = strtoupper(bin2hex(base64_decode($data)));
    return array('eparams' => $data);
}