<?php
/*
 * testurl :
 * getswfx.php?id=9daa5522aaea998fcc220e73&pn=6&rn=5
 * getswfx.php?id=9daa5522aaea998fcc220e73?pn=6&rn=5
 * 脚本初始化
 */
ob_start();
set_time_limit(60);

if(!defined('WEBROOT'))
{	
	define('WEBROOT',$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
}
define('L',DIRECTORY_SEPARATOR);

//get params
$docid = $_REQUEST['docid'];
//$pn = $_REQUEST['pn'];
$pn = 0;
$rn = $_REQUEST['rn'];
$rn = 1; //强制设定每页一个文件.rn恒取1
$totalpages =  $_REQUEST['totalpages'];

@header("Content-Type: application/x-shockwave-flash");
@header('Content-Disposition: attachment; filename="'. $docid . '.swfx"');

/*未取得pn时
 *针对getswfx.php?docid=9daa5522aaea998fcc220e73&totalpages=38?pn=1&rn=5做处理
 *此时totalpages=38?pn=1
 *需要从中分拆出totalpages和pn
 */
if($pn == 0)
{   $pn = substr($totalpages,stripos($totalpages,'?pn=')+4,strlen($totalpages)-stripos($totalpages,'?pn=')-4);
    $totalpages = substr($totalpages,0,stripos($totalpages,'?pn='));
}

$toPage = $pn+$rn-1;
$blankstr = '                                                             ';

$file_head = substr('{"totalPage":"'.$totalpages.'","fromPage":"'.$pn.'","toPage":"'.$toPage.'"}'.$blankstr,0,100);

$subdir = substr(md5($docid),0,2);
$file_path = WEBROOT .'showdoc'. L .'play'. L .$subdir. L .$docid. L .$pn.'_'.$rn.'.swf';
$my_file = file_get_contents($file_path);

echo $file_head.$my_file;
?>
