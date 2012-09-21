<?php
/* func_add_index: doc indexing function
 * $t_curlpath : curl fullpath
 * $t_url : solr update url ,just like : http://192.168.1.100:8000/solr/update
 * $t_txtto : txt file fullpath
 * $t_docid : docid
 */
function func_add_index($t_jarpath,$t_url,$t_txtto,$t_docid)
{
	func_gen_index_file($t_txtto,$t_docid);
	func_jar_index($t_jarpath,$t_url,$t_txtto);
}

/* func_curl_index: doc indexing function
 * $t_url : solr update url ,just like : http://192.168.1.100:8000/solr/update
 * $t_txtto : txt file fullpath
 */
function func_curl_index($t_curlpath,$t_url,$t_txtto)
{
	/* add/update index method:
	 * curl $URL --data-binary @$f -H 'Content-type:application/xml'
	 */
	$m_cmdline = $t_curlpath. " " .$t_url. " --data-binary @" .$t_txtto;
	$m_cmdline .=  " -H 'Content-type:application/xml'";
	func_print($m_cmdline);
	//exec($m_cmdline);
}

/* func_jar_index: doc indexing function
 * $t_url : solr update url ,just like : http://192.168.1.100:8000/solr/update
 * $t_txtto : txt file fullpath
 */
function func_jar_index($t_jarpath,$t_url,$t_txtto)
{
	/* add/update index method:
	 * java -Durl=http://192.168.1.100:8000/solr/update -Ddata=files -jar c:\EnableDocs\edoc\tool\solr\bin\post.jar c:\EnableDocs\edoc\solr\t.xml
	 */
	//$m_cmdline = $t_curlpath. " " .$t_url. " --data-binary @" .$t_txtto;
	$m_cmdline = "java -Durl=". $t_url ." -Ddata=files -Dcommit=yes -jar ". $t_jarpath ." ". $t_txtto;
	func_print($m_cmdline);
	exec($m_cmdline);
}



/* func_commit_index: doc indexing function
 * $t_url : solr update url ,just like : http://192.168.1.100:8000/solr/update
 */
function func_commit_index($t_curlpath,$t_url)
{
	/* add/update index method:
	 * curl $URL --data-binary '<commit/>' -H 'Content-type:application/xml'
	 */
	$m_cmdline = $t_curlpath. " " .$t_url. " --data-binary '<commit/>'";
	$m_cmdline .=  " -H 'Content-type:application/xml'";
	func_print($m_cmdline);
	//exec($m_cmdline);
}

/* func_gen_index_file :
 * $t_txtto : txt file fullpath
 * $t_docid : docid
 * $t_docname : docname default must send " "
 *
 * index file format like below...
 *   '<add><doc><field name="id">'
 *     SOLR1000
 *   '</field><field name="name">'
 *     Solr, the Enterprise Search Server
 *   '</field><field name="content">'
 *     Apache Software Foundation
 *   '</field></doc></add>'
 */
function func_gen_index_file($t_txtto,$t_docid)
{
	$t_file = file_get_contents($t_txtto);
     //$t_file = iconv("gbk","utf-8//IGNORE",$t_file);
     //echo $t_file;
	$t_xml  = '<?xml version="1.0" encoding="UTF-8" ?><add><doc><field name="id">' ;
	$t_xml .= $t_docid;
	$t_xml .= '</field><field name="content">' .stripslashes(strip_tags($t_file));
	$t_xml .= '</field></doc></add>';

  file_put_contents ($t_txtto, $t_xml);
}