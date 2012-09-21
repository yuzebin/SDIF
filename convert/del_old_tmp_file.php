<?php
/*
 *
 */
define('L',DIRECTORY_SEPARATOR);

require_once("config.php");
require_once dirname(__FILE__) . L . 'convert_func.php';

func_print("\r\n");
func_print("start delete temp file that older than 1 day ...");

//遍历目录/play/delayhandle/下的文件,挨个检查
$now_stamp = time();
func_print($now_stamp);
foreach(glob($config['absPath'].'showdoc\play\*.[tp][xd][tf]') as $tmp_file) 
//foreach(glob('/Users/yuzebin/vbox/vbox_share/zebin/zhengbaojun/showdoc/convert/*.[lp][oh][gp]') as $tmp_file) 
{
    func_print(date("Y-m-d H:i:s", filectime($tmp_file)));
    if(($now_stamp - filectime($tmp_file))>86400)
    {
        //confirm is old tmp file , to delete
        unlink($tmp_file);
        func_print('del_old_tmp_file -> '.$tmp_file);
    }
}

func_print("finish delete old tmp file.");
func_print("\r\n");
?>