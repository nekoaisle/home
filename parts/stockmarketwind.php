<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	株価パーツ
//	stock market wind 
//	http://stockmarketwind.com/
//
//	専用設定
//	'stock code' => [銘柄コード]
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	$strStockCode = $aryConfig['stock code'];
?>
<head>
<style type="text/css">
</style>

<script type="text/javascript">
jQuery(function($){
	// ロードが完了した
	$(window).load( function () {
		// 要素のサイズを調整
		$("div.bland").css("padding", "0px" );
		var h = $("div.bland").innerHeight();
		$("div.bland").css("height", h+"px" );
		$("#rssmicle_container").css("height", h+"px" );
		$("#rssmikle_frame").css("height", h+"px" );

		// iframeの高さを調整
		var h = $("body").height();
		$(window.frameElement).css("height",h);
	});
});
</script>
</head>

<body>
<!-- start stock market wind code -->
<div id="rssmikle_preview" style="overflow-y:visible;"></div>
<script type="text/javascript">
<!--
// width は小さい値を指定してもはみ出した部分は表示される
rssmikle_frame_width="100;";
rssmikle_frame_height="100";
rssmikle_target="_blank";
rssmikle_font_size="12";
rssmikle_border="off";
rssmikle_title="undefined";
rssmikle_title_bgcolor="#0066FF";
rssmikle_title_color="#FFFFFF";
rssmikle_title_bgimage="";
rssmikle_title_text="私の株価情報";
rssmikle_stock_color="666666";
rssmikle_stock_index="";
rssmikle_stock_type="0";
rssmikle_stock_code="<?php echo $strStockCode; ?>";
rssmikle_stock_summary="";
//-->
</script>
<script type="text/javascript" src="http://stockmarketwind.com/js/prototype.js"  charset="utf-8"></script>
<script type="text/javascript" src="http://stockmarketwind.com/js/rssmikle.js"  charset="utf-8"></script>
<script type="text/javascript" src="http://stockmarketwind.com/js/controll.js"  charset="utf-8"></script>
<div style="font-size:10px; text-align:right;">
<a href="http://stockmarketwind.com/" target="_blank" style="color:#CCCCCC;">株価ブログパーツ</a>
<!--利用規約に従ってページ内に必ずリンクを表示してください-->
</div>
<!-- end stock market wind code -->
</body>
<?php
}
?>