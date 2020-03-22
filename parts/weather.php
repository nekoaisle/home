<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
?>
<head>
<style type="text/css">
</style>

<script type="text/javascript" src="/js/dspResizeFunc.js"></script>
<script type="text/javascript">
jQuery(function($){
	// ウィンドウがリサイズされた時にサイズを変更
	$(window).dspResizeFunc({
		delay: 5000,
		func : function(){
			var width = $(window.frameElement).innerWidth() - 8;
			if ( up.width != width )
			{
				var iframe = window.frameElement;
				iframe.src = iframe.src;
			}
		},
	});
});
</script>
</head>

<body>
<!--
//====================================================================
// Widget ピンポイント天気予報
//====================================================================
-->
<body>
<div id="ppwj_62bb5698-3432-4bf7-a3d2-0c631373c059"></div>
<script type="text/javascript">
var up = {
	  gid            : "ppwj_62bb5698-3432-4bf7-a3d2-0c631373c059"
	, width          : $(window.frameElement).innerWidth() - 8
	, height         : 270
	, fontSize       : 12
	, lcolor         : "#208dc3"
	, hcolor         : "#cbe6f3"
	, bgcolor        : "#ffffff"
	, label          : true
	, hWidth         : 0
	, name           : true
	, sname          : false
	, unit           : true
	, daysMax        : 8
	, temp           : true
	, detailUntil    : 1
	, hourIndexOfADay: 3
	, tempUnit       : "c"
	, month          : true
	, dayOfWeek      : true
	, humidity       : true
	, icon           : "icon_32_a"
	, prec           : false
	, coRain         : true
	, wind           : false
	, bStyle         : "-"
	, bWidth         : 1
	, bColor         : "#208dc3"
	, bRadius        : false
	, bShadow        : false
	, bPadding       : 4
	, rcode1         : 709
	, rcode2         : 0
}
</script>
<script type="text/javascript" src="http://weather.tmyymmt.net/bp1/js/bp1.js" charset="UTF-8"></script>
</body>
<?php
}
?>