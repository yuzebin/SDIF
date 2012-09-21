<?php
/*
 *every page header should like this:

 ob_start();
 set_time_limit(60);
 require_once dirname(__FILE__) . '/../convert/convert_func.php';
 func_init_page();
 $Cfg   = new UserConfig;
 $FLib  = new UserFunction($Cfg);
 @header("Content-Type: text/html; charset=utf-8");

 */
require_once("config.php");
function func_init_page()
{
	if(!defined('WEBROOT'))
	{	define('WEBROOT',$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
	}
	define('L',DIRECTORY_SEPARATOR);
//	require_once(WEBROOT. 'admin'. L. 'common'. L. 'UserConfig.class.php');
//	require_once(WEBROOT. 'admin'. L. 'common'. L. 'UserFunction.class.php');
}

function func_print($t_string)
{
    global $config;

	print "<br />";
    print "#".$t_string."#";
    print "<br />\r\n";
    file_put_contents( $config['absPath']."\showdoc\\log\\showdoc_".date("Y-m-d").".log","\r\n[".date("H:i:s")."] -> ".$t_string,FILE_APPEND);
}

/* func_index: doc indexing function
 * directory:
 *  htdocs
        |-admin
        |-showdoc
            |-convert
            |     |-convert.php
 * TODO: have not yet finish
 */
function func_index()
{
	//$m_cmdline = "java -jar ".$t_tikapath." -t ".$t_docfrom." > ".$t_txtto;
	func_print($m_cmdline);
	exec($m_cmdline);
}

/* func_extract: doc content extract
 * $t_tikapath : tika full path
 * $t_docfrom  : doc input
 * $t_txtto    : txt output
 * directory:
 *  htdocs
        |-admin
        |-showdoc
            |-convert
            |     |-convert.php
 */
function func_extract($t_tikapath,$t_docfrom,$t_txtto)
{
	$m_cmdline = "java -jar ".$t_tikapath." -t ".$t_docfrom." > ".$t_txtto;
	func_print($m_cmdline);
	exec($m_cmdline);
}

/* func_pdf2swf: pdf to swf
 * $t_pdf2swf  : pdf2swf full path
 * $t_pdffrom  : pdf input
 * $t_swftopath: swf output directory(NOT filename)
 * directory:
 *  htdocs
        |-admin
        |-showdoc
            |-convert
            |     |-convert.php
            |-play
                |-substr(md5(docid),0,2)
 */
function func_pdf2swf($t_pdf2swf,$t_pdffrom,$t_swftopath)
{
	if (!file_exists($t_swftopath)) mkdirs($t_swftopath);
	$m_cmdline = $t_pdf2swf." ".$t_pdffrom." -o ".$t_swftopath. L ."%_1.swf -f -T 9 -t -s storeallcharacters";
	func_print($m_cmdline);
	exec($m_cmdline);
}
function func_getpages($t_swftopath)
{
	if (!file_exists($t_swftopath)) return 0;
	$m_dirs = array();
	foreach(glob($t_swftopath. L ."*") as $d)
	{
		if(is_file($d))
		{
			$m_dirs[]=$d;
		}
	}
	$pages =  count($m_dirs);
	func_print($pages);
	return $pages;
}

/* func_convert_doc : doc/docx to pdf
 * $t_docfrom : doc input
 * $t_pdfto   : pdf output
 * directory:
 *  htdocs
        |-admin
        |-showdoc
            |-convert
            |     |-convert.php
            |-Zend

function func_convert_doc($t_docfrom,$t_pdfto)
{
	func_print($t_docfrom);
	func_print($t_pdfto);
	error_reporting(E_ALL | E_STRICT);
	set_include_path(realpath(dirname(__FILE__) . '/../'));
	require_once dirname(__FILE__) . '/Helper.php';
	require_once 'Zend/Loader/Autoloader.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();
	Zend_Locale::setDefault(Demos_Zend_Service_LiveDocx_Helper::LOCALE);
	$locale = new Zend_Locale(Zend_Locale::ZFDEFAULT);
	Zend_Registry::set('Zend_Locale', $locale);

	if (false === Demos_Zend_Service_LiveDocx_Helper::credentialsAvailable()) {
	    echo Demos_Zend_Service_LiveDocx_Helper::wrapLine(
	            Demos_Zend_Service_LiveDocx_Helper::credentialsHowTo()
	    );
	    exit();
	}

	$mailMerge = new Zend_Service_LiveDocx_MailMerge();
	$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
	          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);
	$mailMerge->setLocalTemplate($t_docfrom);
	$mailMerge->assign('dummyFieldName', 'dummyFieldValue'); 
	$mailMerge->createDocument();
	$document = $mailMerge->retrieveDocument('pdf');
	file_put_contents($t_pdfto, $document);
	unset($mailMerge);
}
 */
/* func_convert_doc : doc/docx to pdf
 * $t_jodpath : jodconvert.jar fullpath
 * $t_ooffice_port : openoffice port , default is 8100
 * $t_docfrom : doc input
 * $t_pdfto   : pdf output
 */
function func_jod_convert($t_jodpath,$t_ooffice_port,$t_docfrom,$t_pdfto)
{
	/*this function need ooffice run as a server
	 *./soffice -headless -accept="socket,host=127.0.0.1,port=8100;urp;" -nofirststartwizard
	 */
	//java -jar jodconverter-cli-2.2.2.jar -p 8100 -f pdf -f /Users/yuzebin/vbox/vbox_share/1234ppt.pdf /Users/yuzebin/vbox/vbox_share/1234.ppt
	//$m_cmdline = "java -jar ".$t_jodpath." -p " .$t_ooffice_port. " -f pdf -f ".$t_pdfto." ".$t_docfrom;
	$m_cmdline = "java -jar ".$t_jodpath." -p " .$t_ooffice_port. " ".$t_docfrom." ".$t_pdfto;
	func_print($m_cmdline);
	//func_chk_soffice();
	exec($m_cmdline);
}

function func_chk_soffice()
{
	global $config;

	$m_cmdline = "tasklist /FI \" Imagename eq soffice.exe\"";
	$m_result = exec($m_cmdline);
	if (strstr($m_result,"soffice") == false)
	{
        $m_cmdline = $config['officePath']."soffice.exe\" -headless -accept=\"socket,host=127.0.0.1,port=8100;urp;\" -nofirststartwizard";
	}
	echo $m_cmdline;
	exec ($m_cmdline);
}

function mkdirs($dir)
{
	if(!is_dir($dir))
	{
		if(!mkdirs(dirname($dir)))
		{
			return false;
		}
		if(!mkdir($dir,0777))
		{
			return false;
		}
	}
	return true;
}
