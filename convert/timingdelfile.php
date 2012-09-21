<?php
/*****
*timimg delete the unwanted files
*everyday
******/
ob_start();
@header("Content-Type: text/html; charset=utf-8");
if(!defined('L')) define('L',DIRECTORY_SEPARATOR);
if(!defined('ROOT')) define('ROOT',dirname(dirname(dirname(__FILE__))).L);
require_once(ROOT.'includes'.L.'config.class.php');
require_once(ROOT.'includes'.L.'dbcontrol.class.php');
$Cfg = new Config;
$Db = new DBControl($Cfg);
$Db->open();

if(!defined('TBPRE')) define('TBPRE', $Cfg->TB_PREFIX);

//begin added by zebin
$g_pdfpath = ROOT.'showdoc'.L.'play';//分割路径建议使用 DIRECTORY_SEPARATOR，避免linux系统路径出问题
//end

print "<br />start delete deleted file and folder ...<br />\r\n";
$date = date('Y-m-d').' 23:59:59';
//删除今天(包括今天)以前所有已经删除了的文件实体
$sql = "select ID,Vc_path,Vc_type,Vc_code from ".TBPRE."file_info where status=0 and Createtime<'$date'";
//write_log('b='.$sql);
$Re = $Db->SelectSql($sql);
while($Rs = $Db->GetRsArray($Re)){
	$vcpath = ROOT.str_replace('/', L, substr($Rs['Vc_path'],1));
	$vcfpath = $vcpath.'.'.$Rs['Vc_type'];
	//write_log('o1='.$vcpath);
	//write_log('o2='.$vcfpath);
	//删除文件
	if(file_exists($vcpath)) unlink($vcpath);
	if(file_exists($vcfpath)) unlink($vcfpath);
	
	//begin added by zebin
	$docid  = $Rs['Vc_code'];
	$subdir = substr(md5($docid),0,2);
	$g_pdfto = $g_pdfpath. L .$subdir. L .$docid;
	//write_log('o3='.$g_pdfto);
	if ( file_exists($g_pdfto)) deldir($g_pdfto);//是否是删除整个文件夹的意思 
	//end
	
	//设置删除实体标记 
	$Db->querySql("update file_info set status=-1 where ID=$Rs[0]");
}
print "<br />end delete deleted file and folder ...<br />\r\n";

$Db->close();

/*function declaration: write log for test*/
function write_log($logs){
	$fp = @fopen("time_log.txt", "a+");
	fwrite($fp, '['.$logs.']'.date("Y-m-d H:i:s")."\r\n");
	fclose($fp);
}

/*删除整个文件夹的函数*/
function deldir($dir) {
	//先删除目录下的文件：
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!='.' && $file!='..') {
			$fullpath=$dir.L.$file;
			if(!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				deldir($fullpath);
			}
		}
	}
	closedir($dh);
	//删除当前文件夹：
	if(rmdir($dir)) {
		return true;
	} else {
		return false;
	}
} 
?>