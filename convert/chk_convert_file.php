<?php
/*
 *
 */
ob_start();
set_time_limit(600);

if(!defined('WEBROOT'))
{
    define('WEBROOT',$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
}
define('L',DIRECTORY_SEPARATOR);
require_once("config.php");

@header("Content-Type: text/html; charset=utf-8");
require_once dirname(__FILE__) . L . 'convert_func.php';

$g_pdfpath = $config['absPath'].'showdoc\play';

func_print("\r\n");
func_print("start check delay handle folder ...");

//检查soffice服务
$m_cmdline = "tasklist /FI \" Imagename eq soffice.exe\"";
$m_result = exec($m_cmdline);
if (strstr($m_result,"soffice") == false)
{
    func_print("soffice not run");
    $m_cmdline = $config['officePath']."soffice.exe\" -headless -accept=\"socket,host=127.0.0.1,port=8100;urp;\" -nofirststartwizard";
    $m_cmdline = "start /D ".$config['officePath']." soffice.exe -headless -accept=\"socket,host=127.0.0.1,port=8100;urp;\" -nofirststartwizard";
    func_print($m_cmdline);
    exec($m_cmdline);
}
else {
    func_print("soffice is running...");

    //遍历目录/play/delayhandle/下的文件,挨个检查
    $g_phpdir = $config['absPath']."showdoc\play\delayhandle";
    foreach(glob($config['absPath'].'showdoc\play\delayhandle\*.php') as $delay_handle_file) {
        $t_docid = substr($delay_handle_file,stripos($delay_handle_file,"delayhandle")+12,stripos($delay_handle_file,".php")-stripos($delay_handle_file,"delayhandle")-12);
        func_print($t_docid);

        $g_pdfto = $g_pdfpath . L . func_docid2path($t_docid);
        func_print($g_pdfto);

        $page=func_getpages($g_pdfto);
        func_print($page);

        if($page == 0)
        {
            //说明上次转换失败,重新转换
            func_print("last convert fail, reconvert...");
            $m_cmdline = $config['phpPath']."php.exe $delay_handle_file";
            func_print($m_cmdline);
            exec ($m_cmdline);
        }
        else
        {       
            func_print("last convert success, delete file");
            //说明上次转换成功,删掉文件即可
            unlink($delay_handle_file);
            
        }
    }
}

func_print("finish check delay handle folder");
func_print("\r\n");

function func_docid2path($t_docid)
{
    $subdir = substr(md5($t_docid),0,2);
    $t_pdfto = $subdir. L .$t_docid;
    return $t_pdfto;
}
?>