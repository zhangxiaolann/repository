<?php
if(!defined('IN_CRONLITE'))exit();

$users=$DB->count("SELECT count(*) from wjob_user WHERE 1");
$qqs=$DB->count("SELECT count(*) from wjob_qq WHERE 1");

$content=file_get_contents(ROOT."template/index.html");
$content=str_replace("{:C('web_name')}",$conf['sitename'],$content);
$content=str_replace("{:C('web_qq')}",$conf['kfqq'],$content);
$content=str_replace("{:C('web_domain')}",$siteurl,$content);
$content=str_replace("{:get_count('users')}",$users,$content);
$content=str_replace("{:get_count('qqs')}",$qqs,$content);

echo $content;

?>