<?php
ob_start();
set_time_limit(60);
@header("Content-Type: text/html; charset=gb2312");

$docid = $_REQUEST['docid'];
$totalpages =  $_REQUEST['totalpages'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>showdoc</title>
<link type="text/css" rel="stylesheet" href="../static/css/view.css" />
<script type="text/javascript" src="../static/js/doc.js"></script>
<script type="text/javascript" src="../static/js/view.js"></script>
</head>

<body>
<p class="rate" id="rateContainer" style="display:none">
</p>
<div id="readerContainer" class="mt" style="text-align:center;"></div>
<div class="clear"></div>
<script type="text/javascript">
function Reader()
{
	function B()
	{
		if(baidu.swf.getVersion())
		{
			return true
		}
		else
		{
			_id.innerHTML='<p class="ml">文档预览需要最新版本的Flash Player支持。</p><p class="ml">您尚未安装或版本过低，建议您：</p><a href="http://www.baidu.com/s?ie=gb2312&bs=flash+%CF%C2%D4%D8&sr=&z=&cl=3&f=8&wd=Flash+Player+%CF%C2%D4%D8&ct=0" target="_blank"><img src="http://box.zhangmen.baidu.com/images/setupflash.gif" height="39" width="273" /></a>';
			return false
		}
	}
	
	this.create=function(D,C)
	{
		baidu.swf.create({id:"reader",width:"717",height:"700",ver:"9.0.0",errorMessage:"Please download the newest flash player.",url:"../static/flash/reader.swf",bgColor:"#FFFFFF",wmode:"window",wmode:"transparent",allowfullscreen:"true",vars:{docurl:"../play",docid:"getswfx.php?docid=<?php echo $docid;?>&totalpages=<?php echo $totalpages;?>",fpn:"1",npn:"1"}},D);
		A(D)
	};
	
	function A(C)
	{
		baidu.on(C,"mousewheel",function(D){
			var F=D.wheelDelta;
			var E=-3;
			if(F<0)
			{
				E=3
			}
			baidu.swf.getMovie("reader").NS_IK_doMouseWheel(E);
			baidu.preventDefault(D)
		});
		if(window.addEventListener)
		{
			baidu.G(C).addEventListener("DOMMouseScroll",function(D)
			{
				var F=D.detail;
				var E=-3;
				if(F>0)
				{
					E=3
				}
				baidu.swf.getMovie("reader").NS_IK_doMouseWheel(E);
				baidu.preventDefault(D)
			},false)
		}
	}
}

var DOC_INFO={doc_id:"<?php echo $docid;?>",cid:"134",price:"0",value_average:"7"};
var _reader=new Reader();
_reader.create("readerContainer", "<?php echo $docid;?>");
baidu.each(["selfChangeCategory","adminChangeCategory","selfChangePrice"],
	function(B,A)
	{
		baidu.on(B,"click",
			function(C)
			{	
				login.check(baidu.proxy(view.changeDocInfo,B));
				baidu.preventDefault(C)
			})
	});
baidu.on("addToStore","click",function(A){window.open("http://cang.baidu.com/do/add?it="+encodeURIComponent(document.title)+"&iu="+encodeURIComponent(location.href)+"&tn=文库&fr=wk#nw=1","_s","scrollbars=no,width=600,height=450,right=75,top=20,status=no,resizable=yes");pop.show("提示",{url:"/static/html/empty.html",width:420,height:250});document.AddToStore.submit();baidu.preventDefault(A)});baidu.each(["downloadTop","downloadButton"],function(A){baidu.on(A,"click",function(B){log.send("down","download",{fr:"down"});login.check(view.download);baidu.preventDefault(B)})});var rate=new Rate("rateContainer");rate.create("7");if(G("kw")){G("kw").value=""};
</script>
</body>
</html>