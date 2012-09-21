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

@header("Content-Type: text/html; charset=utf-8");
require_once dirname(__FILE__) . L . 'convert_func.php';
require_once dirname(__FILE__) . L . 'index_func.php';
require_once("config.php");

/*
 * 变量初始化
 */

//get params
$docid = $_REQUEST['docid'];
$doctype = $_REQUEST['doctype'];
$g_docfrom = $_REQUEST['docfrom'];

$g_pdftk = $config['absPath'].'tool\pdftk\pdftk.exe';
$g_pdf2swf = $config['absPath'].'tool\SWFTools\pdf2swf.exe';
$g_pdfpath = $config['absPath'].'showdoc\play';
$g_jarpath = $config['absPath'].'tool\solr\bin\post.jar';

$g_url = $config['webPathURL'].'solr/update';
$g_tikapath = $config['absPath'].'tool\tika\tika-app-0.9.jar';
$g_jodpath = $config['absPath'].'tool\jodconverter\jodconverter-cli-2.2.2.jar';

$g_ooffice_port = $config['officePort'];

$g_txtto = $config['absPath'].'showdoc\play'. L .$docid. '.txt';
$g_phpto = $config['absPath'].'showdoc\play\delayhandle'. L .$docid. '.php';

$g_pdffrom = $g_pdfpath. L .$docid.'.pdf';



func_print("\r\n");

func_print("start convert ...");
func_print("docid : " . $docid);
func_print("docfrom : " . $g_docfrom);

func_print("pdfto : " . $g_pdffrom);

func_print("index_file : " . $g_txtto);



//不同文件格式生成pdf


        switch ($doctype)

        {


            case 'txt':
        // copy index convert

                if (!copy($g_docfrom,$g_txtto)) 

                {

                    func_print("copy txt failed!");


                }

                func_add_index($g_jarpath,$g_url,$g_txtto,$docid);

                func_jod_convert($g_jodpath,$g_ooffice_port,$g_docfrom,$g_pdffrom);

            break;

            case 'htm':


            case 'html':

        

    case 'doc':

            case 'dot':

            case 'docx':


            case 'docm':

            case 'rtf':

            case 'xls':

            case 'xlsx':

            case 'xlsm':



            case 'ppt':


            case 'ppz':


            case 'pot':


            case 'pps':


            case 'pptx':


            case 'pptm':
    // extract index convert
 

                func_extract($g_tikapath,$g_docfrom,$g_txtto);

                func_add_index($g_jarpath,$g_url,$g_txtto,$docid);

                func_jod_convert($g_jodpath,$g_ooffice_port,$g_docfrom,$g_pdffrom);

            break;

        
    case 'pdf':
    // extract index copy

                func_extract($g_tikapath,$g_docfrom,$g_txtto);

                func_add_index($g_jarpath,$g_url,$g_txtto,$docid);

            // 如果已经是pdf则无需转换,只要把文件copy过来即可

            if (!copy($g_docfrom,$g_pdffrom)) {
  

               func_print("copy pdf failed!");
 

            }


            break;

        
    default:
        //不支持的格式不处理


            break;

        
}


//生成存储子目录,存储目录按文档id做md5后取前两位,共256个子目录,按每个目录存放4096个子目录计算,存储容量可达百万

$subdir = substr(md5($docid),0,2);

$g_pdfto = $g_pdfpath. L .$subdir. L .$docid;
func_print("pdfto : " . $g_pdfto);

func_pdf2swf($g_pdf2swf,$g_pdffrom,$g_pdfto);



//获取转换后的文档总页数.

$page=func_getpages($g_pdfto);

// 如果页数为0说明转换失败,将本次转换中间过程记录到/play/delayhandle/$docid.php文件,以便检测服务继续处理
if($page == 0)
{
    $temp_str  = "<?php";
    $temp_str .= "\r\n";

    $temp_str .= "ob_start();";

    $temp_str .= "\r\n";

    $temp_str .= "set_time_limit(600);";
    $temp_str .= "\r\n";

    $temp_str .= "define('L',DIRECTORY_SEPARATOR);";

    $temp_str .= "\r\n";

    $temp_str .= "require_once dirname(__FILE__) .L.'..'.L.'..'.L.'convert'.L.'convert_func.php';";

    $temp_str .= "\r\n";

    $temp_str .= "func_jod_convert('$g_jodpath',$g_ooffice_port,'$g_docfrom','$g_pdffrom');";

    $temp_str .= "\r\n";

    $temp_str .= "func_pdf2swf('$g_pdf2swf','$g_pdffrom','$g_pdfto');";
    $temp_str .= "\r\n";

    $temp_str .= "?>";
 

    file_put_contents ($g_phpto, $temp_str);
 

    func_print("pages is zero, put to delay handle file $g_phpto");
}

//将总页数入库(显示文档时需要总页数),fid是数据库中的文档id

$fid=isset($_REQUEST['fid'])?intval($_REQUEST['fid']):0;

//@unlink($g_docfrom);

if($page && $fid){

  require_once(WEBROOT. 'includes/config.class.php');

  require_once(WEBROOT. 'includes/dbcontrol.class.php');
  $Cfg = new Config();

  $Db = new DBControl($Cfg);

  $Db->Open();

  $Db->querySql("update {$Cfg->TB_PREFIX}file_info set I_page=$page where ID=$fid");

  $Db->close();

}


func_print("finish convert ...");

